<?php

namespace App\Services;

use App\Enums\AppointmentStatus;
use App\Enums\Modality;
use App\Enums\WaitlistStatus;
use App\Models\Appointment;
use App\Models\Professional;
use App\Models\WaitlistEntry;
use App\Notifications\WaitlistMatchFoundNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class WaitlistService
{
    public const HOLD_MINUTES = 60;

    public function __construct(
        private AvailabilityService $availability,
    ) {}

    /**
     * Cuando se libera un hueco (cancelación u otra), busca match FIFO y ofrece.
     */
    public function onSlotFreed(Professional $professional, string $date, string $time, ?Modality $modality = null): ?WaitlistEntry
    {
        $dateObj = Carbon::parse($date);
        $timeNorm = Carbon::parse($time)->format('H:i');

        // No ofrecer si ya hay hold activo de otro paciente sobre ese slot
        if ($this->isSlotHeld($professional->id, $date, $timeNorm)) {
            return null;
        }

        // Verificar que el slot siga libre (citas reales; holds de otros ya filtrados arriba)
        if (! $this->availability->isSlotFreeOfAppointments($professional, $dateObj, $timeNorm)) {
            return null;
        }

        $candidates = WaitlistEntry::query()
            ->with('patient')
            ->where('status', WaitlistStatus::Waiting->value)
            ->where('specialty', $professional->specialty)
            ->orderBy('created_at') // FIFO
            ->get();

        foreach ($candidates as $entry) {
            $mod = $modality ?? $entry->modality;
            if (! $entry->matchesSlot($professional, $dateObj, $timeNorm, $mod)) {
                continue;
            }
            // No ofrecer si el paciente ya tiene cita a esa hora
            if ($this->patientHasConflict($entry->patient_id, $date, $timeNorm)) {
                continue;
            }
            return $this->offerSlot($entry, $professional, $date, $timeNorm, $mod ?? Modality::InPerson);
        }

        return null;
    }

    /**
     * Compatibilidad con llamadas anteriores desde cancelación.
     */
    public function notifyOnSlotFreed(Appointment $cancelled): int
    {
        $professional = $cancelled->professional;
        if (! $professional) {
            return 0;
        }
        $date = $cancelled->date->format('Y-m-d');
        $time = Carbon::parse($cancelled->time)->format('H:i');
        $entry = $this->onSlotFreed($professional, $date, $time, $cancelled->modality);
        return $entry ? 1 : 0;
    }

    public function offerSlot(
        WaitlistEntry $entry,
        Professional $professional,
        string $date,
        string $time,
        Modality $modality,
    ): WaitlistEntry {
        $entry->update([
            'status' => WaitlistStatus::MatchFound,
            'offered_professional_id' => $professional->id,
            'offered_date' => $date,
            'offered_time' => $time,
            'offered_modality' => $modality,
            'offered_at' => now(),
            'offer_expires_at' => now()->addMinutes(self::HOLD_MINUTES),
        ]);

        $entry = $entry->fresh(['patient', 'offeredProfessional']);

        try {
            $entry->patient?->notify(new WaitlistMatchFoundNotification($entry));
        } catch (\Throwable $e) {
            Log::warning('Waitlist offer notify failed', ['id' => $entry->id, 'error' => $e->getMessage()]);
        }

        return $entry;
    }

    public function confirmOffer(WaitlistEntry $entry, AppointmentService $appointmentService): Appointment
    {
        if (! $entry->isOfferActive()) {
            throw ValidationException::withMessages([
                'offer' => 'La oferta ya no está disponible o ha vencido.',
            ]);
        }

        $professional = Professional::findOrFail($entry->offered_professional_id);
        $date = $entry->offered_date->format('Y-m-d');
        $time = Carbon::parse($entry->offered_time)->format('H:i');
        $modality = $entry->offered_modality ?? Modality::InPerson;

        // Liberar el hold de esta entrada temporalmente para que book pueda validar slot
        // El slot está "held" por esta entry; AvailabilityService debe permitirlo al confirmar

        $appointment = $appointmentService->book(
            patient: $entry->patient,
            professional: $professional,
            date: $date,
            time: $time,
            modality: $modality,
            reason: 'Confirmada desde lista de espera #' . $entry->id,
            orderDocument: null,
            authorizationDocument: null,
            skipAvailabilityCheck: true, // slot was held for this patient
        );

        $entry->update([
            'status' => WaitlistStatus::Confirmed,
            'confirmed_at' => now(),
            'assigned_at' => now(),
            'assigned_appointment_id' => $appointment->id,
            'offer_expires_at' => null,
        ]);

        return $appointment;
    }

    public function rejectOffer(WaitlistEntry $entry): void
    {
        if ($entry->status !== WaitlistStatus::MatchFound) {
            throw ValidationException::withMessages(['offer' => 'No hay oferta activa para rechazar.']);
        }

        $proId = $entry->offered_professional_id;
        $date = $entry->offered_date?->format('Y-m-d');
        $time = $entry->offered_time ? Carbon::parse($entry->offered_time)->format('H:i') : null;
        $modality = $entry->offered_modality;

        // Volver a waiting si aún está en rango de fechas
        $stillValid = $entry->date_to && $entry->date_to->gte(today());

        $entry->update([
            'status' => $stillValid ? WaitlistStatus::Waiting : WaitlistStatus::Expired,
            'offered_professional_id' => null,
            'offered_date' => null,
            'offered_time' => null,
            'offered_modality' => null,
            'offered_at' => null,
            'offer_expires_at' => null,
            'expired_at' => $stillValid ? null : now(),
        ]);

        // Ofrecer al siguiente
        if ($proId && $date && $time) {
            $pro = Professional::find($proId);
            if ($pro) {
                $this->onSlotFreed($pro, $date, $time, $modality);
            }
        }
    }

    public function cancelRequest(WaitlistEntry $entry): void
    {
        $hadOffer = $entry->status === WaitlistStatus::MatchFound;
        $proId = $entry->offered_professional_id;
        $date = $entry->offered_date?->format('Y-m-d');
        $time = $entry->offered_time ? Carbon::parse($entry->offered_time)->format('H:i') : null;
        $modality = $entry->offered_modality;

        $entry->update([
            'status' => WaitlistStatus::Cancelled,
            'cancelled_at' => now(),
            'offered_professional_id' => null,
            'offered_date' => null,
            'offered_time' => null,
            'offered_modality' => null,
            'offered_at' => null,
            'offer_expires_at' => null,
        ]);

        if ($hadOffer && $proId && $date && $time) {
            $pro = Professional::find($proId);
            if ($pro) {
                $this->onSlotFreed($pro, $date, $time, $modality);
            }
        }
    }

    /**
     * Expira ofertas vencidas y ofrece al siguiente (FIFO).
     */
    public function expireOverdueOffers(): int
    {
        $expired = WaitlistEntry::query()
            ->where('status', WaitlistStatus::MatchFound->value)
            ->where('offer_expires_at', '<', now())
            ->get();

        $count = 0;
        foreach ($expired as $entry) {
            $proId = $entry->offered_professional_id;
            $date = $entry->offered_date?->format('Y-m-d');
            $time = $entry->offered_time ? Carbon::parse($entry->offered_time)->format('H:i') : null;
            $modality = $entry->offered_modality;

            $entry->update([
                'status' => WaitlistStatus::Expired,
                'expired_at' => now(),
                'offered_professional_id' => null,
                'offered_date' => null,
                'offered_time' => null,
                'offered_modality' => null,
                'offered_at' => null,
                'offer_expires_at' => null,
            ]);
            $count++;

            if ($proId && $date && $time) {
                $pro = Professional::find($proId);
                if ($pro) {
                    $this->onSlotFreed($pro, $date, $time, $modality);
                }
            }
        }

        return $count;
    }

    public function isSlotHeld(int $professionalId, string $date, string $time): bool
    {
        $timeNorm = Carbon::parse($time)->format('H:i:s');

        return WaitlistEntry::query()
            ->where('status', WaitlistStatus::MatchFound->value)
            ->where('offered_professional_id', $professionalId)
            ->whereDate('offered_date', $date)
            ->where(function ($q) use ($timeNorm, $time) {
                $q->whereTime('offered_time', $timeNorm)
                  ->orWhereTime('offered_time', Carbon::parse($time)->format('H:i'));
            })
            ->where('offer_expires_at', '>', now())
            ->exists();
    }

    private function patientHasConflict(int $patientId, string $date, string $time): bool
    {
        $timeNorm = Carbon::parse($time)->format('H:i:s');

        return Appointment::query()
            ->where('patient_id', $patientId)
            ->whereDate('date', $date)
            ->whereIn('status', [AppointmentStatus::Pending->value, AppointmentStatus::Confirmed->value])
            ->where(function ($q) use ($timeNorm, $time) {
                $q->whereTime('time', $timeNorm)
                  ->orWhereTime('time', Carbon::parse($time)->format('H:i'));
            })
            ->exists();
    }
}
