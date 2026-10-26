```php
<?php

namespace App\Providers;

use App\Models\Appointment;
use App\Models\Professional;
use App\Policies\AppointmentPolicy;
use App\Policies\ProfessionalPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Forzar HTTPS en producción
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Permisos del superadministrador
        Gate::before(function ($user, $ability) {
            return method_exists($user, 'hasRole')
                && $user->hasRole('super_admin')
                    ? true
                    : null;
        });

        // Políticas de autorización
        Gate::policy(Appointment::class, AppointmentPolicy::class);
        Gate::policy(Professional::class, ProfessionalPolicy::class);
    }
}
```
