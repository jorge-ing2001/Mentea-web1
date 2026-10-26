<?php

namespace App\Models;

use App\Enums\AppointmentStatus;
use App\Enums\Modality;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class Appointment extends Model
{
    use Auditable, HasFactory, SoftDeletes;

    protected $fillable = [
        'patient_id', 'professional_id', 'date', 'time', 'modality', 'status',
        'reason', 'meet_link', 'cancelled_at', 'cancellation_reason', 'notes',
        'order_document', 'authorization_document',
        'clinical_notes', 'clinical_notes_updated_at', 'clinical_notes_by',
        'reminder_24h_sent_at', 'reminder_2h_sent_at', 'marked_no_show_at',
        'confirm_token', 'patient_confirmed_at', 'diagnosis_code', 'diagnosis_label',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'time' => 'datetime:H:i',
            'modality' => Modality::class,
            'status' => AppointmentStatus::class,
            'cancelled_at' => 'datetime',
            'clinical_notes_updated_at' => 'datetime',
            'reminder_24h_sent_at' => 'datetime',
            'reminder_2h_sent_at' => 'datetime',
            'marked_no_show_at' => 'datetime',
            'patient_confirmed_at' => 'datetime',
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

    public function evolutions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ClinicalEvolution::class);
    }

    public function survey(): HasOne
    {
        return $this->hasOne(Survey::class);
    }

    public function canBeCancelledByPatient(): bool
    {
        if (! $this->status->canBeCancelled()) {
            return false;
        }
        $dt = Carbon::parse($this->date->format('Y-m-d') . ' ' . $this->time->format('H:i:s'));
        return now()->diffInDays($dt, false) >= 5;
    }

    public function canBeModifiedByPatient(): bool
    {
        if (! $this->status->canBeModified()) {
            return false;
        }
        return $this->created_at->diffInHours(now()) <= 24;
    }

    public function hasOrderDocument(): bool
    {
        return filled($this->order_document) && Storage::disk('local')->exists($this->order_document);
    }

    public function hasAuthorizationDocument(): bool
    {
        return filled($this->authorization_document) && Storage::disk('local')->exists($this->authorization_document);
    }

    public function orderDocumentUrl(): ?string
    {
        return $this->order_document ? route('documents.view', ['appointment' => $this, 'type' => 'order']) : null;
    }

    public function authorizationDocumentUrl(): ?string
    {
        return $this->authorization_document ? route('documents.view', ['appointment' => $this, 'type' => 'authorization']) : null;
    }
}
