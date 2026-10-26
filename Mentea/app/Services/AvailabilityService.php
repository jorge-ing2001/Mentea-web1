<?php

namespace App\Services;

use App\Enums\AppointmentStatus;
use App\Enums\WaitlistStatus;
use App\Models\Appointment;
use App\Models\Professional;
use App\Models\ScheduleException;
use App\Models\WaitlistEntry;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AvailabilityService
{
    public function getAvailableSlots(Professional $professional, Carbon $date): Collection
    {
        if (! $professional->is_available) {
            return collect();
        }

        // Festivos globales o bloqueos/ausencias del profesional
        if ($this->isDateBlocked($professional, $date)) {
            return collect();
        }

        $dayOfWeek = $date->dayOfWeekIso;

        $schedules = $professional->activeSchedules()
            ->where('day_of_week', $dayOfWeek)
            ->get();

        if ($schedules->isEmpty()) {
            return collect();
        }

        $duration = $professional->appointment_duration_minutes ?: 45;

        $booked = Appointment::query()
            ->where('professional_id', $professional->id)
            ->whereDate('date', $date)
            ->whereIn('status', [
                AppointmentStatus::Pending->value,
                AppointmentStatus::Confirmed->value,
            ])
            ->get()
            ->map(fn ($a) => Carbon::parse($a->time)->format('H:i'))
            ->flip();

        // Holds activos de lista de espera (60 min)
        $held = WaitlistEntry::query()
            ->where('status', WaitlistStatus::MatchFound->value)
            ->where('offered_professional_id', $professional->id)
            ->whereDate('offered_date', $date)
            ->where('offer_expires_at', '>', now())
            ->get()
            ->map(fn ($e) => Carbon::parse($e->offered_time)->format('H:i'))
            ->flip();

        $slots = collect();

        foreach ($schedules as $schedule) {
            $start = Carbon::parse($date->format('Y-m-d') . ' ' . $schedule->start_time);
            $end = Carbon::parse($date->format('Y-m-d') . ' ' . $schedule->end_time);

            while ($start->copy()->addMinutes($duration)->lte($end)) {
                $slot = $start->format('H:i');
                if (! $booked->has($slot) && ! $held->has($slot) && $start->gt(now())
                    && ! $this->isTimeBlocked($professional, $date, $slot)) {
                    $slots->push($slot);
                }
                $start->addMinutes($duration);
            }
        }

        return $slots->unique()->sort()->values();
    }

    public function isSlotAvailable(Professional $professional, Carbon $date, string $time): bool
    {
        return $this->getAvailableSlots($professional, $date)->contains(
            Carbon::parse($time)->format('H:i')
        );
    }

    /**
     * Disponibilidad sin considerar holds (para confirmar oferta propia).
     */
    public function isSlotFreeOfAppointments(Professional $professional, Carbon $date, string $time): bool
    {
        $timeNorm = Carbon::parse($time)->format('H:i');

        $taken = Appointment::query()
            ->where('professional_id', $professional->id)
            ->whereDate('date', $date)
            ->whereIn('status', [
                AppointmentStatus::Pending->value,
                AppointmentStatus::Confirmed->value,
            ])
            ->get()
            ->contains(fn ($a) => Carbon::parse($a->time)->format('H:i') === $timeNorm);

        return ! $taken;
    }

    public function getAvailableDates(Professional $professional, int $daysAhead = 45): Collection
    {
        $dates = collect();
        $cursor = now()->startOfDay();

        for ($i = 0; $i < $daysAhead; $i++) {
            $day = $cursor->copy()->addDays($i);
            if ($this->getAvailableSlots($professional, $day)->isNotEmpty()) {
                $dates->push($day->format('Y-m-d'));
            }
        }

        return $dates;
    }

    public function isDateBlocked(Professional $professional, Carbon $date): bool
    {
        // Festivo / cierre clínica (professional_id null, full day)
        $global = ScheduleException::query()
            ->whereNull('professional_id')
            ->whereDate('date', $date)
            ->whereIn('type', ['holiday', 'block'])
            ->whereNull('time_from')
            ->exists();
        if ($global) {
            return true;
        }

        return ScheduleException::query()
            ->where('professional_id', $professional->id)
            ->whereDate('date', $date)
            ->whereIn('type', ['absence', 'block', 'holiday'])
            ->whereNull('time_from')
            ->exists();
    }

    public function isTimeBlocked(Professional $professional, Carbon $date, string $time): bool
    {
        $t = Carbon::parse($time)->format('H:i');
        $exceptions = ScheduleException::query()
            ->whereDate('date', $date)
            ->whereNotNull('time_from')
            ->where(function ($q) use ($professional) {
                $q->whereNull('professional_id')
                    ->orWhere('professional_id', $professional->id);
            })
            ->get();

        foreach ($exceptions as $ex) {
            $from = Carbon::parse($ex->time_from)->format('H:i');
            $to = Carbon::parse($ex->time_to)->format('H:i');
            if ($t >= $from && $t < $to) {
                return true;
            }
        }

        return false;
    }
}
