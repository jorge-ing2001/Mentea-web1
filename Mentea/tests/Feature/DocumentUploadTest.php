<?php

namespace Tests\Feature;

use App\Enums\AppointmentStatus;
use App\Enums\Modality;
use App\Enums\Role;
use App\Models\Appointment;
use App\Models\Professional;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentUploadTest extends TestCase
{
    use RefreshDatabase;

    private function patientWithConsent(): User
    {
        $user = User::factory()->create([
            'role' => Role::Patient,
            'email_verified_at' => now(),
            'is_active' => true,
            'consent_informed_at' => now(),
            'consent_data_at' => now(),
        ]);
        try { $user->assignRole('patient'); } catch (\Throwable) {}
        return $user;
    }

    public function test_booking_requires_order_and_authorization_documents(): void
    {
        Storage::fake('local');
        $patient = $this->patientWithConsent();
        $pro = Professional::factory()->create([]);

        $response = $this->actingAs($patient)->post(route('patient.appointments.store'), [
            'professional_id' => $pro->id,
            'date' => now()->addDays(7)->toDateString(),
            'time' => '10:00',
            'modality' => Modality::InPerson->value,
            'reason' => 'Consulta',
        ]);

        $response->assertSessionHasErrors(['order_document', 'authorization_document']);
    }

    public function test_admin_can_view_appointment_documents(): void
    {
        Storage::fake('local');
        $patient = $this->patientWithConsent();
        $admin = User::factory()->create([
            'role' => Role::Admin,
            'email_verified_at' => now(),
            'is_active' => true,
        ]);
        try { $admin->assignRole('admin'); } catch (\Throwable) {}

        $pro = Professional::factory()->create([]);
        $order = UploadedFile::fake()->create('orden.pdf', 100, 'application/pdf');
        $path = $order->store('appointments/docs', 'local');

        $appointment = Appointment::create([
            'patient_id' => $patient->id,
            'professional_id' => $pro->id,
            'date' => now()->addDays(5)->toDateString(),
            'time' => '11:00:00',
            'modality' => Modality::InPerson,
            'status' => AppointmentStatus::Pending,
            'order_document' => $path,
            'authorization_document' => $path,
        ]);

        $response = $this->actingAs($admin)->get(route('documents.view', [
            'appointment' => $appointment,
            'type' => 'order',
        ]));

        $response->assertOk();
    }

    public function test_patient_cannot_view_another_patients_documents(): void
    {
        Storage::fake('local');
        $owner = $this->patientWithConsent();
        $other = $this->patientWithConsent();
        $pro = Professional::factory()->create([]);
        $path = UploadedFile::fake()->create('orden.pdf', 50, 'application/pdf')->store('appointments/docs', 'local');

        $appointment = Appointment::create([
            'patient_id' => $owner->id,
            'professional_id' => $pro->id,
            'date' => now()->addDays(3)->toDateString(),
            'time' => '09:00:00',
            'modality' => Modality::Virtual,
            'status' => AppointmentStatus::Pending,
            'order_document' => $path,
            'authorization_document' => $path,
        ]);

        $this->actingAs($other)->get(route('documents.view', [
            'appointment' => $appointment,
            'type' => 'order',
        ]))->assertForbidden();
    }
}
