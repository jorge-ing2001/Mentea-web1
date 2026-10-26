<?php

namespace App\Jobs;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Notifications\SurveyInvitationNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendSurveyReminders implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        // Citas completadas hace 1–7 días sin encuesta: invitar o recordar
        $appointments = Appointment::query()
            ->with(['patient', 'survey'])
            ->where('status', AppointmentStatus::Completed->value)
            ->whereDate('date', '>=', now()->subDays(7)->toDateString())
            ->whereDate('date', '<=', now()->subDay()->toDateString())
            ->get();

        $sent = 0;
        foreach ($appointments as $appointment) {
            if ($appointment->survey || ! $appointment->patient) {
                continue;
            }
            // Ya se recordó en las últimas 48 h
            if ($appointment->survey_invited_at && $appointment->survey_invited_at->gt(now()->subHours(48))) {
                continue;
            }
            try {
                $appointment->patient->notify(new SurveyInvitationNotification($appointment));
                $appointment->forceFill(['survey_invited_at' => now()])->saveQuietly();
                $sent++;
            } catch (\Throwable $e) {
                Log::warning('Survey invite failed', ['id' => $appointment->id, 'e' => $e->getMessage()]);
            }
        }
        Log::info("Survey reminders sent: {$sent}");
    }
}
