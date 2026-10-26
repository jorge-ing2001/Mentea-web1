<?php
namespace App\Enums;
enum AppointmentStatus: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Cancelled = 'cancelled';
    case Completed = 'completed';
    case NoShow = 'no_show';
    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pendiente',
            self::Confirmed => 'Confirmada',
            self::Cancelled => 'Cancelada',
            self::Completed => 'Completada',
            self::NoShow => 'No asistió',
        };
    }
    public function color(): string
    {
        return match ($this) {
            self::Pending => 'yellow',
            self::Confirmed => 'blue',
            self::Cancelled => 'red',
            self::Completed => 'green',
            self::NoShow => 'gray',
        };
    }
    public function canBeCancelled(): bool
    {
        return in_array($this, [self::Pending, self::Confirmed], true);
    }
    public function canBeModified(): bool
    {
        return in_array($this, [self::Pending, self::Confirmed], true);
    }
}
