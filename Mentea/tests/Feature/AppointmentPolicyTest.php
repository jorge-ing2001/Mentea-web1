<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Appointment;
use App\Models\Professional;
use App\Models\User;
use App\Policies\AppointmentPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_patient_can_view_own_appointment(): void
    {
        $patient = User::factory()->create(['role' => Role::Patient]);
        $appointment = Appointment::factory()->create(['patient_id' => $patient->id]);
        $policy = new AppointmentPolicy;

        $this->assertTrue($policy->view($patient, $appointment));
    }

    public function test_patient_cannot_view_others_appointment(): void
    {
        $patient = User::factory()->create(['role' => Role::Patient]);
        $other = User::factory()->create(['role' => Role::Patient]);
        $appointment = Appointment::factory()->create(['patient_id' => $other->id]);
        $policy = new AppointmentPolicy;

        $this->assertFalse($policy->view($patient, $appointment));
    }

    public function test_only_assigned_professional_sees_clinical_notes(): void
    {
        $proUser = User::factory()->create(['role' => Role::Professional]);
        $professional = Professional::factory()->create(['user_id' => $proUser->id]);
        $otherProUser = User::factory()->create(['role' => Role::Professional]);
        Professional::factory()->create(['user_id' => $otherProUser->id]);

        $appointment = Appointment::factory()->create(['professional_id' => $professional->id]);
        $policy = new AppointmentPolicy;

        $this->assertTrue($policy->viewClinicalNotes($proUser, $appointment));
        $this->assertFalse($policy->viewClinicalNotes($otherProUser, $appointment));
    }
}
