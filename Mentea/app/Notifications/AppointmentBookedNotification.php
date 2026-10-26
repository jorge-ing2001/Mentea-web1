<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentBookedNotification extends Notification implements ShouldQueue
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
        $url = url('/paciente/citas/' . $a->id);

        return (new MailMessage)
            ->subject('Cita confirmada — Mentea')
            ->view('emails.appointment-booked', [
                'patientName' => $notifiable->name,
                'professionalName' => $a->professional->name ?? '—',
                'date' => $date,
                'time' => $time,
                'modality' => $a->modality->label(),
                'meetLink' => $a->meet_link,
                'url' => $url,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        $a = $this->appointment->loadMissing('professional');
        $date = $a->date->format('d/m/Y');
        $time = \Carbon\Carbon::parse($a->time)->format('H:i');

        return [
            'kind' => 'appointment_booked',
            'title' => 'Cita agendada',
            'body' => "Con {$a->professional->name} el {$date} a las {$time}.",
            'url' => '/paciente/citas/' . $a->id,
            'icon' => 'calendar',
            'appointment_id' => $a->id,
        ];
    }
}
