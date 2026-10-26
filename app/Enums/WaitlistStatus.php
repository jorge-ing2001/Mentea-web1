<?php

namespace App\Enums;

enum WaitlistStatus: string
{
    case Waiting = 'waiting';
    case MatchFound = 'match_found';
    case Confirmed = 'confirmed';
    case Expired = 'expired';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Waiting => 'En espera',
            self::MatchFound => 'Disponibilidad encontrada',
            self::Confirmed => 'Cita confirmada',
            self::Expired => 'Solicitud vencida',
            self::Cancelled => 'Cancelada',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Waiting => 'amber',
            self::MatchFound => 'blue',
            self::Confirmed => 'green',
            self::Expired => 'red',
            self::Cancelled => 'gray',
        };
    }

    public function emoji(): string
    {
        return match ($this) {
            self::Waiting => '🟡',
            self::MatchFound => '🔵',
            self::Confirmed => '🟢',
            self::Expired => '🔴',
            self::Cancelled => '⚪',
        };
    }

    public function isActive(): bool
    {
        return in_array($this, [self::Waiting, self::MatchFound], true);
    }
}
