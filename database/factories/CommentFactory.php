<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $arabicText = 'هذا نص عربي عشوائي لاختبار البيانات. يمكنك استخدامه لتوليد بيانات وهمية.';

        return [
            // 'student_id' => \App\Models\Student::query()->inRandomOrder()->first()?->id,
            'user_id' => \App\Models\User::query()->inRandomOrder()->first()?->id,
            'comment' => $arabicText,
            'created_at' => now(),
            'commentable_id' => $this->faker->randomNumber(),
            'commentable_type' => $this->faker->randomElement(['App\Models\Student', 'App\Models\RepeatedStudent']),
        ];
    }
}
