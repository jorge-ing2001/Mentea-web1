<?php

namespace App\Services;

use App\Models\Appointment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Integración de videollamada.
 *
 * Modes (.env MENTEA_MEET_MODE):
 * - off         → no genera enlace; la UI indica que se enviará por otro canal
 * - manual      → no genera enlace automático (admin/profesional lo pegan luego)
 * - placeholder → SOLO desarrollo: enlace ficticio (nunca en producción)
 * - google      → Google Calendar API (requiere OAuth token en config)
 */
class GoogleMeetService
{
    public function mode(): string
    {
        $mode = config('mentea.meet_mode', env('MENTEA_MEET_MODE', 'manual'));

        if (app()->environment('production') && $mode === 'placeholder') {
            return 'manual'; // nunca placeholders en producción
        }

        return $mode;
    }

    public function isEnabled(): bool
    {
        return in_array($this->mode(), ['placeholder', 'google'], true);
    }

    /**
     * @return string|null URL de Meet o null si no aplica
     */
    public function createMeeting(Appointment $appointment): ?string
    {
        return match ($this->mode()) {
            'google' => $this->createGoogleMeet($appointment),
            'placeholder' => $this->createPlaceholder($appointment),
            default => null,
        };
    }

    public function statusMessage(): string
    {
        return match ($this->mode()) {
            'google' => 'Videollamada generada automáticamente con Google Meet.',
            'placeholder' => 'Enlace de prueba (solo desarrollo). No es una sala real de Meet.',
            'manual' => 'El enlace de videollamada se asignará manualmente o se enviará por correo/WhatsApp.',
            default => 'Videollamada no configurada en este entorno.',
        };
    }

    private function createPlaceholder(Appointment $appointment): string
    {
        $link = 'https://meet.google.com/' . Str::lower(Str::random(3) . '-' . Str::random(4) . '-' . Str::random(3));
        Log::warning('Meet placeholder (solo desarrollo)', [
            'appointment_id' => $appointment->id,
            'link' => $link,
        ]);

        return $link;
    }

    private function createGoogleMeet(Appointment $appointment): ?string
    {
        $accessToken = config('mentea.google_access_token') ?: env('GOOGLE_MEET_ACCESS_TOKEN');
        if (! $accessToken) {
            Log::error('MENTEA_MEET_MODE=google pero falta GOOGLE_MEET_ACCESS_TOKEN');
            return null;
        }

        $appointment->loadMissing(['patient', 'professional']);
        $start = $appointment->date->format('Y-m-d') . 'T' .
            (\Carbon\Carbon::parse($appointment->time)->format('H:i:s'));
        $duration = $appointment->professional?->appointment_duration_minutes ?: 45;
        $end = \Carbon\Carbon::parse($start)->addMinutes($duration)->format('Y-m-d\TH:i:s');
        $tz = config('app.timezone', 'America/Bogota');

        try {
            $response = Http::withToken($accessToken)
                ->post('https://www.googleapis.com/calendar/v3/calendars/primary/events?conferenceDataVersion=1', [
                    'summary' => 'Cita Mentea #' . $appointment->id,
                    'description' => 'Cita virtual — no incluye datos clínicos. Ingresa a Mentea para el detalle.',
                    'start' => ['dateTime' => $start, 'timeZone' => $tz],
                    'end' => ['dateTime' => $end, 'timeZone' => $tz],
                    'conferenceData' => [
                        'createRequest' => [
                            'requestId' => 'mentea-' . $appointment->id . '-' . Str::random(6),
                            'conferenceSolutionKey' => ['type' => 'hangoutsMeet'],
                        ],
                    ],
                ]);

            if (! $response->successful()) {
                Log::error('Google Meet API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return null;
            }

            $link = data_get($response->json(), 'hangoutLink')
                ?: data_get($response->json(), 'conferenceData.entryPoints.0.uri');

            Log::info('Google Meet creado', ['appointment_id' => $appointment->id]);

            return $link;
        } catch (\Throwable $e) {
            Log::error('Google Meet exception', ['e' => $e->getMessage()]);
            return null;
        }
    }
}
