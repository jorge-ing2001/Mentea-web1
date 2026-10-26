<?php

namespace Tests\Feature;

use App\Enums\AppointmentStatus;
use App\Enums\Modality;
use App\Enums\Role;
use App\Models\Appointment;
use App\Models\ChatConversation;
use App\Models\Professional;
use App\Models\Schedule;
use App\Models\User;
use App\Services\Chat\ChatAgentService;
use App\Services\Chat\ChatTools;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Ciclo de agendamiento conversacional completo: ofrecer -> seleccionar ->
 * reservar/reprogramar/cancelar/lista de espera a través de un turno de chat
 * (no llamando a la herramienta directamente). Re-verifica cupos antes de
 * confirmar y degrada con gracia cuando un cupo queda obsoleto.
 */
class ChatBookingFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
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
            'session_id' => $user ? null : 'guest-flow',
            'status' => 'active',
        ]);
    }

    private function patient(bool $withConsent = true): User
    {
        return User::factory()->create([
            'role' => Role::Patient,
            'is_active' => true,
            'consent_informed_at' => $withConsent ? now() : null,
        ]);
    }

    private function professional(string $start = '09:00', string $end = '12:00', int $duration = 45): Professional
    {
        $pro = Professional::create([
            'name' => 'Dra. Prueba',
            'specialty' => 'Psicología Clínica',
            'profession' => 'Psicología',
            'email' => 'flow-'.uniqid().'@test.com',
            'is_available' => true,
            'appointment_duration_minutes' => $duration,
        ]);

        foreach (range(1, 7) as $day) {
            Schedule::create([
                'professional_id' => $pro->id,
                'day_of_week' => $day,
                'start_time' => $start,
                'end_time' => $end,
                'is_active' => true,
            ]);
        }

        return $pro;
    }

    public function test_full_booking_through_conversation_turns_creates_appointment(): void
    {
        $pro = $this->professional();
        $patient = $this->patient();
        $conversation = $this->conversationFor($patient);

        // Turno 1: oferta de cupos.
        $offer = $this->agent()->handle($conversation, 'Quiero agendar una cita de psicología', $patient);
        $this->assertNotEmpty($offer['slots']);
        $this->assertDatabaseCount('appointments', 0);

        $chosen = $offer['slots'][0];

        // Turno 2: el paciente elige "la primera".
        $result = $this->agent()->handle($conversation, 'La primera por favor', $patient);

        $this->assertDatabaseCount('appointments', 1);
        $appointment = Appointment::where('patient_id', $patient->id)->firstOrFail();
        $this->assertSame($pro->id, $appointment->professional_id);
        $this->assertSame($chosen['date'], $appointment->date->format('Y-m-d'));
        $this->assertSame(AppointmentStatus::Pending->value, $appointment->status->value);
        $this->assertStringContainsString($chosen['time'], $result['reply']);
    }

    public function test_selection_by_time_books_the_matching_slot(): void
    {
        $pro = $this->professional();
        $patient = $this->patient();
        $conversation = $this->conversationFor($patient);

        $offer = $this->agent()->handle($conversation, 'Necesito agendar psicología', $patient);
        $target = $offer['slots'][1] ?? $offer['slots'][0];

        $this->agent()->handle($conversation, 'Prefiero las '.$target['time'], $patient);

        $appointment = Appointment::where('patient_id', $patient->id)->firstOrFail();
        $this->assertSame($target['date'], $appointment->date->format('Y-m-d'));
        $this->assertSame($target['time'], Carbon::parse($appointment->time)->format('H:i'));
    }

    public function test_stale_slot_at_confirm_time_is_rejected_gracefully_and_reoffers(): void
    {
        $pro = $this->professional();
        $patient = $this->patient();
        $other = $this->patient();
        $conversation = $this->conversationFor($patient);

        $offer = $this->agent()->handle($conversation, 'Quiero agendar psicología', $patient);
        $chosen = $offer['slots'][0];

        // Otro paciente toma ese mismo cupo justo antes de confirmar.
        app(ChatTools::class)->book($other, $pro, $chosen['date'], $chosen['time'], Modality::InPerson);
        $this->assertDatabaseCount('appointments', 1);

        // El paciente intenta confirmar el cupo ya tomado.
        $result = $this->agent()->handle($conversation, 'La primera', $patient);

        // No se crea una segunda cita para ESTE paciente; se re-ofrece con gracia.
        $this->assertSame(0, Appointment::where('patient_id', $patient->id)->count());
        $this->assertStringContainsString('ya no está disponible', $result['reply']);
        $this->assertNotEmpty($result['slots']);
    }

    public function test_single_real_slot_is_offered_as_one_option_without_inventing(): void
    {
        // Ventana que produce exactamente 1 cupo por día (09:00, duración 120 -> solo 09:00).
        $pro = $this->professional('09:00', '11:00', 120);
        $patient = $this->patient();
        $conversation = $this->conversationFor($patient);

        $result = $this->agent()->handle($conversation, 'Quiero agendar psicología', $patient);

        // Hay varias fechas con 1 cupo cada una: el mínimo de 2 se cumple
        // combinando fechas reales, nunca inventando.
        $this->assertGreaterThanOrEqual(1, count($result['slots']));
        $this->assertLessThanOrEqual(4, count($result['slots']));

        $tools = app(ChatTools::class);
        foreach ($result['slots'] as $slot) {
            $this->assertContains($slot['time'], $tools->getSlots($pro, $slot['date']));
        }
    }

    public function test_cancel_through_conversation_cancels_existing_appointment(): void
    {
        $pro = $this->professional();
        $patient = $this->patient();

        // Cita futura cancelable (>= 5 días).
        $date = now()->addDays(10)->format('Y-m-d');
        $appointment = app(ChatTools::class)->book($patient, $pro, $date, '09:00', Modality::InPerson);
        $this->assertSame(AppointmentStatus::Pending->value, $appointment->status->value);

        $conversation = $this->conversationFor($patient);
        $result = $this->agent()->handle($conversation, 'Quiero cancelar mi cita', $patient);

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => AppointmentStatus::Cancelled->value,
        ]);
        $this->assertStringContainsString('Cancel', $result['reply']);
    }

    public function test_waitlist_join_through_conversation_creates_entry(): void
    {
        $pro = $this->professional();
        $patient = $this->patient();
        $conversation = $this->conversationFor($patient);

        $result = $this->agent()->handle($conversation, 'Ponme en lista de espera para psicología', $patient);

        $this->assertDatabaseHas('waitlist_entries', [
            'patient_id' => $patient->id,
            'professional_id' => $pro->id,
            'specialty' => $pro->specialty,
        ]);
        $this->assertStringContainsString('lista de espera', mb_strtolower($result['reply']));
    }

    public function test_reschedule_through_conversation_moves_appointment(): void
    {
        $pro = $this->professional();
        $patient = $this->patient();

        $date = now()->addDays(10)->format('Y-m-d');
        $appointment = app(ChatTools::class)->book($patient, $pro, $date, '09:00', Modality::InPerson);

        $conversation = $this->conversationFor($patient);

        // Turno 1: pedir reprogramación -> oferta de nuevos cupos.
        $offer = $this->agent()->handle($conversation, 'Quiero reprogramar mi cita', $patient);
        $this->assertNotEmpty($offer['slots']);

        // Elegir un cupo distinto al actual.
        $target = collect($offer['slots'])
            ->first(fn ($s) => ! ($s['date'] === $date && $s['time'] === '09:00')) ?? $offer['slots'][0];

        $this->agent()->handle($conversation, 'La '.($this->ordinalFor($offer['slots'], $target)), $patient);

        $appointment->refresh();
        $this->assertSame($target['date'], $appointment->date->format('Y-m-d'));
        // Sigue siendo una sola cita (reprogramada, no duplicada).
        $this->assertSame(1, Appointment::where('patient_id', $patient->id)->count());
    }

    private function ordinalFor(array $slots, array $target): string
    {
        $map = [1 => 'primera', 2 => 'segunda', 3 => 'tercera', 4 => 'cuarta'];
        foreach ($slots as $i => $slot) {
            if ($slot['date'] === $target['date'] && $slot['time'] === $target['time']) {
                return $map[$i + 1] ?? 'primera';
            }
        }

        return 'primera';
    }

    public function test_guest_booking_sets_requires_login_and_requires_consent(): void
    {
        $this->professional();
        $conversation = $this->conversationFor(null);

        $result = $this->agent()->handle($conversation, 'Quiero agendar una cita de psicología', null);

        $this->assertTrue($result['requires_login']);
        $this->assertTrue($result['requires_consent']);
        $this->assertEmpty($result['slots']);
        $this->assertDatabaseCount('appointments', 0);
    }

    public function test_patient_without_consent_requires_consent_but_not_login(): void
    {
        $this->professional();
        $patient = $this->patient(withConsent: false);
        $conversation = $this->conversationFor($patient);

        $result = $this->agent()->handle($conversation, 'Quiero agendar una cita de psicología', $patient);

        $this->assertTrue($result['requires_consent']);
        $this->assertFalse($result['requires_login']);
    }
}
