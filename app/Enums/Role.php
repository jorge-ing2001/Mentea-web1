<?php

namespace App\Enums;

enum Role: string
{
    case SuperAdmin = 'super_admin';
    case Admin = 'admin';
    case Professional = 'professional';
    case Patient = 'patient';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super usuario',
            self::Admin => 'Administrador',
            self::Professional => 'Profesional',
            self::Patient => 'Paciente',
        };
    }
}
