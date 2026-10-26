<?php

namespace Database\Factories;

use App\Models\Professional;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Professional> */
class ProfessionalFactory extends Factory
{
    protected $model = Professional::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'specialty' => fake()->randomElement(['Psicología Clínica', 'Psiquiatría Clínica', 'Neuropsicología']),
            'profession' => fake()->randomElement(['Psicólogo', 'Psiquiatra', 'Neuropsicólogo']),
            'description' => fake()->sentence(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->numerify('3#########'),
            'is_available' => true,
            'appointment_duration_minutes' => 45,
        ];
    }
}
