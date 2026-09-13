<?php

namespace Database\Factories;

use App\Models\Submission;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Grade>
 */
class GradeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'submission_id' => Submission::factory(),
            'graded_by' => User::factory()->dosen(),
            'score' => fake()->randomFloat(2, 60, 100),
            'feedback' => fake()->randomElement([
                'Pekerjaan sudah baik.',
                'Pemahaman materi sudah cukup baik.',
                'Perlu memperbaiki beberapa bagian.',
                'Hasil pekerjaan sangat baik.',
                'Sudah memenuhi sebagian besar kriteria.',
            ]),
            'graded_at' => now(),
        ];
    }
}