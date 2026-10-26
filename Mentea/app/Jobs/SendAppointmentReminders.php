<?php

namespace App\Jobs;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Notifications\AppointmentReminderNotification;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

/**
 * Envía recordatorios ~24 h y ~2 h antes de la cita (ventana de 15 min).
 */
class SendAppointmentReminders implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        $sent24 = $this->sendWindow(hoursBefore: 24, column: 'reminder_24h_sent_at', windowMinutes: 20);
        $sent2 = $this->sendWindow(hoursBefore: 2, column: 'reminder_2h_sent_at', windowMinutes: 15);

        Log::info("Recordatorios enviados: 24h={$sent24}, 2h={$sent2}");
    }

    private function sendWindow(int $hoursBefore, string $column, int $windowMinutes): int
    {
        $target = now()->addHours($hoursBefore);
        $from = $target->copy()->subMinutes($windowMinutes);
        $to = $target->copy()->addMinutes($windowMinutes);

        $appointments = Appointment::query()
            ->with(['patient', 'professional'])
            ->whereIn('status', [
                AppointmentStatus::Pending->value,
                AppointmentStatus::Confirmed->value,
            ])
            ->whereNull($column)
            ->get()
            ->filter(function (Appointment $a) use ($from, $to) {
                $dt = Carbon::parse($a->date->format('Y-m-d') . ' ' . $a->time->format('H:i:s'));
                return $dt->between($from, $to);
            });

        $sent = 0;
        foreach ($appointments as $appointment) {
            if (! $appointment->patient) {
                continue;
            }
            try {
                $appointment->patient->notify(new AppointmentReminderNotification($appointment));
                $appointment->forceFill([$column => now()])->saveQuietly();
                $sent++;
            } catch (\Throwable $e) {
                Log::warning('Reminder failed', [
                    'appointment_id' => $appointment->id,
                    'column' => $column,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $sent;
    }
}
