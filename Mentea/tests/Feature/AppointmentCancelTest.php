<?php

namespace Tests\Feature;

use App\Enums\AppointmentStatus;
use App\Enums\Modality;
use App\Enums\Role;
use App\Models\Appointment;
use App\Models\Professional;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentCancelTest extends TestCase
{
    use RefreshDatabase;

    public function test_patient_can_cancel_if_more_than_5_days_ahead(): void
    {
        $patient = User::factory()->create(['role' => Role::Patient, 'is_active' => true]);
        $pro = Professional::create([
            'name' => 'Dr Test',
            'specialty' => 'Psicología',
            'email' => 'x@test.com',
            'is_available' => true,
            'appointment_duration_minutes' => 45,
        ]);

        $appointment = Appointment::create([
            'patient_id' => $patient->id,
            'professional_id' => $pro->id,
            'date' => now()->addDays(10)->toDateString(),
            'time' => '10:00',
            'modality' => Modality::InPerson,
            'status' => AppointmentStatus::Confirmed,
        ]);

        $this->actingAs($patient)
            ->post(route('patient.appointments.cancel', $appointment), ['reason' => 'Cambio de planes'])
            ->assertRedirect(route('patient.appointments.index'));

        $this->assertEquals(AppointmentStatus::Cancelled, $appointment->fresh()->status);
    }

    public function test_patient_cannot_cancel_within_5_days(): void
    {
        $patient = User::factory()->create(['role' => Role::Patient, 'is_active' => true]);
        $pro = Professional::create([
            'name' => 'Dr Test',
            'specialty' => 'Psicología',
            'email' => 'y@test.com',
            'is_available' => true,
            'appointment_duration_minutes' => 45,
        ]);

        $appointment = Appointment::create([
            'patient_id' => $patient->id,
            'professional_id' => $pro->id,
            'date' => now()->addDays(2)->toDateString(),
            'time' => '10:00',
            'modality' => Modality::InPerson,
            'status' => AppointmentStatus::Confirmed,
        ]);

        $this->actingAs($patient)
            ->post(route('patient.appointments.cancel', $appointment))
            ->assertSessionHasErrors('appointment');

        $this->assertEquals(AppointmentStatus::Confirmed, $appointment->fresh()->status);
    }
}
