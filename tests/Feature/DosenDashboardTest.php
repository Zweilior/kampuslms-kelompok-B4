<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\FinalGrade;
use App\Models\Grade;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DosenDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_shows_counts_limited_to_the_logged_in_lecturers_courses(): void
    {
        $lecturer = User::factory()->dosen()->create();
        $otherLecturer = User::factory()->dosen()->create();
        $course = Course::factory()->for($lecturer, 'lecturer')->create();
        $otherCourse = Course::factory()->for($otherLecturer, 'lecturer')->create();

        $openAssignment = Assignment::factory()->for($course)->active()->create();
        Assignment::factory()->for($course)->draft()->create();
        Assignment::factory()->for($course)->past()->create();
        Assignment::factory()->for($otherCourse)->active()->create();

        Submission::factory()->for($openAssignment)->count(2)->create();
        $gradedSubmission = Submission::factory()->for($openAssignment)->create();
        Grade::factory()->create([
            'submission_id' => $gradedSubmission->id,
            'graded_by' => $lecturer->id,
        ]);
        Submission::factory()
            ->for(Assignment::factory()->for($otherCourse)->active())
            ->create();

        $this->actingAs($lecturer)
            ->get(route('dosen.dashboard'))
            ->assertOk()
            ->assertSee('Halo, ' . $lecturer->name)
            ->assertSee('dosen-dashboard__header', false)
            ->assertSee(route('dosen.courses.index'))
            ->assertSee('Mata kuliah saya')
            ->assertSee('Mata Kuliah')
            ->assertSee('Tugas Dibuka')
            ->assertSee('Pengumpulan tugas yang belum dinilai.')
            ->assertViewHas('totalCourses', 1)
            ->assertViewHas('openAssignments', 1)
            ->assertViewHas('ungradedSubmissions', 2);
    }

    public function test_course_menu_shows_only_courses_taught_by_the_logged_in_lecturer(): void
    {
        $lecturer = User::factory()->dosen()->create();
        $otherLecturer = User::factory()->dosen()->create();
        $course = Course::factory()
            ->for($lecturer, 'lecturer')
            ->create(['name' => 'Kelas Dosen Ini']);
        $otherCourse = Course::factory()
            ->for($otherLecturer, 'lecturer')
            ->create(['name' => 'Kelas Dosen Lain']);

        $this->actingAs($lecturer)
            ->get(route('dosen.courses.index'))
            ->assertOk()
            ->assertSee('Kelas Dosen Ini')
            ->assertDontSee('Kelas Dosen Lain')
            ->assertSee('Dashboard')
            ->assertSee('Mata Kuliah')
            ->assertSee('Nilai')
            ->assertDontSee('>Materi</span>', false);
    }

    public function test_grades_page_separates_active_and_archived_classes_and_shows_class_statistics(): void
    {
        $lecturer = User::factory()->dosen()->create();
        $otherLecturer = User::factory()->dosen()->create();
        $activeCourse = Course::factory()->for($lecturer, 'lecturer')->create([
            'code' => 'ACT101',
            'name' => 'Kelas Aktif',
        ]);
        $archivedCourse = Course::factory()->for($lecturer, 'lecturer')->create([
            'code' => 'ARC101',
            'name' => 'Kelas Arsip',
            'status' => 'archived',
        ]);
        Course::factory()->for($lecturer, 'lecturer')->create([
            'status' => 'draft',
        ]);
        Course::factory()->for($otherLecturer, 'lecturer')->create([
            'status' => 'active',
        ]);

        $activeStudents = User::factory()->mahasiswa()->count(2)->create();
        foreach ($activeStudents as $student) {
            $activeCourse->students()->attach($student, ['enrolled_at' => now()]);
        }
        $archivedStudent = User::factory()->mahasiswa()->create();
        $archivedCourse->students()->attach($archivedStudent, ['enrolled_at' => now()]);

        FinalGrade::create([
            'course_id' => $activeCourse->id,
            'user_id' => $activeStudents[0]->id,
            'total_score' => 80,
            'letter_grade' => 'B',
        ]);
        FinalGrade::create([
            'course_id' => $activeCourse->id,
            'user_id' => $activeStudents[1]->id,
            'total_score' => 90,
            'letter_grade' => 'A',
        ]);
        FinalGrade::create([
            'course_id' => $archivedCourse->id,
            'user_id' => $archivedStudent->id,
            'total_score' => 75,
            'letter_grade' => 'B',
        ]);

        $this->actingAs($lecturer)
            ->get(route('dosen.grades.index'))
            ->assertOk()
            ->assertSee('Monitoring Nilai')
            ->assertSee('Mata Kuliah Aktif')
            ->assertSee('Mata Kuliah Arsip')
            ->assertSee('Kelas Aktif')
            ->assertSee('Kelas Arsip')
            ->assertSee('85.00')
            ->assertSee('75.00')
            ->assertSee('Lihat Materi')
            ->assertSee('Tugas dan Nilai')
            ->assertViewHas('activeCourses', function ($courses): bool {
                return $courses->count() === 1
                    && $courses->first()->students_count === 2
                    && (float) $courses->first()->final_grades_avg_total_score === 85.0;
            })
            ->assertViewHas('archivedCourses', function ($courses): bool {
                return $courses->count() === 1
                    && $courses->first()->students_count === 1
                    && (float) $courses->first()->final_grades_avg_total_score === 75.0;
            })
            ->assertDontSee('draft');
    }

    public function test_course_page_keeps_its_course_cards_instead_of_active_and_archived_panels(): void
    {
        $lecturer = User::factory()->dosen()->create();
        $course = Course::factory()->for($lecturer, 'lecturer')->create([
            'name' => 'Kelas Pengampu',
        ]);

        $this->actingAs($lecturer)
            ->get(route('dosen.courses.index'))
            ->assertOk()
            ->assertSee('Kelas Pengampu')
            ->assertSee('Lihat mata kuliah')
            ->assertDontSee('Mata Kuliah Arsip')
            ->assertDontSee('dosen-course-choices', false);
    }
}
