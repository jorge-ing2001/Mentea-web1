<?php

namespace App\Services\Chat;

use App\Enums\AppointmentStatus;
use App\Enums\Modality;
use App\Models\Appointment;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\Professional;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\AvailabilityService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

/**
 * Orquestador del agente conversacional nativo de Mentea.
 *
 * Principio rector: EL CÓDIGO MANDA, EL MODELO SOLO REDACTA. Todas las reglas
 * duras se aplican aquí en PHP, en este orden por turno:
 *
 *   1. Crisis        -> entrega texto configurable (línea 123 + línea de
 *                       crisis), marca escalado, DETIENE el agendamiento.
 *   2. Escalado      -> preguntas clínicas / menores / fuera de alcance:
 *                       mensaje neutro, no inventa respuestas.
 *   3. Consentimiento-> antes de recolectar datos o agendar exige
 *                       hasInformedConsent(); si falta, enruta a aceptarlo.
 *   4. Cupo/redacción-> solo slots reales de AvailabilityService; la reserva
 *                       pasa por AppointmentService (re-valida y transacciona).
 *
 * Si no hay OPENROUTER_API_KEY o la red falla, se usa una ruta determinista
 * que aplica exactamente las mismas reglas duras. La app nunca se cae.
 */
class ChatAgentService
{
    public function __construct(
        private OpenRouterClient $openRouter,
        private ChatTools $tools,
    ) {}

    /**
     * Procesa un turno del usuario y devuelve la respuesta del asistente + meta.
     *
     * @return array{reply:string, conversation_id:int, crisis:bool, escalated:bool, requires_consent:bool, requires_login:bool, slots:array<int,array<string,mixed>>}
     */
    public function handle(ChatConversation $conversation, string $userMessage, ?User $user = null): array
    {
        $userMessage = trim($userMessage);

        // Capturar la acción pendiente (oferta de cupos / reprogramación) ANTES
        // de persistir el mensaje nuevo, para que un turno de seguimiento
        // ("la primera", "las 9:00", "sí") se resuelva a un cupo concreto.
        $pending = $conversation->pendingAction();

        // 1) Persistir el mensaje entrante.
        $this->persist($conversation, 'user', $userMessage);

        // 2) REGLA DURA: crisis primero. Corta todo el flujo de agendamiento.
        if ($this->detectsCrisis($userMessage)) {
            $reply = $this->crisisText();

            $conversation->forceFill([
                'crisis_flagged_at' => now(),
                'escalated_at' => now(),
            ])->save();

            return $this->finish($conversation, $reply, [
                'crisis' => true,
                'escalated' => true,
                'meta' => ['reason' => 'crisis'],
            ]);
        }

        // 3) REGLA DURA: escalado a humano (clínico / menores / fuera de alcance).
        if ($this->requiresHumanEscalation($userMessage)) {
            $reply = $this->escalationText();

            $conversation->forceFill(['escalated_at' => now()])->save();

            return $this->finish($conversation, $reply, [
                'escalated' => true,
                'meta' => ['reason' => 'escalation'],
            ]);
        }

        // 4) Seguimiento de una acción pendiente (confirmar un cupo ofrecido o
        //    una reprogramación). Requiere paciente autenticado con consentimiento.
        if ($pending !== null && $this->isAuthenticatedPatientWithConsent($user)) {
            $resolved = $this->resolvePendingAction($conversation, $pending, $userMessage, $user);
            if ($resolved !== null) {
                return $resolved;
            }
        }

        // 5) Cancelar una cita existente a través del agente.
        if ($this->intendsToCancel($userMessage)) {
            return $this->handleCancel($conversation, $userMessage, $user);
        }

        // 6) Reprogramar una cita existente a través del agente.
        if ($this->intendsToReschedule($userMessage)) {
            return $this->handleReschedule($conversation, $userMessage, $user);
        }

        // 7) Alta explícita en lista de espera.
        if ($this->intendsWaitlist($userMessage)) {
            return $this->handleWaitlist($conversation, $userMessage, $user);
        }

        // 8) REGLA DURA: consentimiento antes de recolectar datos / agendar.
        if ($this->intendsToBook($userMessage)) {
            $gate = $this->bookingGate($conversation, $user);
            if ($gate !== null) {
                return $gate;
            }

            // Consentimiento OK: ofrecer cupos REALES (2-4) si hay servicio detectable.
            [$slots, $bookingReply, $offerMeta] = $this->offerRealSlots($userMessage);

            if ($bookingReply !== null) {
                $reply = $this->phrase($conversation, $userMessage, $bookingReply, $user);

                return $this->finish($conversation, $reply, [
                    'slots' => $slots,
                    'meta' => array_merge(['reason' => 'slots_offered'], $offerMeta),
                ]);
            }
        }

        // 9) Conversación general: listar servicios / saludo, redactado por el
        //    modelo si está configurado, con fallback determinista si no.
        $deterministic = $this->deterministicReply($userMessage);
        $reply = $this->phrase($conversation, $userMessage, $deterministic, $user);

        return $this->finish($conversation, $reply, ['meta' => []]);
    }

