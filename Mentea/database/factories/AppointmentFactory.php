<?php

namespace Database\Factories;

use App\Enums\AppointmentStatus;
use App\Enums\Modality;
use App\Enums\Role;
use App\Models\Appointment;
use App\Models\Professional;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Appointment> */
class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    public function definition(): array
    {
        return [
            'patient_id' => User::factory()->state(['role' => Role::Patient]),
            'professional_id' => Professional::factory(),
            'date' => fake()->dateTimeBetween('+1 day', '+30 days')->format('Y-m-d'),
            'time' => fake()->randomElement(['09:00:00', '10:00:00', '11:00:00', '14:00:00', '15:00:00']),
            'modality' => Modality::InPerson,
            'status' => AppointmentStatus::Pending,
            'reason' => fake()->sentence(3),
        ];
    }

    public function confirmed(): static
    {
        return $this->state(fn () => ['status' => AppointmentStatus::Confirmed]);
    }

    public function completed(): static
    {
        return $this->state(fn () => ['status' => AppointmentStatus::Completed]);
    }
}
