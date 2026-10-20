<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\FinalGrade;
use App\Models\Grade;
use App\Models\GradeComponent;
use App\Models\Material;
use App\Models\Submission;
use App\Models\User;
use App\Support\GradeScale;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    private const FIXED_COURSES = [
        'Pemrograman Web',
        'Basis Data',
        'Rekayasa Perangkat Lunak',
        'Sistem Informasi Manajemen',
        'Analisis dan Perancangan Sistem',
    ];

    private const RANDOM_COURSE_POOL = [
        'Jaringan Komputer',
        'Pemrograman Berorientasi Objek',
        'Manajemen Proyek Sistem Informasi',
        'Struktur Data',
        'Algoritma dan Pemrograman',
        'Sistem Operasi',
        'Keamanan Informasi',
        'Kecerdasan Buatan',
        'Data Mining',
        'Interaksi Manusia dan Komputer',
        'Pemrograman Mobile',
        'Cloud Computing',
        'Audit Sistem Informasi',
        'E-Commerce',
        'Business Intelligence',
        'Pengujian Perangkat Lunak',
        'Etika Profesi',
        'Statistika',
        'Matematika Diskrit',
        'Jaringan Nirkabel',
    ];

    /** Distribusi status 15 mata kuliah acak. */
    private const RANDOM_STATUS_DISTRIBUTION = [
        'active' => 8,
        'archived' => 4,
        'draft' => 3,
    ];

    /**
     * Template rubrik penilaian: [nama komponen, bobot %].
     * Setiap template totalnya 100.
     */
    private const RUBRIC_TEMPLATES = [
        [
            ['Tugas', 20],
            ['Praktikum', 20],
            ['UTS', 25],
            ['UAS', 35],
        ],
        [
            ['Tugas Individu', 15],
            ['Tugas Kelompok', 25],
            ['Kuis', 20],
            ['Proyek Akhir', 40],
        ],
        [
            ['Latihan Praktikum', 20],
            ['Studi Kasus', 20],
            ['UTS', 30],
            ['UAS', 30],
        ],
    ];

    private const SCHEDULE = [
        'active' => [
            ['offset' => -21, 'submit' => 0.90, 'graded' => 1.00],
            ['offset' => -7,  'submit' => 0.80, 'graded' => 0.60],
            ['offset' => 7,   'submit' => 0.35, 'graded' => 0.00],
            ['offset' => 14,  'submit' => 0.00, 'graded' => 0.00],
        ],
        'archived' => [
            ['offset' => -110, 'submit' => 0.95, 'graded' => 1.00],
            ['offset' => -90,  'submit' => 0.93, 'graded' => 1.00],
            ['offset' => -70,  'submit' => 0.90, 'graded' => 1.00],
            ['offset' => -50,  'submit' => 0.90, 'graded' => 1.00],
        ],
        'draft' => [
            ['offset' => 14, 'submit' => 0, 'graded' => 0],
            ['offset' => 28, 'submit' => 0, 'graded' => 0],
            ['offset' => 42, 'submit' => 0, 'graded' => 0],
            ['offset' => 56, 'submit' => 0, 'graded' => 0],
        ],
    ];

    private const STUDENTS_PER_COURSE = 15;
    private const MATERIAL_TEMPLATES = [
        ['Pengantar dan Kontrak Perkuliahan', 'file', 'pdf', 'application/pdf'],
        ['Konsep Dasar', 'file', 'pptx', 'application/vnd.openxmlformats-officedocument.presentationml.presentation'],
        ['Modul Praktikum', 'file', 'docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
        ['Video Pembelajaran', 'link', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', null],
        ['Referensi Bacaan Tambahan', 'link', 'https://en.wikipedia.org/wiki/Information_system', null],
    ];

    private int $totalSubmissions = 0;
    private int $totalGrades = 0;
    private int $totalMaterials = 0;

    public function run(): void
    {

        $this->upsertUser('admin@kampuslms.test', [
            'name' => 'Administrator',
            'role' => 'admin',
            'nim_nip' => 'ADM001',
        ]);

        $dosenDemo = $this->upsertUser('dosen@kampuslms.test', [
            'name' => 'Budi Santoso',
            'role' => 'dosen',
            'nim_nip' => 'NIP001',
        ]);

        $mahasiswaDemo = $this->upsertUser('mahasiswa@kampuslms.test', [
            'name' => 'Andi Pratama',
            'role' => 'mahasiswa',
            'nim_nip' => 'NIM001',
        ]);

        $dosen = collect([$dosenDemo])->merge(
            User::factory()->count(4)->dosen()->create()
        );

        $mahasiswa = collect([$mahasiswaDemo])->merge(
            User::factory()->count(39)->mahasiswa()->create()
        );

        $courseDefinitions = $this->buildCourseDefinitions();

        foreach ($courseDefinitions as $index => $definition) {
            $lecturer = $dosen[$index % $dosen->count()];

            $course = Course::updateOrCreate(
                ['code' => 'SI2514'.str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)],
                [
                    'name' => $definition['name'],
                    'description' => 'Mata kuliah '.$definition['name'].' pada Kampus LMS.',
                    'sks' => $definition['sks'],
                    'lecturer_id' => $lecturer->id,
                    'status' => $definition['status'],
                ]
            );

            $students = $this->enrollStudents($course, $mahasiswa, $mahasiswaDemo, $definition['status']);
            $this->createMaterials($course, $lecturer);
            $components = $this->createGradeComponents($course);
            $assignments = $this->createAssignments($course, $lecturer, $components, $definition['status']);

            if ($definition['status'] !== 'draft') {
                $this->createSubmissionsAndGrades(
                    $course,
                    $lecturer,
                    $assignments,
                    $students,
                    $definition['status']
                );
            }

            if ($definition['status'] === 'archived') {
                $this->createFinalGrades($course, $students);
            }
        }

        $this->command?->info(sprintf(
            'Seeder selesai: %d mata kuliah, %d materi, %d submission, %d nilai.',
            count($courseDefinitions),
            $this->totalMaterials,
            $this->totalSubmissions,
            $this->totalGrades
        ));

        $this->call(NotificationSeeder::class);
    }

    private function upsertUser(string $email, array $attributes): User
    {
        $user = User::firstOrNew(['email' => $email]);

        $user->forceFill($attributes + [
            'password' => 'password', 
            'email_verified_at' => now(),
        ])->save();

        return $user;
    }

    /**
     * @return array<int, array{name: string, status: string, sks: int}>
     */
    private function buildCourseDefinitions(): array
    {
        $definitions = [];

        foreach (self::FIXED_COURSES as $name) {
            $definitions[] = ['name' => $name, 'status' => 'active', 'sks' => 3];
        }

        $randomNames = collect(self::RANDOM_COURSE_POOL)->shuffle()->take(15)->values();

        $statuses = collect(self::RANDOM_STATUS_DISTRIBUTION)
            ->flatMap(fn (int $count, string $status) => array_fill(0, $count, $status))
            ->shuffle()
            ->values();

        foreach ($randomNames as $i => $name) {
            $definitions[] = [
                'name' => $name,
                'status' => $statuses[$i],
                'sks' => fake()->numberBetween(2, 4),
            ];
        }

        return $definitions;
    }

    /**
     * Daftarkan tepat 15 mahasiswa. Mahasiswa demo ikut di semua mata kuliah
     * kecuali yang berstatus draft.
     *
     * @return Collection<int, User>
     */
    private function enrollStudents(
        Course $course,
        Collection $mahasiswa,
        User $mahasiswaDemo,
        string $status
    ): Collection {
        $others = $mahasiswa
            ->reject(fn (User $student) => $student->id === $mahasiswaDemo->id)
            ->shuffle();

        $selected = $status === 'draft'
            ? $others->take(self::STUDENTS_PER_COURSE)
            : collect([$mahasiswaDemo])->merge($others->take(self::STUDENTS_PER_COURSE - 1));

        $payload = $selected
            ->mapWithKeys(fn (User $student) => [
                $student->id => ['enrolled_at' => now()->subDays(fake()->numberBetween(30, 130))],
            ])
            ->all();

        $course->students()->syncWithoutDetaching($payload);

        return $selected->values();
    }

    private function createMaterials(Course $course, User $lecturer): void
    {
        foreach (self::MATERIAL_TEMPLATES as $i => [$title, $type, $source, $mime]) {
            $number = $i + 1;
            $fullTitle = 'Pertemuan '.$number.' - '.$title.' '.$course->name;

            $material = Material::firstOrNew([
                'course_id' => $course->id,
                'title' => $fullTitle,
            ]);

            $material->uploaded_by = $lecturer->id;

            $material->fill([
                'description' => 'Materi '.strtolower($title).' untuk mata kuliah '.$course->name.'.',
                'type' => $type,
                'file_path' => $type === 'file'
                    ? 'materials/'.$course->code.'-pertemuan-'.$number.'.'.$source
                    : null,
                'original_name' => $type === 'file'
                    ? 'pertemuan-'.$number.'-'.str($course->name)->slug().'.'.$source
                    : null,
                'file_size' => $type === 'file' ? fake()->numberBetween(200_000, 8_000_000) : null,
                'mime_type' => $type === 'file' ? $mime : null,
                'external_url' => $type === 'link' ? $source : null,
            ]);

            $material->save();

            if ($type === 'file') {
                $this->putDummyFile($material->file_path, $material->original_name);
                $material->update([
                    'file_size' => Storage::disk('local')->size($material->file_path),
                ]);
            }            

            $this->totalMaterials++;
        }
    }

    /**
     * @return Collection<int, GradeComponent>
     */
    private function createGradeComponents(Course $course): Collection
    {
        $template = fake()->randomElement(self::RUBRIC_TEMPLATES);

        return collect($template)->map(
            fn (array $item) => GradeComponent::updateOrCreate(
                ['course_id' => $course->id, 'name' => $item[0]],
                ['weight' => $item[1]]
            )
        )->values();
    }

    /**
     * @return Collection<int, Assignment>
     */
    private function createAssignments(
        Course $course,
        User $lecturer,
        Collection $components,
        string $status
    ): Collection {
        $schedule = self::SCHEDULE[$status];

        return $components->values()->map(function (GradeComponent $component, int $i) use ($course, $lecturer, $schedule, $status) {
            return Assignment::updateOrCreate(
                [
                    'course_id' => $course->id,
                    'title' => $component->name.' - '.$course->name,
                ],
                [
                    'grade_component_id' => $component->id,
                    'created_by' => $lecturer->id,
                    'instructions' => 'Kerjakan '.strtolower($component->name).' sesuai materi '
                        .$course->name.' dan kumpulkan sebelum batas waktu.',
                    'due_at' => now()->addDays($schedule[$i]['offset'])->setTime(23, 59),
                    'max_score' => 100,
                    'allow_late' => true,
                    // mata kuliah draft -> tugas ikut draft
                    'status' => $status === 'draft' ? 'draft' : 'published',
                ]
            );
        });
    }

    private function putDummyFile(string $path, string $name): void
    {
        $content = str_ends_with($path, '.pdf')
            ? "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj\n3 0 obj<</Type/Page/Parent 2 0 R/MediaBox[0 0 300 100]>>endobj\ntrailer<</Root 1 0 R>>\n%%EOF\n"
            : "Berkas dummy seeder: {$name}\n";

        Storage::disk('local')->put($path, $content);
    }


    private function createSubmissionsAndGrades(
        Course $course,
        User $lecturer,
        Collection $assignments,
        Collection $students,
        string $status
    ): void {
        $schedule = self::SCHEDULE[$status];

        // kemampuan dasar tiap mahasiswa di mata kuliah ini
        $ability = $students->mapWithKeys(
            fn (User $student) => [$student->id => fake()->numberBetween(62, 95)]
        );

        foreach ($assignments->values() as $i => $assignment) {
            $plan = $schedule[$i];

            $submitCount = (int) round($students->count() * $plan['submit']);

            if ($submitCount === 0) {
                continue;
            }

            $submitters = $students->shuffle()->take($submitCount)->values();
            $gradeCount = (int) round($submitters->count() * $plan['graded']);

            foreach ($submitters as $s => $student) {
                $submission = $this->createSubmission($assignment, $student);

                if ($s < $gradeCount) {
                    $this->createGrade($submission, $lecturer, $ability[$student->id]);
                }
            }
        }
    }

    private function createSubmission(Assignment $assignment, User $student): Submission
    {
        $isPastDue = $assignment->due_at->isPast();

        if ($isPastDue) {
            // 75% tepat waktu, 25% terlambat
            $late = fake()->boolean(25);

            $submittedAt = $late
                ? $assignment->due_at->copy()->addDays(fake()->numberBetween(1, 2))
                : $assignment->due_at->copy()->subDays(fake()->numberBetween(1, 5));
        } else {
            // deadline belum lewat -> dikumpulkan beberapa jam/hari lalu
            $late = false;
            $submittedAt = now()->subHours(fake()->numberBetween(2, 72));
        }

        $submittedAt->setTime(fake()->numberBetween(7, 22), fake()->numberBetween(0, 59));

        if ($submittedAt->isFuture()) {
            $submittedAt = now()->subHour();
        }

        $this->totalSubmissions++;

        return Submission::updateOrCreate(
            [
                'assignment_id' => $assignment->id,
                'user_id' => $student->id,
            ],
            [
                'file_path' => 'submissions/'.$assignment->id.'-'.$student->id.'.pdf',
                'original_name' => 'tugas-'.$assignment->id.'-'.$student->id.'.pdf',
                'file_size' => fake()->numberBetween(100_000, 5_000_000),
                'note' => fake()->optional(0.4)->sentence(),
                'submitted_at' => $submittedAt,
                'is_late' => $late,
            ]
        );
        $this->putDummyFile($submission->file_path, $submission->original_name);
        $submission->update([
            'file_size' => Storage::disk('local')->size($submission->file_path),
        ]);

        return $submission;
    }

    private function createGrade(Submission $submission, User $lecturer, int $ability): Grade
    {
        $score = $ability + fake()->randomFloat(2, -10, 8);

        if ($submission->is_late) {
            $score -= 5;
        }

        $score = round(max(40, min(100, $score)), 2);

        $gradedAt = $submission->submitted_at->copy()->addDays(fake()->numberBetween(1, 4));

        if ($gradedAt->isFuture()) {
            $gradedAt = Carbon::now();
        }

        $this->totalGrades++;

        return Grade::updateOrCreate(
            ['submission_id' => $submission->id],
            [
                'graded_by' => $lecturer->id,
                'score' => $score,
                'feedback' => $this->feedbackFor($score),
                'graded_at' => $gradedAt,
            ]
        );
    }

    private function feedbackFor(float $score): string
    {
        return match (true) {
            $score >= 86 => fake()->randomElement([
                'Hasil pekerjaan sangat baik.',
                'Pemahaman materi sangat baik, pertahankan.',
            ]),
            $score >= 76 => fake()->randomElement([
                'Pekerjaan sudah baik.',
                'Hasil pekerjaan sudah memenuhi kriteria.',
            ]),
            $score >= 66 => fake()->randomElement([
                'Pemahaman materi sudah cukup baik.',
                'Sudah memenuhi sebagian besar kriteria.',
            ]),
            default => fake()->randomElement([
                'Perlu memperbaiki beberapa bagian.',
                'Pelajari kembali materi dan lengkapi bagian yang kurang.',
            ]),
        };
    }

    private function createFinalGrades(Course $course, Collection $students): void
    {
        $assignments = $course->assignments()
            ->whereNotNull('grade_component_id')
            ->with(['gradeComponent', 'submissions.grade'])
            ->get();

        foreach ($students as $student) {
            $weighted = 0.0;
            $gradedWeight = 0.0;

            foreach ($assignments->groupBy('grade_component_id') as $group) {
                $weight = (float) $group->first()->gradeComponent->weight;

                $percentages = $group
                    ->map(function (Assignment $assignment) use ($student) {
                        $grade = $assignment->submissions
                            ->firstWhere('user_id', $student->id)?->grade;

                        return $grade
                            ? ((float) $grade->score / max($assignment->max_score, 1)) * 100
                            : null;
                    })
                    ->filter(fn ($value) => $value !== null);

                if ($percentages->isEmpty()) {
                    continue;
                }

                $weighted += $percentages->avg() * $weight / 100;
                $gradedWeight += $weight;
            }

            if ($gradedWeight <= 0) {
                continue;
            }

            $total = round(($weighted / $gradedWeight) * 100, 2);

            FinalGrade::updateOrCreate(
                ['course_id' => $course->id, 'user_id' => $student->id],
                [
                    'total_score' => $total,
                    'letter_grade' => GradeScale::letterGradeForScore($total),
                ]
            );
        }
    }

}
