```php
<?php

use App\Http\Middleware\AuditRequestMiddleware;
use App\Http\Middleware\ForceHttps;
use App\Http\Middleware\EnsureUserHasRole;
use App\Http\Middleware\EnsureUserIsActive;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Alias de middleware personalizados
        $middleware->alias([
            'role' => EnsureUserHasRole::class,
            'active' => EnsureUserIsActive::class,
            'audit' => AuditRequestMiddleware::class,
        ]);

        // Auditoría automática y redirección HTTPS en rutas web
        $middleware->appendToGroup('web', [
            AuditRequestMiddleware::class,
            ForceHttps::class,
        ]);

        // Aplicar validación de usuario activo a las rutas autenticadas
        $middleware->appendToGroup('auth', [
            EnsureUserIsActive::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) =>
                $request->is('api/*') || $request->expectsJson(),
        );
    })
    ->create();
```
