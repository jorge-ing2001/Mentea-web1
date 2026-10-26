<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Registra en auditoría las acciones de escritura (POST/PUT/PATCH/DELETE)
 * realizadas por usuarios autenticados sobre la plataforma.
 */
class AuditRequestMiddleware
{
    /** Rutas o prefijos que no se auditan (ruido / sensibilidad) */
    private array $except = [
        'login',
        'logout',
        'register',
        'password*',
        'sanctum/*',
        'livewire/*',
        '_ignition/*',
        'up',
    ];

    private array $sensitiveKeys = [
        'password',
        'password_confirmation',
        'current_password',
        'token',
        '_token',
        'remember',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        try {
            if ($this->shouldAudit($request, $response)) {
                $this->writeLog($request, $response);
            }
        } catch (\Throwable $e) {
            // Nunca romper la petición por fallo de auditoría
            report($e);
        }

        return $response;
    }

    private function shouldAudit(Request $request, Response $response): bool
    {
        if (! $request->user()) {
            return false;
        }

        if (! in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            return false;
        }

        // Solo respuestas exitosas o redirecciones típicas de formularios
        $status = $response->getStatusCode();
        if ($status >= 500) {
            return false;
        }

        foreach ($this->except as $pattern) {
            if ($request->is($pattern)) {
                return false;
            }
        }

        return true;
    }

    private function writeLog(Request $request, Response $response): void
    {
        $route = $request->route();
        $routeName = $route?->getName() ?? '';
        $action = $request->method() . ' ' . ($routeName ?: '/'.$request->path());

        $payload = $this->sanitize($request->except($this->sensitiveKeys));

        // Limitar tamaño del payload
        $encoded = json_encode($payload, JSON_UNESCAPED_UNICODE);
        if ($encoded !== false && strlen($encoded) > 8000) {
            $payload = ['_truncated' => true, 'keys' => array_keys($payload)];
        }

        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => $action,
            'auditable_type' => null,
            'auditable_id' => null,
            'old_values' => null,
            'new_values' => [
                'route' => $routeName,
                'path' => $request->path(),
                'method' => $request->method(),
                'status' => $response->getStatusCode(),
                'input' => $payload,
            ],
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
        ]);
    }

    private function sanitize(array $data): array
    {
        $clean = [];
        foreach ($data as $key => $value) {
            $lower = strtolower((string) $key);
            if (in_array($lower, $this->sensitiveKeys, true) || str_contains($lower, 'password')) {
                $clean[$key] = '[oculto]';
                continue;
            }
            if (is_array($value)) {
                $clean[$key] = $this->sanitize($value);
                continue;
            }
            if (is_object($value)) {
                continue;
            }
            // No guardar binarios de archivos
            if ($value instanceof \Illuminate\Http\UploadedFile) {
                $clean[$key] = [
                    'file' => $value->getClientOriginalName(),
                    'size' => $value->getSize(),
                    'mime' => $value->getMimeType(),
                ];
                continue;
            }
            $clean[$key] = $value;
        }

        return $clean;
    }
}
