<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Sistema (super)
            'settings.manage',
            'tickets.manage',
            'audit.view',
            'roles.manage',
            'users.manage',
            // Clínica (admin)
            'professionals.manage',
            'schedules.manage',
            'appointments.manage',
            'waitlist.manage',
            'calendar.view',
            // Profesional
            'appointments.attend',
            'notes.write',
            // Paciente
            'appointments.book',
            'waitlist.join',
            'tickets.create',
            'surveys.respond',
        ];

        foreach ($permissions as $name) {
            Permission::findOrCreate($name, 'web');
        }

        $super = Role::findOrCreate('super_admin', 'web');
        $super->syncPermissions($permissions); // todos

        $admin = Role::findOrCreate('admin', 'web');
        $admin->syncPermissions([
            'professionals.manage',
            'schedules.manage',
            'appointments.manage',
            'waitlist.manage',
            'calendar.view',
            'tickets.manage',
        ]);

        $professional = Role::findOrCreate('professional', 'web');
        $professional->syncPermissions([
            'appointments.attend',
            'notes.write',
            'calendar.view',
        ]);

        $patient = Role::findOrCreate('patient', 'web');
        $patient->syncPermissions([
            'appointments.book',
            'waitlist.join',
            'tickets.create',
            'surveys.respond',
        ]);

        // Asignar Spatie role según columna role de usuarios existentes
        User::query()->each(function (User $user) {
            $name = $user->role?->value ?? 'patient';
            if (! $user->hasRole($name)) {
                $user->syncRoles([$name]);
            }
        });
    }
}
