<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentRescheduledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Appointment $appointment,
        public array $old,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $a = $this->appointment->loadMissing('professional');
        $date = $a->date->format('d/m/Y');
        $time = \Carbon\Carbon::parse($a->time)->format('H:i');

        return (new MailMessage)
            ->subject('Cita reprogramada — Mentea')
            ->view('emails.appointment-rescheduled', [
                'patientName' => $notifiable->name,
                'professionalName' => $a->professional->name ?? '—',
                'oldDate' => $this->old['date'] ?? '',
                'oldTime' => $this->old['time'] ?? '',
                'date' => $date,
                'time' => $time,
                'url' => url('/paciente/citas/' . $a->id),
            ]);
    }

    public function toArray(object $notifiable): array
    {
        $a = $this->appointment->loadMissing('professional');
        $date = $a->date->format('d/m/Y');
        $time = \Carbon\Carbon::parse($a->time)->format('H:i');

        return [
            'kind' => 'appointment_rescheduled',
            'title' => 'Cita reprogramada',
            'body' => "Nueva fecha: {$date} a las {$time} con {$a->professional->name}.",
            'url' => '/paciente/citas/' . $a->id,
            'icon' => 'refresh',
            'appointment_id' => $a->id,
        ];
    }
}
