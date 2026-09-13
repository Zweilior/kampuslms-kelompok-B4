<?php

namespace Database\Factories;

use App\Models\Assignment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Submission>
 */
class SubmissionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'assignment_id' => Assignment::factory(),
            'user_id' => User::factory()->mahasiswa(),
            'file_path' => 'submissions/' . fake()->uuid() . '.pdf',
            'original_name' => fake()->words(3, true) . '.pdf',
            'file_size' => fake()->numberBetween(100_000, 5_000_000),
            'note' => fake()->optional()->sentence(),
            'submitted_at' => now(),
            'is_late' => false,
        ];
    }

    public function late(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_late' => true,
        ]);
    }

    public function onTime(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_late' => false,
        ]);
    }
}