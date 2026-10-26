<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentReminderNotification extends Notification implements ShouldQueue
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
            ->subject('Recordatorio de cita — Mentea')
            ->view('emails.appointment-reminder', [
                'patientName' => $notifiable->name,
                'professionalName' => $a->professional->name ?? '—',
                'date' => $date,
                'time' => $time,
                'modality' => $a->modality->label(),
                'meetLink' => $a->meet_link,
                'url' => url('/paciente/citas/' . $a->id),
            ]);
    }

    public function toArray(object $notifiable): array
    {
        $a = $this->appointment->loadMissing('professional');
        $date = $a->date->format('d/m/Y');
        $time = \Carbon\Carbon::parse($a->time)->format('H:i');

        return [
            'kind' => 'appointment_reminder',
            'title' => 'Recordatorio: cita mañana',
            'body' => "Con {$a->professional->name} el {$date} a las {$time}.",
            'url' => '/paciente/citas/' . $a->id,
            'icon' => 'bell',
            'appointment_id' => $a->id,
        ];
    }
}