    /**
     * Puerta de reglas de agendamiento: distingue "debe iniciar sesión" de
     * "debe aceptar consentimiento". Devuelve una respuesta ya cerrada si la
     * puerta bloquea, o null si el paciente puede continuar.
     *
     * @return array<string,mixed>|null
     */
    private function bookingGate(ChatConversation $conversation, ?User $user): ?array
    {
        if (! $user instanceof User || ! $user->isPatient()) {
            return $this->finish($conversation, $this->guestBookingText(), [
                'requires_login' => true,
                'requires_consent' => true,
                'meta' => ['reason' => 'guest_booking'],
            ]);
        }

        if (! $user->hasInformedConsent()) {
            return $this->finish($conversation, $this->consentText(), [
                'requires_consent' => true,
                'meta' => ['reason' => 'consent'],
            ]);
        }

        return null;
    }

    private function isAuthenticatedPatientWithConsent(?User $user): bool
    {
        return $user instanceof User && $user->isPatient() && $user->hasInformedConsent();
    }

    /**
     * Resuelve un turno de seguimiento sobre una oferta pendiente. Re-verifica
     * el cupo contra AvailabilityService JUSTO antes de reservar/reprogramar;
     * si ya no está disponible, degrada con gracia y re-ofrece.
     *
     * @param  array<string,mixed>  $pending
     * @return array<string,mixed>|null Respuesta cerrada o null si el mensaje
     *                                  no era una selección reconocible.
     */
    private function resolvePendingAction(ChatConversation $conversation, array $pending, string $message, User $user): ?array
    {
        $type = (string) ($pending['type'] ?? '');
        $slots = (array) ($pending['slots'] ?? []);

        if ($slots === [] || ! in_array($type, ['book', 'reschedule'], true)) {
            return null;
        }

        // Si el usuario pide cancelar la operación pendiente.
        if ($this->matchesAny($message, ['cancelar', 'cancela', 'mejor no', 'olvidalo', 'olvídalo', 'dejalo', 'déjalo'])
            && ! $this->matchesAny($message, ['cita', 'turno'])) {
            return $this->finish($conversation, 'Sin problema, no agendé nada. ¿Te ayudo con algo más?', [
                'meta' => ['reason' => 'offer_dismissed'],
            ]);
        }

        $selected = $this->matchSelection($message, $slots);

        if ($selected === null) {
            return null;
        }

        $professional = Professional::find($selected['professional_id'] ?? null);

        if (! $professional instanceof Professional) {
            return $this->finish($conversation, $this->noAvailabilityText(), [
                'meta' => ['reason' => 'offer_stale'],
            ]);
        }

        $date = (string) $selected['date'];
        $time = (string) $selected['time'];
        $modality = $this->resolveModality($pending, $message);

        // RE-VERIFICAR el cupo contra la agenda ANTES de confirmar.
        if (! $this->availability()->isSlotAvailable($professional, Carbon::parse($date), $time)) {
            return $this->reofferAfterStaleSlot($conversation, $professional, (string) ($pending['service'] ?? ''), $type, $pending);
        }

        try {
            if ($type === 'reschedule') {
                $appointment = Appointment::find($pending['appointment_id'] ?? null);
                if (! $appointment instanceof Appointment) {
                    return $this->finish($conversation, 'No encontré la cita que querías reprogramar. ¿Me indicas de nuevo?', [
                        'meta' => ['reason' => 'reschedule_missing'],
                    ]);
                }

                $this->tools->reschedule($appointment, $user, $date, $time, $modality);

                $reply = "Listo, reprogramé tu cita para el {$date} a las {$time}. "
                    .'Te llegará la confirmación a tu correo.';

                return $this->finish($conversation, $reply, ['meta' => ['reason' => 'rescheduled']]);
            }

            $appointment = $this->tools->book($user, $professional, $date, $time, $modality);

            $reply = "¡Listo! Reservé tu cita de {$pending['service']} con {$professional->name} "
                ."el {$date} a las {$time} ({$modality->label()}). Queda pendiente de confirmación "
                .'y te llegará el detalle a tu correo.';

            return $this->finish($conversation, $reply, [
                'meta' => ['reason' => 'booked', 'appointment_id' => $appointment->id],
            ]);
        } catch (ValidationException $e) {
            // AppointmentService re-valida y lanza si el cupo se tomó entre medio.
            return $this->reofferAfterStaleSlot($conversation, $professional, (string) ($pending['service'] ?? ''), $type, $pending);
        }
    }

    /**
     * El cupo elegido ya no está disponible: mensaje grácil + re-oferta real.
     *
     * @param  array<string,mixed>  $pending
     * @return array<string,mixed>
     */
    private function reofferAfterStaleSlot(ChatConversation $conversation, Professional $professional, string $service, string $type, array $pending): array
    {
        $prefix = 'Lo siento, ese cupo ya no está disponible.';

        // Mismo piso 2-4 que el flujo de oferta: primero el profesional actual,
        // luego otros del mismo servicio. Cupos siempre reales.
        $slots = $this->collectRescheduleSlots($professional, $service);

        if ($slots === []) {
            return $this->finish($conversation, $prefix.' '.$this->noAvailabilityText(), [
                'meta' => ['reason' => 'offer_stale'],
            ]);
        }

        $reply = $prefix.' Te ofrezco estas otras opciones:'."\n"
            .$this->renderOptions($slots)
            ."\nDime cuál prefieres y la confirmo tras verificar la disponibilidad.";

        return $this->finish($conversation, $reply, [
            'slots' => $slots,
            'meta' => [
                'reason' => 'slots_offered',
                'pending' => [
                    'type' => $type,
                    'service' => $service,
                    'professional_id' => $professional->id,
                    'date' => $slots[0]['date'],
                    'slots' => $slots,
                    'appointment_id' => $pending['appointment_id'] ?? null,
                    'modality' => $pending['modality'] ?? null,
                ],
            ],
        ]);
    }

