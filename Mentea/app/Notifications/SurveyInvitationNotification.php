<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SurveyInvitationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Appointment $appointment) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('patient.surveys.create', $this->appointment);

        return (new MailMessage)
            ->subject('¿Cómo fue tu cita? — Mentea')
            ->greeting('Hola ' . $notifiable->name)
            ->line('Tu cita del ' . $this->appointment->date->format('d/m/Y') . ' fue completada.')
            ->line('Nos ayuda mucho conocer tu experiencia (1 minuto).')
            ->action('Responder encuesta', $url)
            ->line('Gracias por confiar en Mentea.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'survey_invite',
            'appointment_id' => $this->appointment->id,
            'message' => 'Califica tu cita del ' . $this->appointment->date->format('d/m/Y'),
            'url' => route('patient.surveys.create', $this->appointment),
        ];
    }
}
