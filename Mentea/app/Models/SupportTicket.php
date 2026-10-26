<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportTicket extends Model
{
    public const TYPE_SUPPORT = 'support';
    public const TYPE_PLATFORM_EVENT = 'platform_event';

    protected $fillable = [
        'user_id', 'type', 'category', 'event_date',
        'subject', 'message', 'status', 'priority',
        'assigned_to', 'resolved_at', 'admin_notes',
    ];

    protected function casts(): array
    {
        return [
            'resolved_at' => 'datetime',
            'event_date' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function isPlatformEvent(): bool
    {
        return $this->type === self::TYPE_PLATFORM_EVENT;
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            self::TYPE_PLATFORM_EVENT => 'Diligenciamiento / evento',
            default => 'Soporte usuario',
        };
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'open' => 'Abierto',
            'in_progress' => 'En progreso',
            'resolved' => 'Resuelto',
            'closed' => 'Cerrado',
            default => $this->status,
        };
    }

    public function priorityLabel(): string
    {
        return match ($this->priority) {
            'low' => 'Baja',
            'medium' => 'Media',
            'high' => 'Alta',
            'urgent' => 'Urgente',
            default => $this->priority,
        };
    }

    public static function categories(): array
    {
        return [
            'incidencia' => 'Incidencia técnica',
            'mantenimiento' => 'Mantenimiento',
            'cambio_config' => 'Cambio de configuración',
            'seguridad' => 'Seguridad',
            'capacitacion' => 'Capacitación / aviso',
            'otro' => 'Otro evento',
            'consulta' => 'Consulta de usuario',
            'queja' => 'Queja / reclamo',
        ];
    }
}
