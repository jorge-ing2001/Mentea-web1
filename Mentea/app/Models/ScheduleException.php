<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduleException extends Model
{
    use Auditable;

    protected $fillable = [
        'professional_id', 'date', 'time_from', 'time_to', 'type', 'reason',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'time_from' => 'datetime:H:i',
            'time_to' => 'datetime:H:i',
        ];
    }

    public function professional(): BelongsTo
    {
        return $this->belongsTo(Professional::class);
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            'holiday' => 'Festivo / clínica cerrada',
            'absence' => 'Ausencia del profesional',
            default => 'Bloqueo puntual',
        };
    }

    public function isFullDay(): bool
    {
        return $this->time_from === null && $this->time_to === null;
    }
}
