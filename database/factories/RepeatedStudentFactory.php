<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RepeatedSutdent>
 */
class RepeatedStudentFactory extends Factory
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
            'track_start' => $this->faker->randomElement(['html', 'css', 'javascript', 'php', 'mysql', 'project']),
            'repeat_status' => $this->faker->randomElement(['waiting', 'accepted']),
            'group_id' => \App\Models\Group::query()->inRandomOrder()->first()?->id,
            'instructor_id' => \App\Models\Instructor::query()->inRandomOrder()->first()?->id,
            'branch_id' => \App\Models\Branch::query()->inRandomOrder()->first()?->id,
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
