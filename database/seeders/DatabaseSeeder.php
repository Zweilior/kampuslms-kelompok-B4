<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\Grade;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | 1. Akun wajib
        |--------------------------------------------------------------------------
        */

        $admin = User::updateOrCreate(
            ['email' => 'admin@kampuslms.test'],
            [
                'name' => 'Administrator LMS',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'nim_nip' => 'ADM001',
                'email_verified_at' => now(),
            ]
        );

        $dosenDemo = User::updateOrCreate(
            ['email' => 'dosen@kampuslms.test'],
            [
                'name' => 'Budi Santoso',
                'password' => Hash::make('password'),
                'role' => 'dosen',
                'nim_nip' => 'NIP001',
                'email_verified_at' => now(),
            ]
        );

        $mahasiswaDemo = User::updateOrCreate(
            ['email' => 'mahasiswa@kampuslms.test'],
            [
                'name' => 'Andi Pratama',
                'password' => Hash::make('password'),
                'role' => 'mahasiswa',
                'nim_nip' => 'NIM001',
                'email_verified_at' => now(),
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | 2. Tambahan dosen
        |--------------------------------------------------------------------------
        */

        $dosenLain = User::factory()
            ->count(2)
            ->dosen()
            ->create();

        $dosen = collect([$dosenDemo])
            ->merge($dosenLain);

        /*
        |--------------------------------------------------------------------------
        | 3. Tambahan mahasiswa
        |--------------------------------------------------------------------------
        |
        | Sudah ada 1 mahasiswa demo, sehingga dibuat 29 tambahan.
        |
        */

        $mahasiswaLain = User::factory()
            ->count(29)
            ->mahasiswa()
            ->create();

        $mahasiswa = collect([$mahasiswaDemo])
            ->merge($mahasiswaLain);

        /*
        |--------------------------------------------------------------------------
        | 4. Lima mata kuliah
        |--------------------------------------------------------------------------
        */

        $courseNames = [
            'Pemrograman Web',
            'Basis Data',
            'Rekayasa Perangkat Lunak',
            'Sistem Informasi Manajemen',
            'Analisis dan Perancangan Sistem',
        ];

        $courses = collect();

        foreach ($courseNames as $index => $courseName) {
            $lecturer = $dosen[$index % $dosen->count()];

            $course = Course::updateOrCreate(
                [
                    'code' => 'SI25140' . ($index + 1),
                ],
                [
                    'name' => $courseName,
                    'description' => 'Mata kuliah ' . $courseName . ' pada Kampus LMS.',
                    'sks' => 3,
                    'lecturer_id' => $lecturer->id,
                    'status' => 'active',
                ]
            );

            $courses->push($course);

            /*
            |--------------------------------------------------------------------------
            | 5. Enroll minimal 15 mahasiswa
            |--------------------------------------------------------------------------
            */

            $selectedStudents = $mahasiswa
                ->shuffle()
                ->take(15);

            foreach ($selectedStudents as $student) {
                $course->students()->syncWithoutDetaching([
                    $student->id => [
                        'enrolled_at' => now(),
                    ],
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | 6. Tiga tugas per mata kuliah
            |--------------------------------------------------------------------------
            |
            | Tugas 1 = deadline sudah lewat
            | Tugas 2 = masih aktif
            | Tugas 3 = draft
            |
            */

            $pastAssignment = Assignment::updateOrCreate(
                [
                    'course_id' => $course->id,
                    'title' => 'Tugas 1 - ' . $courseName,
                ],
                [
                    'created_by' => $lecturer->id,
                    'instructions' => 'Kerjakan tugas sesuai materi yang telah diberikan.',
                    'due_at' => now()->subDays(7),
                    'max_score' => 100,
                    'allow_late' => true,
                    'status' => 'published',
                ]
            );

            $activeAssignment = Assignment::updateOrCreate(
                [
                    'course_id' => $course->id,
                    'title' => 'Tugas 2 - ' . $courseName,
                ],
                [
                    'created_by' => $lecturer->id,
                    'instructions' => 'Kerjakan tugas dan kumpulkan sebelum batas waktu.',
                    'due_at' => now()->addDays(7),
                    'max_score' => 100,
                    'allow_late' => true,
                    'status' => 'published',
                ]
            );

            Assignment::updateOrCreate(
                [
                    'course_id' => $course->id,
                    'title' => 'Tugas 3 - ' . $courseName,
                ],
                [
                    'created_by' => $lecturer->id,
                    'instructions' => 'Tugas ini masih dalam tahap persiapan.',
                    'due_at' => now()->addDays(14),
                    'max_score' => 100,
                    'allow_late' => true,
                    'status' => 'draft',
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | 7. Submission
            |--------------------------------------------------------------------------
            |
            | 10 mahasiswa dari 15 peserta mengumpulkan untuk masing-masing
            | dari 2 tugas published.
            |
            | 5 course x 2 assignment x 10 mahasiswa = 100 submission.
            |
            */

            $submitters = $selectedStudents->shuffle()->take(10);

            foreach ($submitters as $studentIndex => $student) {
                /*
                | Tugas yang sudah lewat deadline:
                | sebagian dibuat terlambat, sebagian tepat waktu.
                */

                $submittedAt = $studentIndex % 3 === 0
                    ? $pastAssignment->due_at->copy()->addDays(1)
                    : $pastAssignment->due_at->copy()->subDays(1);

                $this->createSubmission(
                    $pastAssignment,
                    $student,
                    $submittedAt
                );

                /*
                | Tugas aktif:
                | seluruh submission dibuat sebelum deadline.
                */

                $submittedAt = $activeAssignment->due_at
                    ->copy()
                    ->subDays(fake()->numberBetween(1, 5));

                $this->createSubmission(
                    $activeAssignment,
                    $student,
                    $submittedAt
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 8. Nilai
        |--------------------------------------------------------------------------
        |
        | Total submission = 100.
        | 60 submission pertama diberi nilai.
        |
        */

        $submissions = Submission::query()
            ->orderBy('id')
            ->get();

        foreach ($submissions->take(60) as $submission) {
            Grade::updateOrCreate(
                [
                    'submission_id' => $submission->id,
                ],
                [
                    'graded_by' => $this->findLecturerForSubmission($submission),
                    'score' => fake()->randomFloat(2, 65, 100),
                    'feedback' => fake()->randomElement([
                        'Pekerjaan sudah baik.',
                        'Hasil pekerjaan sudah memenuhi kriteria.',
                        'Perlu memperbaiki beberapa bagian.',
                        'Pemahaman materi sudah cukup baik.',
                        'Hasil pekerjaan sangat baik.',
                    ]),
                    'graded_at' => now(),
                ]
            );
        }
    }

    private function createSubmission(
        Assignment $assignment,
        User $student,
        Carbon $submittedAt
    ): Submission {
        return Submission::updateOrCreate(
            [
                'assignment_id' => $assignment->id,
                'user_id' => $student->id,
            ],
            [
                'file_path' => 'submissions/'
                    . $assignment->id
                    . '-'
                    . $student->id
                    . '.pdf',

                'original_name' => 'tugas-' . $assignment->id . '.pdf',

                'file_size' => fake()->numberBetween(
                    100_000,
                    5_000_000
                ),

                'note' => 'Pengumpulan tugas oleh mahasiswa.',

                'submitted_at' => $submittedAt,

                'is_late' => $submittedAt->greaterThan(
                    $assignment->due_at
                ),
            ]
        );
    }

    private function findLecturerForSubmission(
        Submission $submission
    ): int {
        return $submission->assignment
            ->course
            ->lecturer_id;
    }
}