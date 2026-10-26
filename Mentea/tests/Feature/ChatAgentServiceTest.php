<?php

namespace Tests\Feature;

use App\Enums\AppointmentStatus;
use App\Enums\Modality;
use App\Enums\Role;
use App\Models\ChatConversation;
use App\Models\Professional;
use App\Models\Schedule;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\AvailabilityService;
use App\Services\Chat\ChatAgentService;
use App\Services\Chat\ChatTools;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Factory;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ChatAgentServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Por defecto: sin red real. Ningún test requiere OpenRouter en vivo.
        Http::fake();
    }

    private function agent(): ChatAgentService
    {
        return app(ChatAgentService::class);
    }

    /**
     * Reemplaza el fake vacío de setUp por uno que devuelve una respuesta de
     * OpenRouter concreta. setUp registra un catch-all vacío; para que este
     * stub tenga efecto se descarta la instancia previa del cliente HTTP.
     */
    private function fakeOpenRouterReply(string $content): void
    {
        Http::clearResolvedInstances();
        $this->app->forgetInstance(Factory::class);

        Http::fake([
            '*' => Http::response([
                'choices' => [[
                    'message' => ['content' => $content],
                ]],
            ], 200),
        ]);
    }

    private function conversationFor(?User $user = null): ChatConversation
    {
        return ChatConversation::create([
            'user_id' => $user?->id,
            'session_id' => $user ? null : 'guest-session',
            'status' => 'active',
        ]);
    }

    private function seedProfessional(): Professional
    {
        $pro = Professional::create([
            'name' => 'Dra. Prueba',
            'specialty' => 'Psicología Clínica',
            'profession' => 'Psicología',
            'email' => 'pro-chat@test.com',
            'is_available' => true,
            'appointment_duration_minutes' => 45,
        ]);

        foreach (range(1, 7) as $day) {
            Schedule::create([
                'professional_id' => $pro->id,
                'day_of_week' => $day,
                'start_time' => '09:00',
                'end_time' => '12:00',
                'is_active' => true,
            ]);
        }

        return $pro;
    }

    private function patient(bool $withConsent): User
    {
        return User::factory()->create([
            'role' => Role::Patient,
            'is_active' => true,
            'consent_informed_at' => $withConsent ? now() : null,
        ]);
    }

    public function test_consent_gate_blocks_booking_without_consent(): void
    {
        $this->seedProfessional();
        $patient = $this->patient(withConsent: false);
        $conversation = $this->conversationFor($patient);

        $result = $this->agent()->handle($conversation, 'Quiero agendar una cita de psicología', $patient);

        $this->assertTrue($result['requires_consent']);
        $this->assertEmpty($result['slots']);
        $this->assertStringContainsString('consentimiento', mb_strtolower($result['reply']));
        $this->assertDatabaseCount('appointments', 0);
    }

    public function test_patient_with_consent_is_offered_real_slots(): void
    {
        $this->seedProfessional();
        $patient = $this->patient(withConsent: true);
        $conversation = $this->conversationFor($patient);

        $result = $this->agent()->handle($conversation, 'Quiero agendar una cita de psicología', $patient);

        $this->assertFalse($result['requires_consent']);
        $this->assertNotEmpty($result['slots']);
    }

    public function test_never_invents_slots_and_offers_between_two_and_four(): void
    {
        $pro = $this->seedProfessional();
        $patient = $this->patient(withConsent: true);
        $conversation = $this->conversationFor($patient);

        $result = $this->agent()->handle($conversation, 'Necesito agendar psicología', $patient);

        $slots = $result['slots'];
        $this->assertGreaterThanOrEqual(2, count($slots));
        $this->assertLessThanOrEqual(4, count($slots));

        // Cada slot ofrecido DEBE existir en AvailabilityService (no inventado).
        $tools = app(ChatTools::class);
        foreach ($slots as $slot) {
            $realSlots = $tools->getSlots($pro, $slot['date']);
            // getSlots ya está recortado a 4; validamos contra disponibilidad real completa
            $available = app(AvailabilityService::class)
                ->getAvailableSlots($pro, Carbon::parse($slot['date']));
            $this->assertTrue($available->contains($slot['time']), "El cupo {$slot['time']} no es real");
        }
    }

    public function test_crisis_path_returns_crisis_text_and_books_nothing(): void
    {
        $this->seedProfessional();
        $patient = $this->patient(withConsent: true);
        $conversation = $this->conversationFor($patient);

        $result = $this->agent()->handle(
            $conversation,
            'Ya no quiero vivir, quiero quitarme la vida',
            $patient
        );

        $this->assertTrue($result['crisis']);
        $this->assertTrue($result['escalated']);
        $this->assertStringContainsString('123', $result['reply']);
        $this->assertStringContainsString((string) config('chat.crisis_line'), $result['reply']);
        $this->assertEmpty($result['slots']);
        $this->assertDatabaseCount('appointments', 0);

        $conversation->refresh();
        $this->assertNotNull($conversation->crisis_flagged_at);
        $this->assertNotNull($conversation->escalated_at);
    }

    /**
     * La capa de refuerzo (regex) debe capturar PARÁFRASIS de crisis que la
     * lista de palabras exactas no cubre, deteniendo el agendamiento.
     */
    #[DataProvider('paraphrasedCrisisMessages')]
    public function test_paraphrased_crisis_language_is_detected_and_books_nothing(string $message): void
    {
        $this->seedProfessional();
        $patient = $this->patient(withConsent: true);
        $conversation = $this->conversationFor($patient);

        $result = $this->agent()->handle($conversation, $message, $patient);

        $this->assertTrue($result['crisis'], "No detectó crisis en: {$message}");
        $this->assertTrue($result['escalated']);
        $this->assertStringContainsString('123', $result['reply']);
        $this->assertEmpty($result['slots']);
        $this->assertDatabaseCount('appointments', 0);
    }

    /**
     * @return array<string,array{0:string}>
     */
    public static function paraphrasedCrisisMessages(): array
    {
        return [
            'ya no le veo sentido a nada' => ['Ya no le encuentro sentido a nada, agéndame una cita'],
            'ya no aguanto' => ['Ya no aguanto más esta situación'],
            'quisiera no despertar' => ['A veces quisiera no despertar mañana'],
            'quiero desaparecer' => ['Solo quiero desaparecer de todo esto'],
            'no veo salida' => ['No veo ninguna salida a lo que me pasa'],
            'estarian mejor sin mi' => ['Todos estarían mejor sin mí'],
            'acabar con todo esto' => ['Pienso en acabar con todo esto de una vez'],
        ];
    }

    /**
     * Una negación directa de la ideación NO debe disparar la capa de patrones
     * (fallback seguro sin inundar de falsos positivos). La capa 1 de palabras
     * clave explícitas no se ve afectada por esto.
     */
    public function test_negated_ideation_does_not_trigger_pattern_layer(): void
    {
        $this->seedProfessional();
        $patient = $this->patient(withConsent: true);
        $conversation = $this->conversationFor($patient);

        // "pienso en desaparecer" coincide con un patrón de la capa 2, pero la
        // negación directa "no pienso en" lo atenúa; no hay palabra clave de
        // la capa 1 en el mensaje, así que no debe marcarse crisis.
        $result = $this->agent()->handle(
            $conversation,
            'No pienso en desaparecer, solo quiero agendar una cita de psicología',
            $patient
        );

        $this->assertFalse($result['crisis']);
    }

    /**
     * La negación NO puede anular una palabra clave explícita de la capa 1:
     * el piso determinista siempre dispara.
     */
    public function test_explicit_keyword_fires_even_with_negation_phrasing(): void
    {
        $patient = $this->patient(withConsent: true);
        $conversation = $this->conversationFor($patient);

        // Contiene la frase exacta "quitarme la vida" (capa 1) junto a una
        // construcción negativa: la capa 1 debe disparar igualmente.
        $result = $this->agent()->handle(
            $conversation,
            'No sé, a veces pienso en quitarme la vida',
            $patient
        );

        $this->assertTrue($result['crisis']);
        $this->assertStringContainsString('123', $result['reply']);
    }

    public function test_crisis_text_respects_system_setting_override(): void
    {
        SystemSetting::setValue(
            'chat.crisis_text',
            'Mensaje de crisis personalizado. Llama al 123 y a la línea 999.',
            'chat'
        );

        $patient = $this->patient(withConsent: true);
        $conversation = $this->conversationFor($patient);

        $result = $this->agent()->handle($conversation, 'quiero matarme', $patient);

        $this->assertStringContainsString('personalizado', $result['reply']);
        $this->assertStringContainsString('123', $result['reply']);
    }

    public function test_booking_goes_through_appointment_service(): void
    {
        $pro = $this->seedProfessional();
        $patient = $this->patient(withConsent: true);

        $tools = app(ChatTools::class);
        $date = $tools->getAvailableDates($pro)->first();
        $time = $tools->getSlots($pro, $date)[0];

        $appointment = $tools->book($patient, $pro, $date, $time, Modality::InPerson, 'Prueba chat');

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'patient_id' => $patient->id,
            'professional_id' => $pro->id,
            'status' => AppointmentStatus::Pending->value,
        ]);
    }

    public function test_graceful_degradation_makes_no_http_call_without_key(): void
    {
        config(['services.openrouter.key' => null]);
        Http::fake();

        $this->seedProfessional();
        $patient = $this->patient(withConsent: true);
        $conversation = $this->conversationFor($patient);

        $result = $this->agent()->handle($conversation, 'Hola, ¿qué servicios ofrecen?', $patient);

        $this->assertNotEmpty($result['reply']);
        Http::assertNothingSent();
    }

    public function test_unseeded_service_reports_no_availability_without_inventing(): void
    {
        $this->seedProfessional(); // solo psicología
        $patient = $this->patient(withConsent: true);
        $conversation = $this->conversationFor($patient);

        $result = $this->agent()->handle($conversation, 'Quiero agendar terapia ocupacional', $patient);

        $this->assertEmpty($result['slots']);
        $this->assertStringContainsString('no tenemos disponibilidad', mb_strtolower($result['reply']));
        $this->assertDatabaseCount('appointments', 0);
    }

    public function test_guest_cannot_book_but_gets_graceful_message(): void
    {
        $this->seedProfessional();
        $conversation = $this->conversationFor(null);

        $result = $this->agent()->handle($conversation, 'Quiero agendar una cita de psicología', null);

        $this->assertTrue($result['requires_consent']);
        $this->assertEmpty($result['slots']);
        $this->assertDatabaseCount('appointments', 0);
    }

    /**
     * Profesional cuyo horario rinde exactamente UN cupo por día (ventana de
     * 45 min). Sirve para probar que la reprogramación sigue reuniendo >= 2
     * opciones reales a través de fechas distintas, sin inventar.
     */
    private function seedSingleSlotProfessional(): Professional
    {
        $pro = Professional::create([
            'name' => 'Dr. Unico',
            'specialty' => 'Psicología Clínica',
            'profession' => 'Psicología',
            'email' => 'pro-unico@test.com',
            'is_available' => true,
            'appointment_duration_minutes' => 45,
        ]);

        foreach (range(1, 7) as $day) {
            Schedule::create([
                'professional_id' => $pro->id,
                'day_of_week' => $day,
                'start_time' => '09:00',
                'end_time' => '09:45',
                'is_active' => true,
            ]);
        }

        return $pro;
    }

    /**
     * Regresión del piso 2-4 en la ruta de REPROGRAMACIÓN: aunque la primera
     * fecha disponible del profesional tenga un solo cupo real, el agente debe
     * ofrecer >= 2 opciones reales tomándolas de fechas posteriores (mismo
     * profesional) o de otro profesional del servicio, sin inventar ninguna.
     */
    public function test_reschedule_offers_at_least_two_real_slots_across_dates(): void
    {
        $pro = $this->seedSingleSlotProfessional();
        $patient = $this->patient(withConsent: true);

        // Cita existente (recién creada => reprogramable) en el primer cupo real.
        $tools = app(ChatTools::class);
        $firstDate = $tools->getAvailableDates($pro)->first();
        $firstTime = $tools->getSlots($pro, $firstDate)[0];
        $appointment = $tools->book($patient, $pro, $firstDate, $firstTime, Modality::InPerson);

        $conversation = $this->conversationFor($patient);
        $result = $this->agent()->handle($conversation, 'Quiero reprogramar mi cita', $patient);

        $slots = $result['slots'];

        // El piso 2-4 debe honrarse pese a 1 cupo por fecha.
        $this->assertGreaterThanOrEqual(2, count($slots), 'La reprogramación debe ofrecer >= 2 opciones reales');
        $this->assertLessThanOrEqual(4, count($slots));

        // Cada cupo ofrecido DEBE ser real según AvailabilityService (no inventado)
        // y NO puede coincidir con el cupo ya ocupado por la cita existente.
        $availability = app(AvailabilityService::class);
        foreach ($slots as $slot) {
            $this->assertTrue(
                $availability->isSlotAvailable($pro, Carbon::parse($slot['date']), $slot['time']),
                "El cupo {$slot['date']} {$slot['time']} no es real"
            );
            $this->assertFalse(
                $slot['date'] === $appointment->date->format('Y-m-d') && $slot['time'] === $firstTime,
                'No debe ofrecerse el cupo ya ocupado por la cita'
            );
        }

        // Las opciones deben estar en fechas distintas (el profesional solo
        // tiene 1 cupo por día), confirmando la colección multi-fecha.
        $this->assertGreaterThanOrEqual(2, collect($slots)->pluck('date')->unique()->count());
    }

    /**
     * La guardia anti-alucinación descarta una respuesta del modelo que invente
     * un NOMBRE de profesional ausente del texto determinista: se devuelve el
     * determinista, nunca el nombre fabricado.
     */
    public function test_phrasing_guard_discards_fabricated_professional_name(): void
    {
        config(['services.openrouter.key' => 'test-key']);
        $this->fakeOpenRouterReply('Para Psicología tengo cupos con Dra. Fantasma Inventada; dime cuál prefieres.');

        $this->seedProfessional(); // Dra. Prueba
        $patient = $this->patient(withConsent: true);
        $conversation = $this->conversationFor($patient);

        $result = $this->agent()->handle($conversation, 'Quiero agendar psicología', $patient);

        // El modelo fabricó "Fantasma Inventada": se descarta y se usa el
        // determinista (que menciona a la Dra. Prueba, no a la inventada).
        $this->assertStringNotContainsString('Fantasma', $result['reply']);
        $this->assertStringNotContainsString('Inventada', $result['reply']);
        $this->assertStringContainsString('Prueba', $result['reply']);
    }

    /**
     * La guardia anti-alucinación descarta una respuesta del modelo que invente
     * una FECHA (YYYY-MM-DD) ausente del texto determinista.
     */
    public function test_phrasing_guard_discards_fabricated_date(): void
    {
        config(['services.openrouter.key' => 'test-key']);
        $this->fakeOpenRouterReply('Te puedo agendar el 2099-12-31 a las 09:00. ¿Confirmas?');

        $this->seedProfessional();
        $patient = $this->patient(withConsent: true);
        $conversation = $this->conversationFor($patient);

        $result = $this->agent()->handle($conversation, 'Quiero agendar psicología', $patient);

        // La fecha 2099-12-31 no está en el determinista: respuesta descartada.
        $this->assertStringNotContainsString('2099-12-31', $result['reply']);
        // El determinista real ofrece cupos; debe aparecer el texto verificado.
        $this->assertStringContainsString('Opción', $result['reply']);
    }

    /**
     * Control: cuando el modelo redacta SIN inventar datos (solo reformula el
     * texto verificado), su respuesta SÍ se conserva.
     */
    public function test_phrasing_guard_keeps_faithful_model_reply(): void
    {
        config(['services.openrouter.key' => 'test-key']);
        $this->fakeOpenRouterReply('Claro, con gusto te ayudo a agendar. ¿En qué más puedo apoyarte?');

        $this->seedProfessional();
        $patient = $this->patient(withConsent: true);
        $conversation = $this->conversationFor($patient);

        $result = $this->agent()->handle($conversation, 'Hola, buenos días', $patient);

        $this->assertStringContainsString('con gusto te ayudo a agendar', $result['reply']);
    }

    /**
     * (a) Capa 1 (palabra clave) dispara crisis AUNQUE el clasificador del
     * modelo no esté disponible (sin OPENROUTER_API_KEY): no se hace ninguna
     * llamada de red y no se agenda nada. El piso determinista gobierna solo.
     */
    public function test_keyword_crisis_flags_without_classifier_and_sends_no_http(): void
    {
        config(['services.openrouter.key' => null]);
        Http::fake();

        $this->seedProfessional();
        $patient = $this->patient(withConsent: true);
        $conversation = $this->conversationFor($patient);

        $result = $this->agent()->handle(
            $conversation,
            'Quiero quitarme la vida y agendar una cita de psicología',
            $patient
        );

        $this->assertTrue($result['crisis']);
        $this->assertEmpty($result['slots']);
        $this->assertDatabaseCount('appointments', 0);
        Http::assertNothingSent();
    }

    /**
     * (b) Capa 3 (clasificador del modelo) ELEVA una paráfrasis que NO cae en
     * las capas deterministas (ni palabra clave ni patrón regex). Con la key
     * configurada y Http::fake() devolviendo {"crisis": true}, el turno se
     * marca como crisis y no agenda nada.
     */
    public function test_model_classifier_escalates_deterministic_miss(): void
    {
        config(['services.openrouter.key' => 'test-key']);
        $this->fakeOpenRouterReply('{"crisis": true}');

        $this->seedProfessional();
        $patient = $this->patient(withConsent: true);
        $conversation = $this->conversationFor($patient);

        // Paráfrasis muy indirecta: no contiene palabra clave de la capa 1 ni
        // coincide con los patrones regex de la capa 2.
        $message = 'Siento que soy una carga para mi familia y preferiria no estar';

        // Pre-condición: el mensaje no debe disparar las capas deterministas.
        config(['chat.crisis_classifier_enabled' => false]);
        $deterministic = $this->agent()->handle($this->conversationFor($patient), $message, $patient);
        $this->assertFalse($deterministic['crisis'], 'El mensaje no debe caer en capas deterministas');

        // Ahora con el clasificador activo, el modelo eleva la señal.
        config(['chat.crisis_classifier_enabled' => true]);
        $result = $this->agent()->handle($conversation, $message, $patient);

        $this->assertTrue($result['crisis']);
        $this->assertTrue($result['escalated']);
        $this->assertEmpty($result['slots']);
        $this->assertDatabaseCount('appointments', 0);
    }

    /**
     * (c) El clasificador NUNCA puede bajar un hit determinista: con un mensaje
     * de palabra clave (capa 1) Y un Http::fake() que clasifica NO-crisis, la
     * crisis sigue marcada Y el clasificador NO se consulta (short-circuit
     * determinista => cero HTTP). Esta prueba falla si se revierte la asimetría.
     */
    public function test_model_classifier_cannot_downgrade_keyword_hit(): void
    {
        config(['services.openrouter.key' => 'test-key']);
        // El modelo diría "no crisis", pero no debe ser consultado siquiera.
        $this->fakeOpenRouterReply('{"crisis": false}');

        $this->seedProfessional();
        $patient = $this->patient(withConsent: true);
        $conversation = $this->conversationFor($patient);

        $result = $this->agent()->handle(
            $conversation,
            'Ya no quiero vivir, quiero quitarme la vida',
            $patient
        );

        $this->assertTrue($result['crisis']);
        $this->assertTrue($result['escalated']);
        $this->assertEmpty($result['slots']);
        $this->assertDatabaseCount('appointments', 0);

        // La capa 1 hace short-circuit: el clasificador no se consulta.
        Http::assertNothingSent();
    }

    public function test_clinical_question_escalates_to_human(): void
    {
        $patient = $this->patient(withConsent: true);
        $conversation = $this->conversationFor($patient);

        $result = $this->agent()->handle($conversation, '¿Qué medicamento me tomo para la depresión?', $patient);

        $this->assertTrue($result['escalated']);
        $this->assertEmpty($result['slots']);
    }
}
