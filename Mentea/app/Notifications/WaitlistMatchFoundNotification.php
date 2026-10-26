<?php

namespace App\Notifications;

use App\Models\WaitlistEntry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WaitlistMatchFoundNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public WaitlistEntry $entry) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $e = $this->entry->loadMissing('offeredProfessional');
        $date = $e->offered_date?->format('d/m/Y');
        $time = $e->offered_time ? \Carbon\Carbon::parse($e->offered_time)->format('H:i') : '';
        $expires = $e->offer_expires_at?->format('H:i');

        return (new MailMessage)
            ->subject('Disponibilidad encontrada — Mentea')
            ->view('emails.waitlist-match', [
                'patientName' => $notifiable->name,
                'specialty' => $e->specialty ?? '—',
                'professionalName' => $e->offeredProfessional->name ?? '—',
                'date' => $date,
                'time' => $time,
                'modality' => $e->offered_modality?->label() ?? '—',
                'expires' => $expires,
                'url' => url('/paciente/lista-espera/' . $e->id),
            ]);
    }

    public function toArray(object $notifiable): array
    {
        $e = $this->entry->loadMissing('offeredProfessional');
        $date = $e->offered_date?->format('d/m/Y');
        $time = $e->offered_time ? \Carbon\Carbon::parse($e->offered_time)->format('H:i') : '';

        return [
            'kind' => 'waitlist_match',
            'title' => 'Disponibilidad en lista de espera',
            'body' => "{$e->offeredProfessional->name ?? 'Profesional'} · {$date} {$time}. Tienes 60 min para confirmar.",
            'url' => '/paciente/lista-espera/' . $e->id,
            'icon' => 'clock',
            'waitlist_id' => $e->id,
        ];
    }
}
