<?php

namespace Tests\Feature;

use App\Enums\AppointmentStatus;
use App\Enums\Modality;
use App\Enums\Role;
use App\Models\Appointment;
use App\Models\Professional;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentBookingTest extends TestCase
{
    use RefreshDatabase;

    private function seedMinimal(): array
    {
        $patient = User::factory()->create([
            'document_number' => '1234567890',
            'role' => Role::Patient,
            'is_active' => true,
        ]);

        $pro = Professional::create([
            'name' => 'Dr Test',
            'specialty' => 'Psicología',
            'email' => 'pro@test.com',
            'is_available' => true,
            'appointment_duration_minutes' => 45,
        ]);

        // Mañana = dayOfWeekIso 1..7; usamos todos los días para no fallar
        foreach (range(1, 7) as $day) {
            Schedule::create([
                'professional_id' => $pro->id,
                'day_of_week' => $day,
                'start_time' => '09:00',
                'end_time' => '12:00',
                'is_active' => true,
            ]);
        }

        return compact('patient', 'pro');
    }

    public function test_patient_can_view_booking_page(): void
    {
        ['patient' => $patient, 'pro' => $pro] = $this->seedMinimal();

        $this->actingAs($patient)
            ->get(route('patient.appointments.create'))
            ->assertOk()
            ->assertSee($pro->name);
    }

    public function test_patient_can_book_available_slot(): void
    {
        ['patient' => $patient, 'pro' => $pro] = $this->seedMinimal();

        // Buscar un día laborable futuro con slot
        $date = now()->next('Monday')->format('Y-m-d');
        if (now()->isMonday() && now()->format('H:i') < '09:00') {
            $date = now()->format('Y-m-d');
        }

        $response = $this->actingAs($patient)->post(route('patient.appointments.store'), [
            'professional_id' => $pro->id,
            'date' => $date,
            'time' => '09:00',
            'modality' => Modality::InPerson->value,
            'reason' => 'Consulta de prueba',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('appointments', [
            'patient_id' => $patient->id,
            'professional_id' => $pro->id,
            'status' => AppointmentStatus::Pending->value,
        ]);
    }

    public function test_patient_cannot_double_book_same_slot(): void
    {
        ['patient' => $patient, 'pro' => $pro] = $this->seedMinimal();
        $date = now()->next('Tuesday')->format('Y-m-d');

        Appointment::create([
            'patient_id' => $patient->id,
            'professional_id' => $pro->id,
            'date' => $date,
            'time' => '09:00',
            'modality' => Modality::InPerson,
            'status' => AppointmentStatus::Confirmed,
        ]);

        $other = User::factory()->create([
            'document_number' => '9876543210',
            'role' => Role::Patient,
            'is_active' => true,
        ]);

        $response = $this->actingAs($other)->post(route('patient.appointments.store'), [
            'professional_id' => $pro->id,
            'date' => $date,
            'time' => '09:00',
            'modality' => Modality::InPerson->value,
        ]);

        $response->assertSessionHasErrors('time');
    }

    public function test_guest_cannot_access_booking(): void
    {
        $this->get(route('patient.appointments.create'))->assertRedirect(route('login'));
    }
}
