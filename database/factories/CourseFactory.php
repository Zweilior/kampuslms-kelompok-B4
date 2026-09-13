<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code' => fake()->unique()->regexify('SI[0-9]{7}'),
            'name' => fake()->randomElement([
                'Pemrograman Web',
                'Basis Data',
                'Rekayasa Perangkat Lunak',
                'Sistem Informasi Manajemen',
                'Analisis dan Perancangan Sistem',
                'Jaringan Komputer',
                'Pemrograman Berorientasi Objek',
                'Manajemen Proyek Sistem Informasi',
            ]),
            'description' => fake()->sentence(12),
            'sks' => fake()->numberBetween(2, 4),
            'lecturer_id' => User::factory()->dosen(),
            'status' => 'active',
        ];
    }
}