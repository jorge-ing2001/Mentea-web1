<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentCancelledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Appointment $appointment) {}

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
            ->subject('Cita cancelada — Mentea')
            ->view('emails.appointment-cancelled', [
                'patientName' => $notifiable->name,
                'professionalName' => $a->professional->name ?? '—',
                'date' => $date,
                'time' => $time,
                'reason' => $a->cancellation_reason,
                'url' => url('/paciente/citas/agendar'),
            ]);
    }

    public function toArray(object $notifiable): array
    {
        $a = $this->appointment->loadMissing('professional');
        $date = $a->date->format('d/m/Y');
        $time = \Carbon\Carbon::parse($a->time)->format('H:i');

        return [
            'kind' => 'appointment_cancelled',
            'title' => 'Cita cancelada',
            'body' => "La cita con {$a->professional->name} del {$date} a las {$time} fue cancelada.",
            'url' => '/paciente/citas',
            'icon' => 'x',
            'appointment_id' => $a->id,
        ];
    }
}
