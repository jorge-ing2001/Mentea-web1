<?php

namespace App\Services;

use App\Enums\AppointmentStatus;
use App\Enums\Modality;
use App\Models\Appointment;
use App\Models\AuditLog;
use App\Models\Professional;
use App\Models\User;
use App\Notifications\AppointmentBookedNotification;
use App\Notifications\AppointmentCancelledNotification;
use App\Notifications\AppointmentRescheduledNotification;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class AppointmentService
{
    public function __construct(
        private AvailabilityService $availability,
        private GoogleMeetService $googleMeet,
        private WaitlistService $waitlist,
    ) {}

    /**
     * Comprueba que el paciente no tenga otra cita activa en la misma fecha y hora.
     */
    public function assertPatientHasNoConflict(User $patient, string $date, string $time, ?int $ignoreAppointmentId = null): void
    {
        $timeNormalized = \Carbon\Carbon::parse($time)->format('H:i:s');

        $query = Appointment::query()
            ->where('patient_id', $patient->id)
            ->whereDate('date', $date)
            ->whereIn('status', [
                AppointmentStatus::Pending->value,
                AppointmentStatus::Confirmed->value,
            ])
            ->where(function ($q) use ($timeNormalized, $time) {
                $q->whereTime('time', $timeNormalized)
                  ->orWhereTime('time', \Carbon\Carbon::parse($time)->format('H:i'));
            });

        if ($ignoreAppointmentId) {
            $query->where('id', '!=', $ignoreAppointmentId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'time' => 'Ya tienes una cita programada en esa fecha y hora. No puedes agendar dos citas al mismo tiempo (presencial o virtual).',
            ]);
        }
    }

    public function book(
        User $patient,
        Professional $professional,
        string $date,
        string $time,
        Modality $modality,
        ?string $reason = null,
        ?UploadedFile $orderDocument = null,
        ?UploadedFile $authorizationDocument = null,
        bool $skipAvailabilityCheck = false,
    ): Appointment {
        if ($patient->isBookingBlocked()) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'date' => 'No puedes agendar temporalmente (límites de cancelaciones o inasistencias). Contacta soporte o espera hasta ' .
                    $patient->booking_blocked_until?->format('d/m/Y H:i'),
            ]);
        }
        if (! $patient->hasInformedConsent()) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'consent' => 'Debes aceptar el consentimiento informado en tu perfil antes de agendar.',
            ]);
        }

        if (! $professional->is_available) {
            throw ValidationException::withMessages([
                'professional_id' => 'Este profesional no está disponible para nuevas citas.',
            ]);
        }

        $dateObj = \Carbon\Carbon::parse($date);

        if (! $skipAvailabilityCheck) {
            if (! $this->availability->isSlotAvailable($professional, $dateObj, $time)) {
                throw ValidationException::withMessages([
                    'time' => 'El horario seleccionado ya no está disponible. Elige otro.',
                ]);
            }
        } else {
            // Confirmar oferta de lista de espera: solo validar que no haya cita real
            if (! $this->availability->isSlotFreeOfAppointments($professional, $dateObj, $time)) {
                throw ValidationException::withMessages([
                    'time' => 'El horario ya fue ocupado. Intenta otra disponibilidad.',
                ]);
            }
        }

        $this->assertPatientHasNoConflict($patient, $date, $time);

        return DB::transaction(function () use ($patient, $professional, $date, $time, $modality, $reason, $orderDocument, $authorizationDocument) {
            $appointment = Appointment::create([
                'patient_id' => $patient->id,
                'professional_id' => $professional->id,
                'date' => $date,
                'time' => $time,
                'modality' => $modality,
                'status' => AppointmentStatus::Pending,
                'reason' => $reason,
            ]);

            $this->storeDocuments($appointment, $orderDocument, $authorizationDocument);
            $appointment->forceFill([
                'confirm_token' => \Illuminate\Support\Str::random(48),
            ])->saveQuietly();

            if ($modality === Modality::Virtual) {
                try {
                    $link = $this->googleMeet->createMeeting($appointment);
                    if ($link) {
                        $appointment->update(['meet_link' => $link]);
                    }
                } catch (\Throwable $e) {
                    report($e);
                }
            }

            $this->log($patient, 'appointment.created', $appointment);

            try {
                $patient->notify(new AppointmentBookedNotification($appointment->fresh(['professional'])));
            } catch (\Throwable $e) {
                report($e);
            }

            return $appointment->fresh(['professional', 'patient']);
        });
    }

    public function storeDocuments(
        Appointment $appointment,
        ?UploadedFile $orderDocument,
        ?UploadedFile $authorizationDocument,
    ): void {
        $dir = 'appointments/' . $appointment->id;
        $updates = [];

        if ($orderDocument) {
            $updates['order_document'] = $orderDocument->store($dir, 'local');
        }
        if ($authorizationDocument) {
            $updates['authorization_document'] = $authorizationDocument->store($dir, 'local');
        }

        if ($updates) {
            $appointment->update($updates);
        }
    }

    public function reschedule(
        Appointment $appointment,
        User $actor,
        string $date,
        string $time,
        ?Modality $modality = null,
    ): Appointment {
        $isAdmin = $actor->isAdmin();
        $isOwner = $appointment->patient_id === $actor->id;

        if (! $isAdmin && ! $isOwner) {
            throw ValidationException::withMessages(['appointment' => 'No tienes permiso para reprogramar esta cita.']);
        }

        if (! $isAdmin && ! $appointment->canBeModifiedByPatient()) {
            throw ValidationException::withMessages([
                'appointment' => 'Solo puedes reprogramar en las primeras 24 horas desde que se agendó, y la cita debe estar pendiente o confirmada.',
            ]);
        }

        if (! $appointment->status->canBeModified()) {
            throw ValidationException::withMessages(['appointment' => 'Esta cita no se puede reprogramar por su estado actual.']);
        }

        $professional = $appointment->professional;
        $dateObj = \Carbon\Carbon::parse($date);
        $slots = $this->availability->getAvailableSlots($professional, $dateObj);
        $currentSame = $appointment->date->format('Y-m-d') === $date
            && \Carbon\Carbon::parse($appointment->time)->format('H:i') === $time;

        if (! $currentSame && ! $slots->contains($time)) {
            throw ValidationException::withMessages([
                'time' => 'El nuevo horario no está disponible.',
            ]);
        }

        $this->assertPatientHasNoConflict(
            $appointment->patient,
            $date,
            $time,
            $appointment->id
        );

        $old = [
            'date' => $appointment->date->format('Y-m-d'),
            'time' => \Carbon\Carbon::parse($appointment->time)->format('H:i'),
        ];

        return DB::transaction(function () use ($appointment, $actor, $date, $time, $modality, $old) {
            $appointment->update([
                'date' => $date,
                'time' => $time,
                'modality' => $modality ?? $appointment->modality,
                'status' => AppointmentStatus::Pending,
            ]);

            $this->log($actor, 'appointment.rescheduled', $appointment);

            if ($appointment->patient) {
                try {
                    $appointment->patient->notify(
                        new AppointmentRescheduledNotification($appointment->fresh(['professional']), $old)
                    );
                } catch (\Throwable $e) {
                    report($e);
                }
            }

            return $appointment->fresh(['professional', 'patient']);
        });
    }

    public function cancelByPatient(Appointment $appointment, User $patient, ?string $reason = null): Appointment
    {
        if ($patient->isBookingBlocked()) {
            throw ValidationException::withMessages([
                'appointment' => 'Tu cuenta tiene restricciones temporales de agendamiento. Contacta soporte.',
            ]);
        }

        if ($appointment->patient_id !== $patient->id) {
            throw ValidationException::withMessages(['appointment' => 'No tienes permiso para cancelar esta cita.']);
        }

        if (! $appointment->canBeCancelledByPatient()) {
            throw ValidationException::withMessages([
                'appointment' => 'No se puede cancelar. Debe hacerse con al menos 5 días de anticipación y la cita debe estar pendiente o confirmada.',
            ]);
        }

        return $this->finalizeCancel($appointment, $patient, $reason, 'appointment.cancelled');
    }

    public function cancelByAdmin(Appointment $appointment, User $admin, ?string $reason = null): Appointment
    {
        return $this->finalizeCancel(
            $appointment,
            $admin,
            $reason ?? 'Cancelada por administración',
            'appointment.cancelled_by_admin'
        );
    }

    private function finalizeCancel(Appointment $appointment, User $actor, ?string $reason, string $action): Appointment
    {
        $appointment->loadMissing('professional', 'patient');

        $appointment->update([
            'status' => AppointmentStatus::Cancelled,
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
        ]);

        $this->log($actor, $action, $appointment);

        if ($appointment->patient) {
            try {
                $appointment->patient->notify(new AppointmentCancelledNotification($appointment->fresh(['professional'])));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        try {
            $this->waitlist->notifyOnSlotFreed($appointment->fresh(['professional']));
        } catch (\Throwable $e) {
            report($e);
        }

        return $appointment;
    }

    public function confirm(Appointment $appointment, User $actor): Appointment
    {
        $appointment->update(['status' => AppointmentStatus::Confirmed]);
        $this->log($actor, 'appointment.confirmed', $appointment);
        return $appointment;
    }

    public function complete(Appointment $appointment, User $actor): Appointment
    {
        $appointment->update(['status' => AppointmentStatus::Completed]);
        $this->log($actor, 'appointment.completed', $appointment);
        try {
            if ($appointment->patient) {
                $appointment->patient->notify(new \App\Notifications\SurveyInvitationNotification($appointment->fresh()));
                $appointment->forceFill(['survey_invited_at' => now()])->saveQuietly();
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Survey invite on complete failed', ['e' => $e->getMessage()]);
        }

        return $appointment;
    }

        protected function applyCancellationLimits(User $patient): void
    {
        $patient->increment('cancellation_count');
        $patient->refresh();
        // Más de 5 cancelaciones en total → bloqueo suave 7 días
        if ($patient->cancellation_count >= 5) {
            $until = $patient->booking_blocked_until && $patient->booking_blocked_until->isFuture()
                ? $patient->booking_blocked_until
                : now()->addDays(7);
            $patient->forceFill(['booking_blocked_until' => $until])->save();
        }
    }

    public function markNoShow(Appointment $appointment, User $actor): Appointment
    {
        $appointment->update([
            'status' => AppointmentStatus::NoShow,
            'marked_no_show_at' => now(),
        ]);

        $patient = $appointment->patient;
        if ($patient) {
            $patient->increment('no_show_count');
            $patient->refresh();
            // Lista negra suave: 3 no-shows → bloqueo 14 días
            if ($patient->no_show_count >= 3) {
                $patient->forceFill([
                    'booking_blocked_until' => now()->addDays(14),
                ])->save();
            }
        }

        $this->log($actor, 'appointment.no_show', $appointment, null, ['status' => 'no_show']);

        return $appointment->fresh();
    }

    private function log(User $user, string $action, Appointment $appointment): void
    {
        AuditLog::create([
            'user_id' => $user->id,
            'action' => $action,
            'auditable_type' => Appointment::class,
            'auditable_id' => $appointment->id,
            'new_values' => $appointment->toArray(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
