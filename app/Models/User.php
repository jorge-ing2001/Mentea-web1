<?php

namespace App\Models;

use App\Enums\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\Auditable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Auditable, HasFactory, HasRoles, Notifiable, SoftDeletes;

    protected $fillable = [
        'document_number', 'name', 'email', 'password', 'phone',
        'role', 'photo', 'is_active', 'last_login_at',
        'document_type', 'eps', 'emergency_contact_name', 'emergency_contact_phone',
        'consent_informed_at', 'consent_data_at',
        'cancellation_count', 'no_show_count', 'booking_blocked_until',
        'onboarding_completed_at', 'preferred_theme',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
            'consent_informed_at' => 'datetime',
            'consent_data_at' => 'datetime',
            'booking_blocked_until' => 'datetime',
            'onboarding_completed_at' => 'datetime',
            'role' => Role::class,
        ];
    }

    /**
     * Sincroniza columna legacy `role` con Spatie (un rol principal).
     */
    public function syncPrimaryRole(string $roleName): void
    {
        $this->syncRoles([$roleName]);
        if (Role::tryFrom($roleName)) {
            $this->forceFill(['role' => $roleName])->save();
        }
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin') || $this->role === Role::SuperAdmin;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin') || $this->role === Role::Admin;
    }

    public function isStaffAdmin(): bool
    {
        return $this->isSuperAdmin() || $this->isAdmin();
    }

    public function isProfessional(): bool
    {
        return $this->hasRole('professional') || $this->role === Role::Professional;
    }

    public function isPatient(): bool
    {
        return $this->hasRole('patient') || $this->role === Role::Patient;
    }

    public function needsOnboarding(): bool
    {
        return $this->isPatient() && $this->onboarding_completed_at === null;
    }

    public function isBookingBlocked(): bool
    {
        return $this->booking_blocked_until && $this->booking_blocked_until->isFuture();
    }

    public function hasInformedConsent(): bool
    {
        return $this->consent_informed_at !== null;
    }

    public function professionalProfile(): HasOne
    {
        return $this->hasOne(Professional::class);
    }

    public function appointmentsAsPatient(): HasMany
    {
        return $this->hasMany(Appointment::class, 'patient_id');
    }

    public function waitlistEntries(): HasMany
    {
        return $this->hasMany(WaitlistEntry::class, 'patient_id');
    }
}