    /**
     * Cancela la próxima cita cancelable del paciente a través del agente.
     *
     * @return array<string,mixed>
     */
    private function handleCancel(ChatConversation $conversation, string $message, ?User $user): array
    {
        $gate = $this->bookingGate($conversation, $user);
        if ($gate !== null) {
            return $gate;
        }

        /** @var User $user */
        $appointment = $this->nextCancellableAppointment($user);

        if ($appointment === null) {
            return $this->finish($conversation, 'No encontré ninguna cita que se pueda cancelar en este momento. '
                .'Recuerda que la cancelación debe hacerse con al menos 5 días de anticipación.', [
                    'meta' => ['reason' => 'cancel_none'],
                ]);
        }

        try {
            $this->tools->cancel($appointment, $user);

            $when = $appointment->date->format('Y-m-d').' a las '.Carbon::parse($appointment->time)->format('H:i');
            $reply = "Cancelé tu cita del {$when}. Si deseas, puedo ayudarte a agendar una nueva.";

            return $this->finish($conversation, $reply, ['meta' => ['reason' => 'cancelled']]);
        } catch (ValidationException $e) {
            $reply = collect($e->errors())->flatten()->first() ?: 'No fue posible cancelar la cita.';

            return $this->finish($conversation, (string) $reply, ['meta' => ['reason' => 'cancel_failed']]);
        }
    }

    /**
     * Inicia la reprogramación de la próxima cita del paciente ofreciendo
     * cupos reales; un turno de seguimiento confirma la nueva fecha/hora.
     *
     * @return array<string,mixed>
     */
    private function handleReschedule(ChatConversation $conversation, string $message, ?User $user): array
    {
        $gate = $this->bookingGate($conversation, $user);
        if ($gate !== null) {
            return $gate;
        }

        /** @var User $user */
        $appointment = $this->nextModifiableAppointment($user);

        if ($appointment === null) {
            return $this->finish($conversation, 'No encontré una cita que se pueda reprogramar. '
                .'Solo es posible dentro de las primeras 24 horas desde que se agendó.', [
                    'meta' => ['reason' => 'reschedule_none'],
                ]);
        }

        $professional = $appointment->professional;
        $service = (string) ($professional->profession ?? $professional->specialty);

        // Mismo piso 2-4 que el flujo de oferta: primero el profesional de la
        // cita, luego otros del mismo servicio. Cupos siempre reales.
        $slots = $this->collectRescheduleSlots($professional, $service);

        if ($slots === []) {
            return $this->finish($conversation, $this->noAvailabilityText(), ['meta' => ['reason' => 'no_availability']]);
        }

        $reply = "Puedo reprogramar tu cita con {$professional->name}. Estas son las opciones disponibles:\n"
            .$this->renderOptions($slots)
            ."\nDime cuál prefieres y la reprogramo tras verificar la disponibilidad.";

        $reply = $this->phrase($conversation, $message, $reply, $user);

        return $this->finish($conversation, $reply, [
            'slots' => $slots,
            'meta' => [
                'reason' => 'slots_offered',
                'pending' => [
                    'type' => 'reschedule',
                    'service' => $service,
                    'professional_id' => $professional->id,
                    'appointment_id' => $appointment->id,
                    'date' => $slots[0]['date'],
                    'slots' => $slots,
                    'modality' => $appointment->modality?->value,
                ],
            ],
        ]);
    }

    /**
     * Alta en lista de espera cuando el paciente lo pide (o no hay cupo).
     *
     * @return array<string,mixed>
     */
    private function handleWaitlist(ChatConversation $conversation, string $message, ?User $user): array
    {
        $gate = $this->bookingGate($conversation, $user);
        if ($gate !== null) {
            return $gate;
        }

        /** @var User $user */
        $service = $this->detectService($message) ?? $this->lastOfferedService($conversation);

        if ($service === null) {
            return $this->finish($conversation, 'Con gusto te anoto en la lista de espera. '
                .'¿Para qué servicio deseas que te avise cuando se libere un cupo?', [
                    'meta' => ['reason' => 'waitlist_need_service'],
                ]);
        }

        $professional = $this->tools->professionalsFor($service)->first();

        if (! $professional instanceof Professional) {
            return $this->finish($conversation, $this->noAvailabilityText(), ['meta' => ['reason' => 'no_availability']]);
        }

        $this->tools->joinWaitlist($user, $professional);

        $reply = "Te anoté en la lista de espera para {$service} con {$professional->name}. "
            .'Te avisaremos apenas se libere un cupo. ¿Puedo ayudarte con algo más?';

        return $this->finish($conversation, $reply, ['meta' => ['reason' => 'waitlisted']]);
    }

