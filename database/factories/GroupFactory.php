<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Group>
 */
class GroupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $groups = ['london', 'cut', 'lock', 'home', 'nike'];

        return [
            'name' => $groups[mt_rand(0, count($groups) - 1)],
            'start_date' => $this->faker->date(),
            'end_date' => $this->faker->date(),
            'instructor_id' => \App\Models\Instructor::query()->inRandomOrder()->first()?->id,
            'branch_id' => \App\Models\Branch::query()->inRandomOrder()->first()?->id,
        ];
    }
}
