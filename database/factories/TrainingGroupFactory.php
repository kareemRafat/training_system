<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TrainingGroup>
 */
class TrainingGroupFactory extends Factory
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
            'start_date' => $this->faker->date('Y-m-d', 'now'),
            'end_date' => $this->faker->date(),
            'branch_id' => \App\Models\Branch::query()->inRandomOrder()->first()?->id,
            'instructor_id' => \App\Models\Instructor::query()->inRandomOrder()->first()?->id,
        ];
    }
}
