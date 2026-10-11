<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\FinalGrade;
use App\Models\Grade;
use App\Models\GradeComponent;
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
        $gradingAssignment = Assignment::factory()->for($course)->past()->create([
            'title' => 'Tugas menunggu nilai',
        ]);
        Assignment::factory()->for($course)->draft()->create();
        Assignment::factory()->for($otherCourse)->active()->create();

        Submission::factory()->for($gradingAssignment)->count(2)->create();
        $gradedSubmission = Submission::factory()->for($gradingAssignment)->create();
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
            ->assertSee('Halo, '.$lecturer->name)
            ->assertSee('dosen-dashboard__header', false)
            ->assertSee(route('dosen.courses.index'))
            ->assertSee('Mata kuliah saya')
            ->assertSee('Mata Kuliah')
            ->assertSee('Tugas Dibuka')
            ->assertSee('Tugas Perlu Dinilai')
            ->assertSee('Tugas menunggu nilai')
            ->assertSee(route('dosen.courses.assignments.submissions.index', [$course, $gradingAssignment]))
            ->assertSee('href="#grading-assignments"', false)
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
            ->create(['name' => 'Zoologi', 'status' => 'active']);
        $archivedCourse = Course::factory()
            ->for($lecturer, 'lecturer')
            ->create(['name' => 'Anatomi', 'status' => 'archived']);
        Course::factory()
            ->for($lecturer, 'lecturer')
            ->create(['name' => 'Biologi', 'status' => 'active']);
        $draftCourse = Course::factory()
            ->for($lecturer, 'lecturer')
            ->create(['name' => 'Draft tersembunyi', 'status' => 'draft']);
        $otherCourse = Course::factory()
            ->for($otherLecturer, 'lecturer')
            ->create(['name' => 'Kelas Dosen Lain']);

        $this->actingAs($lecturer)
            ->get(route('dosen.courses.index'))
            ->assertOk()
            ->assertSee('Zoologi')
            ->assertSee('Anatomi')
            ->assertDontSee('Kelas Dosen Lain')
            ->assertDontSee('Draft tersembunyi')
            ->assertSee('Mata Kuliah Aktif')
            ->assertSee('Mata Kuliah Arsip')
            ->assertSee('dosen-course-panel', false)
            ->assertSee('Dashboard')
            ->assertSee('Mata Kuliah')
            ->assertSee('Nilai');
        $content = $this->get(route('dosen.courses.index'))->getContent();
        $this->assertLessThan(
            strpos($content, 'Zoologi'),
            strpos($content, 'Biologi')
        );
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
            'total_score' => 10,
            'letter_grade' => 'B',
        ]);
        FinalGrade::create([
            'course_id' => $activeCourse->id,
            'user_id' => $activeStudents[1]->id,
            'total_score' => 20,
            'letter_grade' => 'A',
        ]);
        FinalGrade::create([
            'course_id' => $archivedCourse->id,
            'user_id' => $archivedStudent->id,
            'total_score' => 75,
            'letter_grade' => 'B',
        ]);

        $activeComponent = GradeComponent::create([
            'course_id' => $activeCourse->id,
            'name' => 'Tugas',
            'weight' => 100,
        ]);
        $activeAssignment = Assignment::factory()->for($activeCourse)->create([
            'grade_component_id' => $activeComponent->id,
            'created_by' => $lecturer->id,
            'max_score' => 100,
        ]);
        foreach ([80, 90] as $index => $score) {
            $submission = Submission::factory()
                ->for($activeAssignment)
                ->for($activeStudents[$index], 'student')
                ->create();
            Grade::factory()->create([
                'submission_id' => $submission->id,
                'graded_by' => $lecturer->id,
                'score' => $score,
            ]);
        }

        $archivedComponent = GradeComponent::create([
            'course_id' => $archivedCourse->id,
            'name' => 'Tugas',
            'weight' => 100,
        ]);
        $archivedAssignment = Assignment::factory()->for($archivedCourse)->create([
            'grade_component_id' => $archivedComponent->id,
            'created_by' => $lecturer->id,
            'max_score' => 100,
        ]);
        $archivedSubmission = Submission::factory()
            ->for($archivedAssignment)
            ->for($archivedStudent, 'student')
            ->create();
        Grade::factory()->create([
            'submission_id' => $archivedSubmission->id,
            'graded_by' => $lecturer->id,
            'score' => 75,
        ]);

        $this->actingAs($lecturer)
            ->get(route('dosen.grades.index'))
            ->assertOk()
            ->assertSee('Monitoring Nilai')
            ->assertSee(route('dosen.courses.grades.export', $activeCourse))
            ->assertSee(route('dosen.courses.grades.export', $archivedCourse))
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
                    && (float) $courses->first()->monitoring_average === 85.0
                    && $courses->first()->monitoring_graded_students_count === 2;
            })
            ->assertViewHas('archivedCourses', function ($courses): bool {
                return $courses->count() === 1
                    && $courses->first()->students_count === 1
                    && (float) $courses->first()->monitoring_average === 75.0
                    && $courses->first()->monitoring_graded_students_count === 1;
            })
            ->assertDontSee('draft');
    }

    public function test_lecturer_can_export_all_submission_grades_for_one_course_only(): void
    {
        $lecturer = User::factory()->dosen()->create();
        $otherLecturer = User::factory()->dosen()->create();
        $course = Course::factory()->for($lecturer, 'lecturer')->create([
            'code' => 'WEB101',
            'name' => 'Pemrograman Web',
        ]);
        $otherCourse = Course::factory()->for($otherLecturer, 'lecturer')->create([
            'code' => 'DB101',
            'name' => 'Basis Data',
        ]);
        $gradedAssignment = Assignment::factory()->for($course)->create([
            'title' => 'Tugas Pertama',
            'created_by' => $lecturer->id,
        ]);
        $ungradedAssignment = Assignment::factory()->for($course)->create([
            'title' => 'Tugas Kedua',
            'created_by' => $lecturer->id,
        ]);
        $otherAssignment = Assignment::factory()->for($otherCourse)->create([
            'title' => 'Tugas Rahasia',
            'created_by' => $otherLecturer->id,
        ]);

        $student = User::factory()->mahasiswa()->create([
            'name' => 'Mahasiswa Uji',
            'nim_nip' => 'NIM1001',
        ]);
        $gradedSubmission = Submission::factory()
            ->for($gradedAssignment)
            ->for($student, 'student')
            ->create();
        Submission::factory()
            ->for($ungradedAssignment)
            ->for($student, 'student')
            ->create();
        Submission::factory()
            ->for($otherAssignment)
            ->for(User::factory()->mahasiswa()->create(), 'student')
            ->create();
        Grade::factory()->create([
            'submission_id' => $gradedSubmission->id,
            'graded_by' => $lecturer->id,
            'score' => 92,
            'feedback' => 'Bagus',
        ]);

        $response = $this->actingAs($lecturer)
            ->get(route('dosen.courses.grades.export', $course))
            ->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8')
            ->assertHeader('content-disposition', 'attachment; filename=nilai-pemrograman-web.csv');

        $csv = $response->streamedContent();
        $this->assertStringContainsString("\xEF\xBB\xBF", $csv);
        $this->assertStringContainsString('Tugas Pertama', $csv);
        $this->assertStringContainsString('Sudah dinilai', $csv);
        $this->assertStringContainsString('Tugas Kedua', $csv);
        $this->assertStringContainsString('Belum dinilai', $csv);
        $this->assertStringNotContainsString('Tugas Rahasia', $csv);
    }

    public function test_lecturer_cannot_export_all_grades_for_another_lecturers_course(): void
    {
        $lecturer = User::factory()->dosen()->create();
        $otherLecturer = User::factory()->dosen()->create();
        $otherCourse = Course::factory()->for($otherLecturer, 'lecturer')->create();

        $this->actingAs($lecturer)
            ->get(route('dosen.courses.grades.export', $otherCourse))
            ->assertForbidden();
    }

    public function test_course_page_separates_active_and_archived_courses_alphabetically(): void
    {
        $lecturer = User::factory()->dosen()->create();
        Course::factory()->for($lecturer, 'lecturer')->create([
            'name' => 'Zoologi',
            'status' => 'active',
        ]);
        Course::factory()->for($lecturer, 'lecturer')->create([
            'name' => 'Biologi',
            'status' => 'active',
        ]);
        Course::factory()->for($lecturer, 'lecturer')->create([
            'name' => 'Anatomi',
            'status' => 'archived',
        ]);
        Course::factory()->for($lecturer, 'lecturer')->create([
            'name' => 'Akademi',
            'status' => 'archived',
        ]);

        $response = $this->actingAs($lecturer)
            ->get(route('dosen.courses.index'))
            ->assertOk()
            ->assertSee('Mata Kuliah Aktif')
            ->assertSee('Mata Kuliah Arsip')
            ->assertSee('Zoologi')
            ->assertSee('Biologi')
            ->assertSee('Anatomi')
            ->assertSee('Akademi')
            ->assertSee('Lihat mata kuliah')
            ->assertSee('dosen-course-choices', false);

        $this->assertLessThan(
            strpos($response->getContent(), 'Zoologi'),
            strpos($response->getContent(), 'Biologi')
        );
        $this->assertLessThan(
            strpos($response->getContent(), 'Anatomi'),
            strpos($response->getContent(), 'Akademi')
        );
    }
}
