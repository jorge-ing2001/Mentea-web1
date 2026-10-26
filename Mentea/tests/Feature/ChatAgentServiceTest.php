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

    public function test_clinical_question_escalates_to_human(): void
    {
        $patient = $this->patient(withConsent: true);
        $conversation = $this->conversationFor($patient);

        $result = $this->agent()->handle($conversation, '¿Qué medicamento me tomo para la depresión?', $patient);

        $this->assertTrue($result['escalated']);
        $this->assertEmpty($result['slots']);
    }
}
