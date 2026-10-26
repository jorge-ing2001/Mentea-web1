<?php
namespace App\Enums;
enum Modality: string
{
    case InPerson = 'presencial';
    case Virtual = 'virtual';
    public function label(): string
    {
        return match ($this) {
            self::InPerson => 'Presencial',
            self::Virtual => 'Virtual',
        };
    }
}
