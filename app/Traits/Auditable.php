<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(function (Model $model) {
            static::writeAudit($model, 'created', null, $model->auditPayload());
        });

        static::updated(function (Model $model) {
            $dirty = $model->getDirty();
            if ($dirty === []) {
                return;
            }
            $old = [];
            $new = [];
            foreach (array_keys($dirty) as $key) {
                if (in_array($key, $model->auditHidden(), true)) {
                    continue;
                }
                $old[$key] = $model->getOriginal($key);
                $new[$key] = $model->getAttribute($key);
            }
            if ($old === [] && $new === []) {
                return;
            }
            static::writeAudit($model, 'updated', $old, $new);
        });

        static::deleted(function (Model $model) {
            static::writeAudit($model, 'deleted', $model->auditPayload(), null);
        });
    }

    protected static function writeAudit(Model $model, string $event, ?array $old, ?array $new): void
    {
        try {
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => class_basename($model) . '.' . $event,
                'auditable_type' => $model->getMorphClass(),
                'auditable_id' => $model->getKey(),
                'old_values' => $old,
                'new_values' => $new,
                'ip_address' => Request::ip(),
                'user_agent' => substr((string) Request::userAgent(), 0, 500),
            ]);
        } catch (\Throwable $e) {
            report($e);
        }
    }

    /** Campos sensibles que no van al log */
    public function auditHidden(): array
    {
        return ['password', 'remember_token'];
    }

    public function auditPayload(): array
    {
        $attrs = $model = $this->getAttributes();
        foreach ($this->auditHidden() as $h) {
            unset($attrs[$h]);
        }
        // No guardar paths de documentos completos en created si son muy largos — ok
        return $attrs;
    }
}