    private function nextCancellableAppointment(User $user): ?Appointment
    {
        return $user->appointmentsAsPatient()
            ->whereIn('status', [AppointmentStatus::Pending->value, AppointmentStatus::Confirmed->value])
            ->orderBy('date')
            ->orderBy('time')
            ->get()
            ->first(fn (Appointment $a) => $a->canBeCancelledByPatient());
    }

    private function nextModifiableAppointment(User $user): ?Appointment
    {
        return $user->appointmentsAsPatient()
            ->whereIn('status', [AppointmentStatus::Pending->value, AppointmentStatus::Confirmed->value])
            ->orderBy('date')
            ->orderBy('time')
            ->get()
            ->first(fn (Appointment $a) => $a->canBeModifiedByPatient());
    }

    /**
     * Ofrece entre 2 y 4 cupos reales para el primer profesional disponible del
     * servicio detectado. Nunca inventa horarios ni profesionales: garantiza un
     * mínimo de 2 opciones SOLO cuando existen de verdad (tomándolas de la
     * siguiente fecha o de otro profesional del mismo servicio).
     *
     * @return array{0:array<int,array<string,mixed>>,1:?string,2:array<string,mixed>}
     */
    private function offerRealSlots(string $message): array
    {
        $service = $this->detectService($message);

        if ($service === null) {
            return [[], 'Con gusto te ayudo a agendar. ¿Para qué servicio te gustaría la cita? '
                .$this->servicesSummary(), []];
        }

        $professionals = $this->tools->professionalsFor($service);

        if ($professionals->isEmpty()) {
            return [[], $this->noAvailabilityText(), []];
        }

        $offer = $this->buildSlotOffer($professionals, $service);

        if ($offer === null) {
            return [[], $this->noAvailabilityText(), []];
        }

        [$slots, $professionalId] = $offer;

        $reply = "Para {$service} tengo estas opciones de cupo:\n"
            .$this->renderOptions($slots)
            ."\nDime cuál prefieres y la confirmo tras verificar la disponibilidad.";

        return [$slots, $reply, [
            'pending' => [
                'type' => 'book',
                'service' => $service,
                'professional_id' => $professionalId,
                'slots' => $slots,
            ],
        ]];
    }

    /**
     * Arma de 2 a 4 cupos reales. Prioriza el primer profesional del servicio;
     * si en su primera fecha solo hay 1 cupo, completa con la siguiente fecha
     * del MISMO profesional y, si aún falta, con otro profesional del servicio.
     * Si en total solo existe 1 cupo real, lo ofrece como única opción.
     *
     * @param  Collection<int,Professional>  $professionals
     * @return array{0:array<int,array<string,mixed>>,1:int}|null
     */
    private function buildSlotOffer(Collection $professionals, string $service): ?array
    {
        $collected = $this->collectRealSlots($professionals, $service);

        if ($collected === []) {
            return null;
        }

        return [$collected, (int) $collected[0]['professional_id']];
    }

    /**
     * Colecciona entre 1 y MAX_SLOTS cupos REALES recorriendo los profesionales
     * en el orden recibido, y para cada uno todas sus fechas disponibles. Nunca
     * inventa horarios: cada cupo proviene de AvailabilityService (vía
     * ChatTools::getSlots). El orden de la colección determina la prioridad: el
     * llamador pasa el profesional preferido primero (p.ej. el de la cita a
     * reprogramar), seguido de otros profesionales del mismo servicio. Si en
     * total solo existe 1 cupo real, se devuelve esa única opción (sin rellenar).
     *
     * @param  Collection<int,Professional>  $professionals
     * @return array<int,array<string,mixed>>
     */
    private function collectRealSlots(Collection $professionals, string $service): array
    {
        $collected = [];

        foreach ($professionals as $professional) {
            $dates = $this->tools->getAvailableDates($professional);

            foreach ($dates as $date) {
                $times = $this->tools->getSlots($professional, $date);

                foreach ($times as $time) {
                    $collected[] = [
                        'professional_id' => $professional->id,
                        'professional' => $professional->name,
                        'service' => $service,
                        'date' => $date,
                        'time' => $time,
                    ];

                    if (count($collected) >= ChatTools::MAX_SLOTS) {
                        return $collected;
                    }
                }
            }
        }

        return $collected;
    }

    /**
     * Cupos reales para reprogramar / re-ofrecer, honrando el mismo piso 2-4 que
     * el flujo de oferta: toma primero al profesional indicado (el de la cita) y
     * luego a otros profesionales del MISMO servicio, hasta MAX_SLOTS. Nunca
     * inventa: si solo existe 1 cupo real en total, devuelve esa única opción.
     *
     * @return array<int,array<string,mixed>>
     */
    private function collectRescheduleSlots(Professional $professional, string $service): array
    {
        $ordered = collect([$professional])
            ->concat($this->tools->professionalsFor($service)
                ->reject(fn (Professional $p) => $p->id === $professional->id))
            ->values();

        return $this->collectRealSlots($ordered, $service);
    }

    /**
     * @param  array<int,array<string,mixed>>  $slots
     */
    private function renderOptions(array $slots): string
    {
        return collect($slots)
            ->map(fn (array $s) => "• Opción: {$s['date']} a las {$s['time']} con {$s['professional']}")
            ->implode("\n");
    }

