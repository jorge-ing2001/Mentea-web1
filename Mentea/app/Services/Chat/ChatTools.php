<?php

namespace App\Services\Chat;

use App\Enums\Modality;
use App\Enums\WaitlistStatus;
use App\Models\Appointment;
use App\Models\Professional;
use App\Models\User;
use App\Models\WaitlistEntry;
use App\Services\AppointmentService;
use App\Services\AvailabilityService;
use App\Services\WaitlistService;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Capa de herramientas del agente. Adaptadores finos sobre los servicios ya
 * existentes: NO reimplementan lógica de agenda. Devuelven datos estructurados
 * que el modelo solo redacta. Ninguna herramienta de cupo inventa horarios:
 * todos los slots vienen de AvailabilityService.
 */
class ChatTools
{
    /** Máximo de opciones de cupo que se ofrecen por turno (regla 2-4). */
    public const MAX_SLOTS = 4;

    public function __construct(
        private AvailabilityService $availability,
        private AppointmentService $appointments,
        private WaitlistService $waitlist,
    ) {}

    /**
     * Catálogo de los 5 servicios de negocio mapeados a Professional.profession.
     * Reporta cuáles tienen profesionales disponibles (sin inventar).
     *
     * @return array<int,array{service:string,available:bool,professionals:int}>
     */
    public function listServices(): array
    {
        $services = (array) config('chat.services', []);

        return collect($services)->map(function (string $service) {
            $count = $this->professionalsFor($service)->count();

            return [
                'service' => $service,
                'available' => $count > 0,
                'professionals' => $count,
            ];
        })->all();
    }

    /**
     * Profesionales disponibles para un servicio (profession amplio) o una
     * especialidad granular. Match case-insensitive para tolerar variantes.
     *
     * @return Collection<int,Professional>
     */
    public function professionalsFor(string $serviceOrSpecialty): Collection
    {
        $needle = $this->normalize($serviceOrSpecialty);

        return Professional::query()
            ->where('is_available', true)
            ->get()
            ->filter(function (Professional $p) use ($needle) {
                $profession = $this->normalize((string) $p->profession);
                $specialty = $this->normalize((string) $p->specialty);

                return str_contains($profession, $needle)
                    || str_contains($needle, $profession)
                    || str_contains($specialty, $needle)
                    || str_contains($needle, $specialty);
            })
            ->values();
    }

    /**
     * Fechas reales con disponibilidad para un profesional.
     *
     * @return Collection<int,string> 'Y-m-d'
     */
    public function getAvailableDates(Professional $professional, int $daysAhead = 45): Collection
    {
        return $this->availability->getAvailableDates($professional, $daysAhead);
    }

    /**
     * Entre 2 y 4 cupos REALES para un profesional en una fecha. Nunca
     * sintetiza horarios: devuelve únicamente lo que AvailabilityService
     * reporta, recortado al máximo permitido.
     *
     * @return array<int,string> 'H:i'
     */
    public function getSlots(Professional $professional, string $date): array
    {
        $slots = $this->availability->getAvailableSlots($professional, Carbon::parse($date));

        return $slots->take(self::MAX_SLOTS)->values()->all();
    }

    /**
     * Reserva a través de AppointmentService (que re-valida disponibilidad,
     * consentimiento, bloqueo y doble reserva dentro de una transacción).
     */
    public function book(
        User $patient,
        Professional $professional,
        string $date,
        string $time,
        Modality $modality,
        ?string $reason = null,
    ): Appointment {
        return $this->appointments->book($patient, $professional, $date, $time, $modality, $reason);
    }

    public function reschedule(
        Appointment $appointment,
        User $actor,
        string $date,
        string $time,
        ?Modality $modality = null,
    ): Appointment {
        return $this->appointments->reschedule($appointment, $actor, $date, $time, $modality);
    }

    public function cancel(Appointment $appointment, User $patient, ?string $reason = null): Appointment
    {
        return $this->appointments->cancelByPatient($appointment, $patient, $reason);
    }

    /**
     * Alta en lista de espera (FIFO). Usa la especialidad granular del
     * profesional para que el match de WaitlistService::onSlotFreed funcione.
     */
    public function joinWaitlist(
        User $patient,
        Professional $professional,
        ?Modality $modality = null,
        ?string $notes = null,
    ): WaitlistEntry {
        return WaitlistEntry::create([
            'patient_id' => $patient->id,
            'professional_id' => $professional->id,
            'specialty' => $professional->specialty,
            'modality' => $modality,
            'status' => WaitlistStatus::Waiting,
            'notes' => $notes,
        ]);
    }

    private function normalize(string $value): string
    {
        $value = mb_strtolower(trim($value));
        $map = [
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ñ' => 'n', 'ü' => 'u',
        ];

        return strtr($value, $map);
    }
}
