<?php

namespace Database\Factories;

use App\Enums\Modality;
use App\Enums\Role;
use App\Enums\WaitlistStatus;
use App\Models\User;
use App\Models\WaitlistEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<WaitlistEntry> */
class WaitlistEntryFactory extends Factory
{
    protected $model = WaitlistEntry::class;

    public function definition(): array
    {
        return [
            'patient_id' => User::factory()->state(['role' => Role::Patient]),
            'specialty' => 'Psicología Clínica',
            'date_from' => now()->toDateString(),
            'date_to' => now()->addDays(30)->toDateString(),
            'time_from' => '08:00',
            'time_to' => '17:00',
            'modality' => Modality::InPerson,
            'status' => WaitlistStatus::Waiting,
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