    /**
     * Intenta mapear el mensaje del usuario a uno de los cupos ofrecidos:
     * por ordinal ("la primera"), por hora ("las 9:00"), por fecha, o una
     * confirmación genérica ("sí") cuando solo hay una opción.
     *
     * @param  array<int,array<string,mixed>>  $slots
     * @return array<string,mixed>|null
     */
    private function matchSelection(string $message, array $slots): ?array
    {
        $norm = $this->normalize($message);

        // 1) Por hora exacta (09:00, 9:00, 9 am, "las 9").
        if (preg_match('/\b(\d{1,2})(?::(\d{2}))?\s*(am|pm|h|hrs)?\b/', $norm, $m)) {
            $hour = (int) $m[1];
            $min = isset($m[2]) ? (int) $m[2] : 0;
            $candidate = sprintf('%02d:%02d', $hour, $min);
            foreach ($slots as $slot) {
                if (($slot['time'] ?? null) === $candidate) {
                    return $slot;
                }
            }
            // Solo hora sin minutos: match por prefijo de hora.
            foreach ($slots as $slot) {
                if (is_string($slot['time'] ?? null) && str_starts_with($slot['time'], sprintf('%02d:', $hour))) {
                    return $slot;
                }
            }
        }

        // 2) Por ordinal.
        $ordinals = [
            1 => ['primera', 'primero', 'uno', '1', 'la 1'],
            2 => ['segunda', 'segundo', 'dos', '2', 'la 2'],
            3 => ['tercera', 'tercero', 'tres', '3', 'la 3'],
            4 => ['cuarta', 'cuarto', 'cuatro', '4', 'la 4'],
        ];
        foreach ($ordinals as $index => $words) {
            foreach ($words as $w) {
                if (str_contains($norm, $this->normalize($w)) && isset($slots[$index - 1])) {
                    return $slots[$index - 1];
                }
            }
        }

        // 3) Confirmación genérica cuando solo hay una opción.
        if (count($slots) === 1 && $this->matchesAny($message, ['si', 'sí', 'confirmo', 'dale', 'listo', 'de acuerdo', 'esa', 'ese', 'esa misma'])) {
            return $slots[0];
        }

        return null;
    }

    private function resolveModality(array $pending, string $message): Modality
    {
        if ($this->matchesAny($message, ['virtual', 'en linea', 'en línea', 'videollamada', 'meet', 'remoto'])) {
            return Modality::Virtual;
        }

        if ($this->matchesAny($message, ['presencial', 'en persona', 'en sede', 'en el consultorio'])) {
            return Modality::InPerson;
        }

        $stored = $pending['modality'] ?? null;
        if (is_string($stored)) {
            return Modality::tryFrom($stored) ?? Modality::InPerson;
        }

        return Modality::InPerson;
    }

    private function lastOfferedService(ChatConversation $conversation): ?string
    {
        $pending = $conversation->pendingAction();

        return is_array($pending) ? ($pending['service'] ?? null) : null;
    }

    private function availability(): AvailabilityService
    {
        return app(AvailabilityService::class);
    }

    /**
     * Si OpenRouter está configurado, se le pide que REDACTE a partir del texto
     * determinista ya calculado (nunca decide reglas ni datos). Si no, devuelve
     * el determinista tal cual.
     */
    private function phrase(ChatConversation $conversation, string $userMessage, string $deterministic, ?User $user): string
    {
        if (! $this->openRouter->isConfigured()) {
            return $deterministic;
        }

        $messages = array_merge(
            [['role' => 'system', 'content' => $this->systemPrompt()]],
            $conversation->recentHistory((int) config('chat.history_window', 12)),
            [[
                'role' => 'system',
                'content' => 'Responde al usuario en español, de forma cálida y neutra, usando EXACTAMENTE '
                    .'la siguiente información verificada (no inventes datos, horarios ni servicios): '
                    .$deterministic,
            ]],
            [['role' => 'user', 'content' => $userMessage]],
        );

        $reply = $this->openRouter->chat($messages);

        if ($reply === null || trim($reply) === '') {
            return $deterministic;
        }

        // Guardia anti-alucinación: el modelo solo puede REDACTAR el texto
        // determinista, nunca introducir datos que el código no calculó. Si la
        // respuesta del modelo introduce una hora (HH:MM), una fecha
        // (YYYY-MM-DD) o un nombre de profesional (anclado a un título, p.ej.
        // "Dra. Prueba") ausente del texto verificado, se descarta y se usa el
        // determinista tal cual.
        if (! $this->phrasedOutputIsSafe($reply, $deterministic)) {
            return $deterministic;
        }

        return $reply;
    }

