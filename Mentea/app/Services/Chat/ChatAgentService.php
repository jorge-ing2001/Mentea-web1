<?php

namespace App\Services\Chat;

use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\Professional;
use App\Models\SystemSetting;
use App\Models\User;

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
     * @return array{reply:string, conversation_id:int, crisis:bool, escalated:bool, requires_consent:bool, slots:array<int,array<string,mixed>>}
     */
    public function handle(ChatConversation $conversation, string $userMessage, ?User $user = null): array
    {
        $userMessage = trim($userMessage);

        // 1) Persistir el mensaje entrante.
        $this->persist($conversation, 'user', $userMessage);

        $crisis = false;
        $escalated = false;
        $requiresConsent = false;
        $slots = [];
        $meta = [];

        // 2) REGLA DURA: crisis primero. Corta todo el flujo de agendamiento.
        if ($this->detectsCrisis($userMessage)) {
            $crisis = true;
            $escalated = true;
            $reply = $this->crisisText();

            $conversation->forceFill([
                'crisis_flagged_at' => now(),
                'escalated_at' => now(),
            ])->save();

            return $this->finish($conversation, $reply, compact('crisis', 'escalated', 'requiresConsent', 'slots') + ['meta' => ['reason' => 'crisis']]);
        }

        // 3) REGLA DURA: escalado a humano (clínico / menores / fuera de alcance).
        if ($this->requiresHumanEscalation($userMessage)) {
            $escalated = true;
            $reply = $this->escalationText();

            $conversation->forceFill(['escalated_at' => now()])->save();

            return $this->finish($conversation, $reply, compact('crisis', 'escalated', 'requiresConsent', 'slots') + ['meta' => ['reason' => 'escalation']]);
        }

        $wantsToBook = $this->intendsToBook($userMessage);

        // 4) REGLA DURA: consentimiento antes de recolectar datos / agendar.
        if ($wantsToBook) {
            if (! $user instanceof User || ! $user->isPatient()) {
                $requiresConsent = true;
                $reply = $this->guestBookingText();

                return $this->finish($conversation, $reply, compact('crisis', 'escalated', 'requiresConsent', 'slots') + ['meta' => ['reason' => 'guest_booking']]);
            }

            if (! $user->hasInformedConsent()) {
                $requiresConsent = true;
                $reply = $this->consentText();

                return $this->finish($conversation, $reply, compact('crisis', 'escalated', 'requiresConsent', 'slots') + ['meta' => ['reason' => 'consent']]);
            }

            // Consentimiento OK: ofrecer cupos REALES (2-4) si hay servicio detectable.
            [$slots, $bookingReply] = $this->offerRealSlots($userMessage);

            if ($bookingReply !== null) {
                $reply = $this->phrase($conversation, $userMessage, $bookingReply, $user);

                return $this->finish($conversation, $reply, compact('crisis', 'escalated', 'requiresConsent', 'slots') + ['meta' => ['reason' => 'slots_offered']]);
            }
        }

        // 5) Conversación general: listar servicios / saludo, redactado por el
        //    modelo si está configurado, con fallback determinista si no.
        $deterministic = $this->deterministicReply($userMessage);
        $reply = $this->phrase($conversation, $userMessage, $deterministic, $user);

        return $this->finish($conversation, $reply, compact('crisis', 'escalated', 'requiresConsent', 'slots') + ['meta' => $meta]);
    }

    /**
     * Ofrece entre 2 y 4 cupos reales para el primer profesional disponible del
     * servicio detectado. Nunca inventa horarios ni profesionales.
     *
     * @return array{0:array<int,array<string,mixed>>,1:?string}
     */
    private function offerRealSlots(string $message): array
    {
        $service = $this->detectService($message);

        if ($service === null) {
            return [[], 'Con gusto te ayudo a agendar. ¿Para qué servicio te gustaría la cita? '
                .$this->servicesSummary()];
        }

        $professionals = $this->tools->professionalsFor($service);

        if ($professionals->isEmpty()) {
            return [[], $this->noAvailabilityText()];
        }

        /** @var Professional $professional */
        $professional = $professionals->first();
        $dates = $this->tools->getAvailableDates($professional);

        if ($dates->isEmpty()) {
            return [[], $this->noAvailabilityText()];
        }

        $date = $dates->first();
        $times = $this->tools->getSlots($professional, $date);

        if (count($times) === 0) {
            return [[], $this->noAvailabilityText()];
        }

        $slots = array_map(fn (string $time) => [
            'professional_id' => $professional->id,
            'professional' => $professional->name,
            'service' => $service,
            'date' => $date,
            'time' => $time,
        ], $times);

        $optionsText = collect($times)
            ->map(fn (string $t) => "• {$date} a las {$t}")
            ->implode("\n");

        $reply = "Para {$service} con {$professional->name} tengo estas opciones de cupo:\n"
            .$optionsText
            ."\nDime cuál prefieres y la confirmo tras verificar la disponibilidad.";

        return [$slots, $reply];
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

        return $reply ?? $deterministic;
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

    private function detectsCrisis(string $message): bool
    {
        return $this->matchesAny($message, (array) config('chat.crisis_keywords', []));
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
     * @return array{reply:string, conversation_id:int, crisis:bool, escalated:bool, requires_consent:bool, slots:array<int,array<string,mixed>>}
     */
    private function finish(ChatConversation $conversation, string $reply, array $state): array
    {
        $this->persist($conversation, 'assistant', $reply, (array) ($state['meta'] ?? []));

        return [
            'reply' => $reply,
            'conversation_id' => $conversation->id,
            'crisis' => (bool) ($state['crisis'] ?? false),
            'escalated' => (bool) ($state['escalated'] ?? false),
            'requires_consent' => (bool) ($state['requiresConsent'] ?? false),
            'slots' => (array) ($state['slots'] ?? []),
        ];
    }
}
