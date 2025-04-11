<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'phone' => $this->faker->phoneNumber(),
            'status' => $this->faker->randomElement(['normal', 'important']),
            'group_id' => \App\Models\Group::query()->inRandomOrder()->first()?->id,
            'branch_id' => \App\Models\Branch::query()->inRandomOrder()->first()?->id,
            'created_at' => $this->faker->dateTimeBetween('-6 months', 'now'), // Random timestamp within the past year
        ];
    }
}
