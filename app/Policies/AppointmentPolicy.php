<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;

class AppointmentPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Appointment $appointment): bool
    {
        if ($user->isSuperAdmin() || $user->isAdmin()) {
            return true;
        }
        if ($user->isPatient()) {
            return $appointment->patient_id === $user->id;
        }
        if ($user->isProfessional()) {
            return $appointment->professional?->user_id === $user->id;
        }
        return false;
    }

    public function create(User $user): bool
    {
        return $user->isPatient() || $user->isAdmin();
    }

    public function update(User $user, Appointment $appointment): bool
    {
        if ($user->isSuperAdmin() || $user->isAdmin()) {
            return true;
        }
        if ($user->isPatient()) {
            return $appointment->patient_id === $user->id && $appointment->canBeModifiedByPatient();
        }
        return false;
    }

    public function cancel(User $user, Appointment $appointment): bool
    {
        if ($user->isAdmin()) {
            return $appointment->status->canBeCancelled();
        }
        if ($user->isPatient()) {
            return $appointment->patient_id === $user->id && $appointment->canBeCancelledByPatient();
        }
        return false;
    }

    public function reschedule(User $user, Appointment $appointment): bool
    {
        if ($user->isAdmin()) {
            return $appointment->status->canBeModified();
        }
        if ($user->isPatient()) {
            return $appointment->patient_id === $user->id && $appointment->canBeModifiedByPatient();
        }
        return false;
    }

    public function delete(User $user, Appointment $appointment): bool
    {
        return $user->isAdmin();
    }

    public function viewClinicalNotes(User $user, Appointment $appointment): bool
    {
        if ($user->isSuperAdmin() || $user->isAdmin()) {
            return true;
        }
        if ($user->isProfessional()) {
            $profile = $user->professionalProfile;
            if (! $profile) {
                return false;
            }
            return (int) $appointment->professional_id === (int) $profile->id;
        }
        return false;
    }

    public function updateClinicalNotes(User $user, Appointment $appointment): bool
    {
        return $this->viewClinicalNotes($user, $appointment);
    }
}
