<?php

namespace App\Notifications;

use App\Models\Appointment;
use App\Models\WaitlistEntry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WaitlistSlotAvailableNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Appointment $freedAppointment,
        public WaitlistEntry $entry,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $pro = $this->freedAppointment->professional;
        $date = $this->freedAppointment->date?->format('d/m/Y');
        $time = \Carbon\Carbon::parse($this->freedAppointment->time)->format('H:i');

        return (new MailMessage)
            ->subject('Hay un hueco disponible — Mentea')
            ->greeting('Hola ' . $notifiable->name . ',')
            ->line('Se ha liberado un horario que puede interesarte (estás en lista de espera).')
            ->line('**Profesional:** ' . ($pro->name ?? '—'))
            ->line('**Especialidad:** ' . ($pro->specialty ?? '—'))
            ->line('**Fecha liberada:** ' . $date . ' a las ' . $time)
            ->line('El horario puede ocuparse rápido. Entra y agenda si sigue disponible.')
            ->action('Agendar cita', url('/paciente/citas/agendar'))
            ->line('Si ya no te interesa, ignora este mensaje.');
    }
}
