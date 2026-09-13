<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Assignment>
 */
class AssignmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'created_by' => User::factory()->dosen(),
            'title' => fake()->randomElement([
                'Tugas Individu',
                'Tugas Kelompok',
                'Latihan Praktikum',
                'Studi Kasus',
                'Proyek Akhir',
            ]),
            'instructions' => fake()->paragraph(),
            'due_at' => now()->addDays(7),
            'max_score' => 100,
            'allow_late' => true,
            'status' => 'published',
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }

    public function past(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
            'due_at' => now()->subDays(fake()->numberBetween(2, 14)),
        ]);
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
            'due_at' => now()->addDays(fake()->numberBetween(2, 14)),
        ]);
    }
}