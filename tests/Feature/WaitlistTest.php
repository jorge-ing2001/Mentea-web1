<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Enums\WaitlistStatus;
use App\Jobs\ScanWaitlistSlots;
use App\Models\User;
use App\Models\WaitlistEntry;
use App\Services\AvailabilityService;
use App\Services\WaitlistService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WaitlistTest extends TestCase
{
    use RefreshDatabase;

    private function patient(): User
    {
        $user = User::factory()->create([
            'role' => Role::Patient,
            'email_verified_at' => now(),
            'is_active' => true,
            'consent_informed_at' => now(),
        ]);
        if (method_exists($user, 'assignRole')) {
            try {
                $user->assignRole('patient');
            } catch (\Throwable) {
            }
        }

        return $user;
    }

    public function test_patient_can_create_waitlist_entry(): void
    {
        $patient = $this->patient();

        $response = $this->actingAs($patient)->post(route('patient.waitlist.store'), [
            'specialty' => 'Psicología Clínica',
            'professional_id' => '',
            'date_from' => now()->toDateString(),
            'date_to' => now()->addDays(30)->toDateString(),
            'time_from' => '08:00',
            'time_to' => '12:00',
            'modality' => 'in_person',
            'notes' => 'Preferencia mañana',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('waitlist_entries', [
            'patient_id' => $patient->id,
            'specialty' => 'Psicología Clínica',
            'status' => WaitlistStatus::Waiting->value,
        ]);
    }

    public function test_patient_can_cancel_own_waitlist_entry(): void
    {
        $patient = $this->patient();
        $entry = WaitlistEntry::create([
            'patient_id' => $patient->id,
            'specialty' => 'Psiquiatría',
            'date_from' => now()->toDateString(),
            'date_to' => now()->addDays(20)->toDateString(),
            'status' => WaitlistStatus::Waiting,
        ]);

        $response = $this->actingAs($patient)->post(route('patient.waitlist.cancel', $entry));

        $response->assertRedirect();
        $entry->refresh();
        $this->assertTrue(in_array($entry->status->value ?? $entry->status, [
            WaitlistStatus::Cancelled->value,
            'cancelled',
        ], true));
    }

    public function test_guest_cannot_access_waitlist(): void
    {
        $this->get(route('patient.waitlist.index'))->assertRedirect(route('login'));
    }

    public function test_waitlist_scan_job_runs_without_error(): void
    {
        $this->patient();
        WaitlistEntry::create([
            'patient_id' => User::where('role', Role::Patient)->first()->id,
            'specialty' => 'Psicología Clínica',
            'date_from' => now()->toDateString(),
            'date_to' => now()->addDays(14)->toDateString(),
            'status' => WaitlistStatus::Waiting,
        ]);

        $job = new ScanWaitlistSlots(7);
        $job->handle(
            app(AvailabilityService::class),
            app(WaitlistService::class)
        );

        $this->assertTrue(true); // no exception
    }
}