    /**
     * Verifica que el texto redactado por el modelo no introduzca datos
     * estructurados ausentes del payload determinista. Comprueba tres clases de
     * token que el modelo solo podría obtener del texto verificado:
     *
     *   - Horas de reloj (HH:MM): un cupo que la agenda nunca ofreció.
     *   - Fechas ISO (YYYY-MM-DD): una fecha corrida o inventada.
     *   - Nombres de profesional: tokens capitalizados (incluye títulos como
     *     "Dra."/"Dr.") que no aparezcan en el determinista.
     *
     * Es deliberadamente conservadora: solo marca tokens que "parecen" datos
     * estructurados, para no rechazar la redacción normal en español. Devuelve
     * false si la respuesta del modelo diverge del payload verificado.
     */
    private function phrasedOutputIsSafe(string $modelReply, string $deterministic): bool
    {
        $extractTimes = static function (string $text): array {
            preg_match_all('/\b(\d{1,2}:\d{2})\b/', $text, $m);

            return array_map(fn (string $t) => ltrim($t, '0'), $m[1] ?? []);
        };

        $extractDates = static function (string $text): array {
            preg_match_all('/\b(\d{4}-\d{2}-\d{2})\b/', $text, $m);

            return $m[1] ?? [];
        };

        // Horas.
        foreach ($extractTimes($modelReply) as $time) {
            if (! in_array($time, $extractTimes($deterministic), true)) {
                return false;
            }
        }

        // Fechas ISO.
        foreach ($extractDates($modelReply) as $date) {
            if (! in_array($date, $extractDates($deterministic), true)) {
                return false;
            }
        }

        // Nombres de profesional: se anclan a un título clínico ("Dra.", "Dr.",
        // "Lic.", "Psic.", etc.) seguido del nombre. Cada nombre así construido
        // en la respuesta del modelo debe existir, normalizado, en el
        // determinista. Es conservador a propósito: NO inspecciona palabras
        // capitalizadas sueltas (inicio de oración, etc.), solo nombres que
        // siguen a un título, que es la única forma en que el agente nombra a
        // un profesional.
        $allowedNames = $this->titledProfessionalNames($deterministic);

        foreach ($this->titledProfessionalNames($modelReply) as $name) {
            if (! in_array($name, $allowedNames, true)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Extrae nombres de profesional anclados a un título clínico abreviado
     * (p.ej. "Dra. Prueba", "Dr. Juan Pérez"). Devuelve el nombre normalizado
     * (sin el título, minúsculas, sin tildes) para comparar. Solo reconoce la
     * forma "<título>. <Nombre[ Apellido...]>", que es como el agente nombra a
     * un profesional; así evita falsos positivos con la prosa normal.
     *
     * @return array<int,string>
     */
    private function titledProfessionalNames(string $text): array
    {
        $titles = '(?:[Dd]ra?|[Ll]ic|[Pp]sic|[Pp]siq|[Dd]r|[Dd][ñn]a|[Dd]o[nñ]a)';
        preg_match_all(
            '/\b'.$titles.'\.?\s+((?:[A-ZÁÉÍÓÚÑÜ][\p{L}]+)(?:\s+[A-ZÁÉÍÓÚÑÜ][\p{L}]+)*)/u',
            $text,
            $m
        );

        return collect($m[1] ?? [])
            ->map(fn (string $t) => $this->normalize($t))
            ->filter(fn (string $t) => $t !== '')
            ->unique()
            ->values()
            ->all();
    }

    private function deterministicReply(string $message): string
    {
        $service = $this->detectService($message);

        if ($service !== null) {
            $professionals = $this->tools->professionalsFor($service);
            if ($professionals->isEmpty()) {
                return $this->noAvailabilityText();
            }

            return "Ofrecemos {$service}. Si deseas agendar una cita, con gusto te muestro los cupos "
                .'disponibles. También puedo ayudarte con información general.';
        }

        if ($this->looksLikeGreeting($message)) {
            return $this->greetingText();
        }

        return $this->greetingText().' '.$this->servicesSummary();
    }

    private function servicesSummary(): string
    {
        $available = collect($this->tools->listServices())
            ->filter(fn (array $s) => $s['available'])
            ->pluck('service');

        if ($available->isEmpty()) {
            return 'En este momento no tenemos profesionales disponibles; puedo anotarte en lista de espera.';
        }

        return 'Estos son nuestros servicios con disponibilidad: '.$available->implode(', ').'.';
    }

    // ---- Detección (heurísticas en español) ------------------------------

    /**
     * Detección de crisis en TRES capas, con sesgo deliberado a FALLAR-SEGURO
     * (preferir un falso positivo a dejar pasar una crisis real). El orden es
     * crítico y es lo que hace el diseño seguro: el modelo SOLO puede ELEVAR la
     * bandera cuando las capas deterministas no la dispararon; NUNCA puede
     * bajarla.
     *
     *   1. Piso determinista: lista de palabras/frases exactas
     *      (config('chat.crisis_keywords')). Un match aquí SIEMPRE dispara y
     *      NO puede ser anulado por nada (ni por negaciones ni por el modelo).
     *   2. Capa de refuerzo determinista: expresiones regulares sobre el texto
     *      normalizado (config('chat.crisis_patterns')) que capturan paráfrasis.
     *      Esta capa SOLO eleva la señal; se descarta únicamente ante una
     *      negación directa de la ideación (config 'chat.crisis_pattern_negations').
     *   3. Clasificador asistido por el modelo (OPCIONAL, solo eleva): se
     *      consulta ÚNICAMENTE cuando las dos capas deterministas NO dispararon.
     *      Si cualquiera de las capas deterministas ya marcó crisis, se retorna
     *      true de inmediato y el modelo NO se consulta siquiera, de modo que el
     *      modelo jamás puede des-marcar un hit de palabra/patrón. Si no hay
     *      key, hay error de red/HTTP, respuesta inparseable, o el clasificador
     *      responde no-crisis, se cae al resultado determinista (no eleva).
     */
    private function detectsCrisis(string $message): bool
    {
        // Capa 1: piso de palabras clave exactas. Incondicional.
        if ($this->matchesAny($message, (array) config('chat.crisis_keywords', []))) {
            return true;
        }

        // Capa 2: paráfrasis por regex. Solo eleva la señal; puede atenuarse
        // ante una negación directa, nunca anula la capa 1 (ya evaluada arriba).
        if ($this->matchesCrisisPattern($message)) {
            return true;
        }

        // Capa 3: clasificador del modelo, SOLO para elevar una ausencia de las
        // capas deterministas. Nunca se alcanza si una capa determinista
        // disparó (short-circuit arriba), así el modelo jamás baja la bandera.
        return $this->classifierDetectsCrisis($message);
    }

    /**
     * Capa 3 (escalate-only): consulta al modelo para clasificar el mensaje
     * como crisis / no-crisis en contexto de salud mental colombiano. Solo se
     * invoca cuando las capas deterministas no dispararon. Fail-safe total:
     *
     *   - Si el toggle 'chat.crisis_classifier_enabled' está desactivado,
     *     o OpenRouter no está configurado (sin OPENROUTER_API_KEY), NO se
     *     hace ninguna llamada de red y se devuelve false (no eleva).
     *   - Cualquier fallo de red/HTTP o respuesta vacía => OpenRouterClient
     *     devuelve null => false.
     *   - Respuesta inparseable o clasificación no-crisis => false.
     *
     * Nunca lanza, nunca baja una señal determinista (no puede: solo se llega
     * aquí si ninguna capa determinista disparó).
     */
    private function classifierDetectsCrisis(string $message): bool
    {
        if (! (bool) config('chat.crisis_classifier_enabled', true)) {
            return false;
        }

        if (! $this->openRouter->isConfigured()) {
            return false;
        }

        $raw = $this->openRouter->classify((string) config('chat.crisis_classifier_prompt', ''), $message);

        if (! is_string($raw) || trim($raw) === '') {
            return false;
        }

        return $this->parseCrisisClassification($raw);
    }

    /**
     * Parsea la salida del clasificador buscando una señal de crisis. Acepta
     * un JSON {"crisis": true|false} o un token simple (CRISIS / NO_CRISIS /
     * SI / NO). Conservador: solo devuelve true ante una señal afirmativa
     * explícita; cualquier otra cosa (inparseable, ambigua, negativa) => false.
     */
    private function parseCrisisClassification(string $raw): bool
    {
        $text = trim($raw);

        // 1) Intento JSON: {"crisis": true}.
        $decoded = json_decode($text, true);
        if (is_array($decoded) && array_key_exists('crisis', $decoded)) {
            return filter_var($decoded['crisis'], FILTER_VALIDATE_BOOLEAN);
        }

        // 2) Token afirmativo explícito en texto normalizado.
        $normalized = $this->normalize($text);

        if ($normalized === 'no_crisis' || $normalized === 'no crisis' || $normalized === 'no') {
            return false;
        }

        return in_array($normalized, ['crisis', 'si', 'true'], true);
    }

    private function matchesCrisisPattern(string $message): bool
    {
        $normalized = $this->normalize($message);

        if ($normalized === '') {
            return false;
        }

        $matched = false;
        foreach ((array) config('chat.crisis_patterns', []) as $pattern) {
            if (! is_string($pattern) || $pattern === '') {
                continue;
            }

            if (@preg_match($pattern, $normalized) === 1) {
                $matched = true;
                break;
            }
        }

        if (! $matched) {
            return false;
        }

        // Negación directa de la ideación: atenúa el patrón (no la capa 1).
        foreach ((array) config('chat.crisis_pattern_negations', []) as $negation) {
            $n = $this->normalize((string) $negation);
            if ($n !== '' && str_contains($normalized, $n)) {
                return false;
            }
        }

        return true;
    }

    private function requiresHumanEscalation(string $message): bool
    {
        return $this->matchesAny($message, (array) config('chat.escalation_keywords', []));
    }

    private function intendsToBook(string $message): bool
    {
        return $this->matchesAny($message, [
            'agendar', 'agenda', 'cita', 'reservar', 'reserva', 'turno',
            'cuándo', 'cuando puedo', 'disponibilidad', 'cupo', 'cupos', 'horario',
        ]);
    }

    private function intendsToCancel(string $message): bool
    {
        return $this->matchesAny($message, [
            'cancelar mi cita', 'cancelar la cita', 'cancelar cita', 'anular mi cita',
            'anular la cita', 'anular cita', 'quiero cancelar', 'cancela mi cita',
        ]);
    }

    private function intendsToReschedule(string $message): bool
    {
        return $this->matchesAny($message, [
            'reprogramar', 'reagendar', 'cambiar mi cita', 'cambiar la cita',
            'mover mi cita', 'mover la cita', 'cambiar la hora', 'cambiar la fecha',
            'otro horario para mi cita',
        ]);
    }

    private function intendsWaitlist(string $message): bool
    {
        return $this->matchesAny($message, [
            'lista de espera', 'anotarme en lista', 'anotame en lista', 'ponme en lista',
            'avisame cuando', 'avísame cuando', 'quiero esperar un cupo',
        ]);
    }

    private function looksLikeGreeting(string $message): bool
    {
        return $this->matchesAny($message, ['hola', 'buenas', 'buenos días', 'buenos dias', 'buenas tardes', 'buenas noches', 'saludos']);
    }

    private function detectService(string $message): ?string
    {
        $needle = $this->normalize($message);

        foreach ((array) config('chat.services', []) as $service) {
            if (str_contains($needle, $this->normalize($service))) {
                return $service;
            }
        }

        // Sinónimos comunes.
        $synonyms = [
            'Psicología' => ['psicolog', 'psicolo'],
            'Psiquiatría' => ['psiquiatr'],
            'Neuropsicología' => ['neuropsico'],
            'Terapia Ocupacional' => ['ocupacional'],
            'Terapia Física' => ['fisica', 'física', 'fisioterap'],
        ];

        foreach ($synonyms as $service => $keys) {
            foreach ($keys as $key) {
                if (str_contains($needle, $this->normalize($key))) {
                    return $service;
                }
            }
        }

        return null;
    }

    private function matchesAny(string $message, array $needles): bool
    {
        $haystack = $this->normalize($message);

        foreach ($needles as $needle) {
            $n = $this->normalize((string) $needle);
            if ($n !== '' && str_contains($haystack, $n)) {
                return true;
            }
        }

        return false;
    }

    private function normalize(string $value): string
    {
        $value = mb_strtolower(trim($value));
        $map = ['á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ñ' => 'n', 'ü' => 'u'];

        return strtr($value, $map);
    }

    // ---- Textos configurables (SystemSetting override -> config) ---------

    private function crisisText(): string
    {
        $keys = (array) config('chat.settings_keys', []);
        $override = SystemSetting::getValue($keys['crisis'] ?? 'chat.crisis_text');
        if (is_string($override) && trim($override) !== '') {
            return $override;
        }

        $emergency = (string) config('chat.emergency_line', '123');
        $crisisLine = $this->crisisLine();

        $template = (string) config('chat.crisis_copy');

        return strtr($template, [
            ':emergency_line' => $emergency,
            ':crisis_line' => $crisisLine,
        ]);
    }

    private function crisisLine(): string
    {
        $keys = (array) config('chat.settings_keys', []);
        $override = SystemSetting::getValue($keys['crisis_line'] ?? 'chat.crisis_line');
        if (is_string($override) && trim($override) !== '') {
            return $override;
        }

        return (string) config('chat.crisis_line', '106');
    }

    private function escalationText(): string
    {
        $keys = (array) config('chat.settings_keys', []);
        $override = SystemSetting::getValue($keys['escalation'] ?? 'chat.escalation_text');
        if (is_string($override) && trim($override) !== '') {
            return $override;
        }

        return (string) config('chat.escalation_copy');
    }

    private function consentText(): string
    {
        $keys = (array) config('chat.settings_keys', []);
        $override = SystemSetting::getValue($keys['consent'] ?? 'chat.consent_text');
        if (is_string($override) && trim($override) !== '') {
            return $override;
        }

        return (string) config('chat.consent_copy');
    }

    private function guestBookingText(): string
    {
        return (string) config('chat.guest_booking_copy');
    }

    private function greetingText(): string
    {
        return (string) config('chat.greeting_copy');
    }

    private function noAvailabilityText(): string
    {
        return (string) config('chat.no_availability_copy');
    }

    private function systemPrompt(): string
    {
        return 'Eres el asistente conversacional de Mentea, un hospital de salud mental en Colombia. '
            .'Hablas siempre en español, con tono cálido, respetuoso y neutro por privacidad. '
            .'NO diagnosticas ni das consejo clínico. NUNCA inventas horarios, profesionales ni servicios: '
            .'solo usas la información verificada que te entrega el sistema. Pides consentimiento de datos '
            .'(Ley 1581 de 2012) antes de agendar. Ante señales de crisis entregas el protocolo de crisis y '
            .'escalas a una persona. Tu rol es redactar con claridad, no decidir reglas de negocio.';
    }

    // ---- Persistencia ----------------------------------------------------

    private function persist(ChatConversation $conversation, string $role, string $content, array $meta = []): ChatMessage
    {
        $conversation->forceFill(['last_activity_at' => now()])->save();

        return $conversation->messages()->create([
            'role' => $role,
            'content' => $content,
            'meta' => $meta ?: null,
        ]);
    }

    /**
     * @param  array<string,mixed>  $state
     * @return array{reply:string, conversation_id:int, crisis:bool, escalated:bool, requires_consent:bool, requires_login:bool, slots:array<int,array<string,mixed>>}
     */
    private function finish(ChatConversation $conversation, string $reply, array $state): array
    {
        $this->persist($conversation, 'assistant', $reply, (array) ($state['meta'] ?? []));

        return [
            'reply' => $reply,
            'conversation_id' => $conversation->id,
            'crisis' => (bool) ($state['crisis'] ?? false),
            'escalated' => (bool) ($state['escalated'] ?? false),
            'requires_consent' => (bool) ($state['requires_consent'] ?? false),
            'requires_login' => (bool) ($state['requires_login'] ?? false),
            'slots' => (array) ($state['slots'] ?? []),
        ];
    }
}
