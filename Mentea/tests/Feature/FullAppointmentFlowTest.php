<?php

namespace Tests\Feature;

use App\Enums\AppointmentStatus;
use App\Enums\Modality;
use App\Enums\Role;
use App\Enums\WaitlistStatus;
use App\Models\Appointment;
use App\Models\Professional;
use App\Models\Schedule;
use App\Models\User;
use App\Models\WaitlistEntry;
use App\Services\AppointmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FullAppointmentFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_book_cancel_waitlist_and_survey_flow(): void
    {
        Storage::fake('local');

        $patient = User::factory()->create([
            'role' => Role::Patient,
            'email_verified_at' => now(),
            'is_active' => true,
            'consent_informed_at' => now(),
            'consent_data_at' => now(),
            'onboarding_completed_at' => now(),
        ]);

        $pro = Professional::factory()->create(['is_available' => true, 'specialty' => 'Psicología Clínica']);
        $date = now()->addDays(10);
        while ($date->isWeekend()) {
            $date->addDay();
        }
        Schedule::create([
            'professional_id' => $pro->id,
            'day_of_week' => $date->dayOfWeekIso,
            'start_time' => '09:00:00',
            'end_time' => '12:00:00',
            'is_active' => true,
        ]);

        $service = app(AppointmentService::class);
        $appointment = $service->book(
            patient: $patient,
            professional: $pro,
            date: $date->toDateString(),
            time: '09:00',
            modality: Modality::InPerson,
            reason: 'Consulta',
            orderDocument: UploadedFile::fake()->create('orden.pdf', 50, 'application/pdf'),
            authorizationDocument: UploadedFile::fake()->create('auth.pdf', 50, 'application/pdf'),
            skipAvailabilityCheck: true,
        );

        $this->assertNotNull($appointment->id);
        $this->assertNotNull($appointment->confirm_token);

        // Confirm
        $this->actingAs($patient)
            ->post(route('patient.appointments.confirm', $appointment))
            ->assertRedirect();
        $appointment->refresh();
        $this->assertNotNull($appointment->patient_confirmed_at);

        // Cancel
        $service->cancelByPatient($appointment, $patient, 'Test cancel');
        $appointment->refresh();
        $this->assertEquals(AppointmentStatus::Cancelled, $appointment->status);

        // Waitlist
        $entry = WaitlistEntry::factory()->create([
            'patient_id' => $patient->id,
            'specialty' => 'Psicología Clínica',
            'status' => WaitlistStatus::Waiting,
        ]);
        $this->assertEquals(WaitlistStatus::Waiting, $entry->status);

        // Survey requires completed appointment
        $completed = Appointment::factory()->completed()->create([
            'patient_id' => $patient->id,
            'professional_id' => $pro->id,
            'date' => now()->subDays(2)->toDateString(),
        ]);
        $this->actingAs($patient)
            ->post(route('patient.surveys.store', $completed), [
                'rating' => 5,
                'comment' => 'Excelente',
            ])
            ->assertRedirect();
        $this->assertDatabaseHas('surveys', [
            'appointment_id' => $completed->id,
            'rating' => 5,
        ]);
    }
}
