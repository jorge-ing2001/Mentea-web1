<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Professional;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AvailabilityApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_patient_can_fetch_slots(): void
    {
        $patient = User::factory()->create([
            'role' => Role::Patient,
            'email_verified_at' => now(),
            'is_active' => true,
            'consent_informed_at' => now(),
        ]);
        $pro = Professional::factory()->create(['is_available' => true]);
        // Monday=1 in ISO
        $date = now()->next('Monday');
        if ($date->isPast()) {
            $date->addWeek();
        }
        Schedule::create([
            'professional_id' => $pro->id,
            'day_of_week' => $date->dayOfWeekIso,
            'start_time' => '09:00:00',
            'end_time' => '12:00:00',
            'is_active' => true,
        ]);

        $response = $this->actingAs($patient)->getJson(
            route('patient.appointments.slots', ['professional' => $pro]) . '?date=' . $date->toDateString()
        );

        $response->assertOk();
        $response->assertJsonStructure(['slots']);
    }

    public function test_guest_cannot_fetch_slots(): void
    {
        $pro = Professional::factory()->create();
        $this->getJson(route('patient.appointments.slots', ['professional' => $pro]) . '?date=' . now()->addDay()->toDateString())
            ->assertRedirect(); // web auth redirect to login
    }
}
