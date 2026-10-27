<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Professional;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ChatEndpointTest extends TestCase
{
    use RefreshDatabase;

    private function seedProfessional(): Professional
    {
        $pro = Professional::create([
            'name' => 'Dra Prueba',
            'specialty' => 'Psicología',
            'profession' => 'Psicología',
            'email' => 'pro@test.com',
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

    public function test_authenticated_patient_receives_json_reply(): void
    {
        // Sin OPENROUTER_API_KEY: el agente usa su ruta determinista. No debe
        // haber ninguna llamada de red real.
        Http::fake();

        $patient = User::factory()->create([
            'role' => Role::Patient,
            'is_active' => true,
        ]);

        $response = $this->actingAs($patient)->postJson(route('chat.send'), [
            'message' => 'Hola, ¿qué servicios ofrecen?',
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'reply',
                'conversation_id',
                'flags' => ['crisis', 'escalated', 'requires_consent'],
                'slots',
            ]);

        $this->assertIsString($response->json('reply'));
        $this->assertNotEmpty($response->json('reply'));

        $this->assertDatabaseHas('chat_conversations', [
            'user_id' => $patient->id,
        ]);
    }

    public function test_crisis_message_returns_crisis_text_and_books_nothing(): void
    {
        Http::fake();

        $patient = User::factory()->create([
            'role' => Role::Patient,
            'is_active' => true,
            'consent_informed_at' => now(),
        ]);

        $this->seedProfessional();

        $response = $this->actingAs($patient)->postJson(route('chat.send'), [
            'message' => 'Quiero agendar pero la verdad pienso en suicidio y quitarme la vida',
        ]);

        $response->assertOk();

        $reply = $response->json('reply');
        $this->assertStringContainsString('123', $reply);
        $this->assertStringContainsString((string) config('chat.crisis_line'), $reply);

        $this->assertTrue($response->json('flags.crisis'));
        $this->assertSame(0, $response->json('slots') === null ? 0 : count($response->json('slots')));

        // El protocolo de crisis detiene todo agendamiento.
        $this->assertDatabaseCount('appointments', 0);
    }

    public function test_guest_can_chat_without_server_error(): void
    {
        Http::fake();

        $response = $this->postJson(route('chat.send'), [
            'message' => 'Hola, ¿me pueden dar información?',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['reply', 'conversation_id', 'flags', 'slots']);

        $this->assertNotEmpty($response->json('reply'));
    }

    public function test_guest_booking_request_degrades_gracefully(): void
    {
        Http::fake();

        $response = $this->postJson(route('chat.send'), [
            'message' => 'Quiero agendar una cita de psicología',
        ]);

        // Nunca un 500: el agente responde pidiendo iniciar sesión / consentimiento.
        $response->assertOk();
        $this->assertTrue($response->json('flags.requires_consent'));
        $this->assertDatabaseCount('appointments', 0);
    }

    public function test_empty_message_is_rejected(): void
    {
        $patient = User::factory()->create([
            'role' => Role::Patient,
            'is_active' => true,
        ]);

        $this->actingAs($patient)
            ->postJson(route('chat.send'), ['message' => ''])
            ->assertStatus(422)
            ->assertJsonValidationErrors('message');
    }

    public function test_chat_route_is_registered_under_web_middleware(): void
    {
        $route = app('router')->getRoutes()->getByName('chat.send');

        $this->assertNotNull($route);
        $this->assertContains('web', $route->gatherMiddleware());
        $this->assertContains('POST', $route->methods());
    }
}
