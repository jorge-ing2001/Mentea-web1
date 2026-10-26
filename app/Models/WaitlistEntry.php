<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Traits\Auditable;

use App\Enums\Modality;
use App\Enums\WaitlistStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WaitlistEntry extends Model
{
    use Auditable, HasFactory;
    protected $fillable = [
        'patient_id', 'professional_id', 'specialty',
        'date_from', 'date_to', 'time_from', 'time_to', 'modality',
        'status', 'notes',
        'offered_professional_id', 'offered_date', 'offered_time', 'offered_modality',
        'offered_at', 'offer_expires_at',
        'confirmed_at', 'cancelled_at', 'expired_at',
        'assigned_at', 'assigned_appointment_id',
        'preferred_date', // legacy
    ];

    protected function casts(): array
    {
        return [
            'date_from' => 'date',
            'date_to' => 'date',
            'preferred_date' => 'date',
            'offered_date' => 'date',
            'status' => WaitlistStatus::class,
            'modality' => Modality::class,
            'offered_modality' => Modality::class,
            'offered_at' => 'datetime',
            'offer_expires_at' => 'datetime',
            'confirmed_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'expired_at' => 'datetime',
            'assigned_at' => 'datetime',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function professional(): BelongsTo
    {
        return $this->belongsTo(Professional::class);
    }

    public function offeredProfessional(): BelongsTo
    {
        return $this->belongsTo(Professional::class, 'offered_professional_id');
    }

    public function assignedAppointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class, 'assigned_appointment_id');
    }

    public function preferenceSummary(): string
    {
        $parts = [];
        if ($this->date_from && $this->date_to) {
            $parts[] = $this->date_from->format('d/m') . '–' . $this->date_to->format('d/m/Y');
        }
        if ($this->time_from && $this->time_to) {
            $parts[] = substr((string) $this->time_from, 0, 5) . '–' . substr((string) $this->time_to, 0, 5);
        }
        $parts[] = $this->professional_id
            ? ($this->professional?->name ?? 'Profesional #' . $this->professional_id)
            : 'Cualquier profesional';
        if ($this->modality) {
            $parts[] = $this->modality->label();
        }
        return implode(' / ', $parts);
    }

    public function isOfferActive(): bool
    {
        return $this->status === WaitlistStatus::MatchFound
            && $this->offer_expires_at
            && $this->offer_expires_at->isFuture();
    }

    public function secondsRemainingOnOffer(): int
    {
        if (! $this->isOfferActive()) {
            return 0;
        }
        return max(0, (int) now()->diffInSeconds($this->offer_expires_at, false));
    }

    public function matchesSlot(Professional $professional, Carbon $date, string $time, ?Modality $modality = null): bool
    {
        if ($this->specialty && strcasecmp($this->specialty, $professional->specialty) !== 0) {
            return false;
        }
        if ($this->professional_id && (int) $this->professional_id !== (int) $professional->id) {
            return false;
        }
        if ($this->date_from && $date->lt($this->date_from->startOfDay())) {
            return false;
        }
        if ($this->date_to && $date->gt($this->date_to->endOfDay())) {
            return false;
        }
        $timeNorm = Carbon::parse($time)->format('H:i');
        if ($this->time_from) {
            $from = Carbon::parse($this->time_from)->format('H:i');
            if ($timeNorm < $from) {
                return false;
            }
        }
        if ($this->time_to) {
            $to = Carbon::parse($this->time_to)->format('H:i');
            if ($timeNorm > $to) {
                return false;
            }
        }
        if ($this->modality && $modality && $this->modality !== $modality) {
            return false;
        }
        return true;
    }
}
