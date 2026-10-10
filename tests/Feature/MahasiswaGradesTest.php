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

class MahasiswaGradesTest extends TestCase
{
    use RefreshDatabase;

    public function test_monitoring_page_shows_enrolled_courses_and_final_grade_for_selected_status(): void
    {
        $student = User::factory()->mahasiswa()->create();
        $lecturer = User::factory()->dosen()->create();
        $activeCourse = Course::factory()->create(['lecturer_id' => $lecturer->id]);
        $archivedCourse = Course::factory()->create([
            'lecturer_id' => $lecturer->id,
            'status' => 'archived',
        ]);
        $student->courses()->attach([
            $activeCourse->id => ['enrolled_at' => now()],
            $archivedCourse->id => ['enrolled_at' => now()],
        ]);

        FinalGrade::create([
            'course_id' => $activeCourse->id,
            'user_id' => $student->id,
            'total_score' => 86.5,
            'letter_grade' => 'A',
        ]);
        FinalGrade::create([
            'course_id' => $archivedCourse->id,
            'user_id' => $student->id,
            'total_score' => 52.25,
            'letter_grade' => 'C',
        ]);

        $monitoringResponse = $this->actingAs($student)
            ->get(route('mahasiswa.grades.index'));

        $monitoringResponse
            ->assertOk()
            ->assertSee('Monitoring Nilai')
            ->assertSee($activeCourse->name)
            ->assertSee($archivedCourse->name)
            ->assertSee('Mata Kuliah Aktif')
            ->assertSee('Mata Kuliah Arsip')
            ->assertSee('86.50')
            ->assertSee('A')
            ->assertSee('52.25')
            ->assertSee('selected: null', false)
            ->assertSee('x-cloak', false)
            ->assertSee('x-show="selected !== null"', false);
        $this->assertMatchesRegularExpression(
            '/href="'.preg_quote(route('mahasiswa.grades.index'), '/').'"\s+class="navbar__link is-active"/',
            $monitoringResponse->getContent()
        );

        $this->get(route('mahasiswa.grades.index', ['status' => 'archived']))
            ->assertOk()
            ->assertSee($archivedCourse->name)
            ->assertSee('52.25')
            ->assertSee('86.50')
            ->assertSee("selected: 'archived'", false);
    }

    public function test_dashboard_summarizes_courses_assignments_and_links_to_monitoring(): void
    {
        $student = User::factory()->mahasiswa()->create();
        $lecturer = User::factory()->dosen()->create();
        $course = Course::factory()->create(['lecturer_id' => $lecturer->id]);
        $student->courses()->attach($course->id, ['enrolled_at' => now()]);
        $pendingAssignment = Assignment::factory()->create([
            'course_id' => $course->id,
            'created_by' => $lecturer->id,
            'title' => 'Tugas Mendatang',
            'due_at' => now()->addDay(),
        ]);
        $gradedAssignment = Assignment::factory()->create([
            'course_id' => $course->id,
            'created_by' => $lecturer->id,
            'title' => 'Tugas Selesai Dinilai',
        ]);
        $submission = Submission::factory()->create([
            'assignment_id' => $gradedAssignment->id,
            'user_id' => $student->id,
        ]);
        Grade::create([
            'submission_id' => $submission->id,
            'graded_by' => $lecturer->id,
            'score' => 90,
            'graded_at' => now(),
        ]);

        $this->actingAs($student)
            ->get(route('mahasiswa.dashboard'))
            ->assertOk()
            ->assertSee($course->name)
            ->assertSee('Tugas Mendatang')
            ->assertSee('Monitoring nilai')
            ->assertSee(route('mahasiswa.grades.index'))
            ->assertSee('1</strong><span>Kelas', false)
            ->assertSee('1</strong><span>Tugas', false);
    }

    public function test_course_detail_shows_code_and_credits_under_title_with_vertical_action_rows(): void
    {
        $student = User::factory()->mahasiswa()->create();
        $lecturer = User::factory()->dosen()->create();
        $course = Course::factory()->create([
            'lecturer_id' => $lecturer->id,
            'name' => 'Basis Data',
            'code' => 'BD101',
            'sks' => 3,
        ]);

        $course->students()->attach($student->id, ['enrolled_at' => now()]);

        $this->actingAs($student)
            ->get(route('mahasiswa.courses.show', $course->id))
            ->assertOk()
            ->assertSee('Basis Data')
            ->assertSee('BD101')
            ->assertSee('3 SKS')
            ->assertSee('mahasiswa-page__grid--course-actions', false)
            ->assertSee('Materi perkuliahan')
            ->assertSee('Tugas dan pengumpulan')
            ->assertSee('Rubrik penilaian')
            ->assertSee('Rincian nilai')
            ->assertDontSee('Tentang mata kuliah')
            ->assertDontSee('Dosen pengampu');
    }

    public function test_student_rubric_page_shows_database_components_published_assignments_and_own_grade(): void
    {
        $student = User::factory()->mahasiswa()->create();
        $otherStudent = User::factory()->mahasiswa()->create();
        $lecturer = User::factory()->dosen()->create();
        $course = Course::factory()->create(['lecturer_id' => $lecturer->id]);
        $student->courses()->attach($course->id, ['enrolled_at' => now()]);
        $component = GradeComponent::create([
            'course_id' => $course->id,
            'name' => 'Proyek Akhir',
            'weight' => 65.5,
        ]);
        $assignment = Assignment::factory()->create([
            'course_id' => $course->id,
            'grade_component_id' => $component->id,
            'created_by' => $lecturer->id,
            'title' => 'Aplikasi Final',
        ]);
        Assignment::factory()->draft()->create([
            'course_id' => $course->id,
            'grade_component_id' => $component->id,
            'created_by' => $lecturer->id,
            'title' => 'Tugas Draft',
        ]);
        $submission = Submission::factory()->create([
            'assignment_id' => $assignment->id,
            'user_id' => $student->id,
        ]);
        Grade::create([
            'submission_id' => $submission->id,
            'graded_by' => $lecturer->id,
            'score' => 87,
            'graded_at' => now(),
        ]);

        $response = $this->actingAs($student)
            ->get(route('mahasiswa.courses.grade-components.index', $course->id));

        $response
            ->assertOk()
            ->assertSee('Rubrik penilaian')
            ->assertSee('Proyek Akhir')
            ->assertSee('65.50')
            ->assertSee('Aplikasi Final')
            ->assertSee('Nilai 87.00 / 100')
            ->assertDontSee('Tugas Draft')
            ->assertSee(route('mahasiswa.courses.assignments.submissions.index', [$course->id, $assignment->id]));

        $this->actingAs($otherStudent)
            ->get(route('mahasiswa.courses.grade-components.index', $course->id))
            ->assertForbidden();
    }

    public function test_course_grade_detail_shows_assignment_scores_and_category_weights(): void
    {
        $student = User::factory()->mahasiswa()->create();
        $lecturer = User::factory()->dosen()->create();
        $course = Course::factory()->create(['lecturer_id' => $lecturer->id]);
        $assignment = Assignment::factory()->create([
            'course_id' => $course->id,
            'created_by' => $lecturer->id,
            'title' => 'Tugas Analisis',
        ]);

        $course->students()->attach($student->id, ['enrolled_at' => now()]);

        $component = GradeComponent::create([
            'course_id' => $course->id,
            'name' => 'Proyek',
            'weight' => 40,
        ]);
        $quizComponent = GradeComponent::create([
            'course_id' => $course->id,
            'name' => 'Kuis',
            'weight' => 20,
        ]);
        $assignment->update(['grade_component_id' => $component->id]);
        $quiz = Assignment::factory()->create([
            'course_id' => $course->id,
            'grade_component_id' => $quizComponent->id,
            'created_by' => $lecturer->id,
            'title' => 'Kuis Basis Data',
            'max_score' => 50,
        ]);
        foreach (['Kehadiran Basis Data', 'Ujian Tengah Basis Data', 'Ujian Akhir Basis Data'] as $title) {
            Assignment::factory()->create([
                'course_id' => $course->id,
                'created_by' => $lecturer->id,
                'title' => $title,
            ]);
        }
        $submission = Submission::factory()->create([
            'assignment_id' => $assignment->id,
            'user_id' => $student->id,
        ]);
        $quizSubmission = Submission::factory()->create([
            'assignment_id' => $quiz->id,
            'user_id' => $student->id,
        ]);
        Grade::create([
            'submission_id' => $submission->id,
            'graded_by' => $lecturer->id,
            'score' => 88,
            'feedback' => 'Analisis sudah baik.',
            'graded_at' => now(),
        ]);
        Grade::create([
            'submission_id' => $quizSubmission->id,
            'graded_by' => $lecturer->id,
            'score' => 40,
            'graded_at' => now(),
        ]);
        FinalGrade::create([
            'course_id' => $course->id,
            'user_id' => $student->id,
            'total_score' => 91.5,
            'letter_grade' => 'A',
        ]);

        $gradeResponse = $this->actingAs($student)
            ->get(route('mahasiswa.grades.courses.show', $course->id));

        $gradeResponse
            ->assertOk()
            ->assertSee('Tugas Analisis')
            ->assertSee('Kuis Basis Data')
            ->assertSee('Tugas')
            ->assertSee('15%')
            ->assertSee('Kehadiran')
            ->assertSee('5%')
            ->assertSee('Kuis')
            ->assertSee('10%')
            ->assertSee('UTS')
            ->assertSee('25%')
            ->assertSee('UAS')
            ->assertSee('45%')
            ->assertSeeInOrder(['Kehadiran · Belum dikumpulkan', '5%'])
            ->assertSeeInOrder(['Ujian Tengah Basis Data', 'UTS · Belum dikumpulkan', '25%'])
            ->assertSeeInOrder(['Ujian Akhir Basis Data', 'UAS · Belum dikumpulkan', '45%'])
            ->assertSee('88.00')
            ->assertSee('40.00')
            ->assertSee('84.80')
            ->assertSee('AB')
            ->assertDontSee('Terhitung')
            ->assertSee('Total')
            ->assertDontSee('91.50');
        $gradeResponse->assertSee('<td>100%</td>', false);
        $gradeResponse->assertDontSee('51.20')
            ->assertSee('84.80')
            ->assertDontSee('Bobot belum diatur')
            ->assertSee('aria-label="Predikat AB"', false);

        $this->assertMatchesRegularExpression(
            '/href="'.preg_quote(route('mahasiswa.grades.index'), '/').'"\s+class="navbar__link is-active"/',
            $gradeResponse->getContent()
        );
        $gradeResponse->assertSee(
            route('mahasiswa.courses.assignments.submissions.index', [$course->id, $assignment->id])
        );

        $quizSubmission->grade()->update(['score' => 41]);
        $this->get(route('mahasiswa.grades.courses.show', $course->id))
            ->assertOk()
            ->assertSee('85.60')
            ->assertSee('aria-label="Predikat AB"', false);

        $this->get('/mahasiswa/courses/'.$course->id.'/grades')
            ->assertRedirect(route('mahasiswa.grades.courses.show', $course->id));

        $this->get(route('mahasiswa.courses.assignments.submissions.index', [$course->id, $assignment->id]))
            ->assertOk()
            ->assertSee('Kembali ke daftar tugas')
            ->assertSee(route('mahasiswa.courses.assignments.index', $course->id));
    }

    public function test_grade_detail_uses_task_category_for_unlinked_assignments(): void
    {
        $student = User::factory()->mahasiswa()->create();
        $lecturer = User::factory()->dosen()->create();
        $course = Course::factory()->create(['lecturer_id' => $lecturer->id]);

        $course->students()->attach($student->id, ['enrolled_at' => now()]);

        foreach ([91.30, 74.53] as $index => $score) {
            $assignment = Assignment::factory()->create([
                'course_id' => $course->id,
                'created_by' => $lecturer->id,
                'title' => 'Tugas Basis Data '.($index + 1),
            ]);
            $submission = Submission::factory()->create([
                'assignment_id' => $assignment->id,
                'user_id' => $student->id,
            ]);
            Grade::create([
                'submission_id' => $submission->id,
                'graded_by' => $lecturer->id,
                'score' => $score,
                'graded_at' => now(),
            ]);
        }

        $this->actingAs($student)
            ->get(route('mahasiswa.grades.courses.show', $course->id))
            ->assertOk()
            ->assertSee('91.30')
            ->assertSee('74.53')
            ->assertSee('82.92')
            ->assertSee('aria-label="Predikat AB"', false)
            ->assertSee('100%')
            ->assertDontSee('rata-rata tugas yang sudah dinilai')
            ->assertDontSee('Bobot belum diatur');
    }

    public function test_assignment_list_shows_assigned_or_submitted_status(): void
    {
        $student = User::factory()->mahasiswa()->create();
        $lecturer = User::factory()->dosen()->create();
        $course = Course::factory()->create(['lecturer_id' => $lecturer->id]);
        $submittedAssignment = Assignment::factory()->create([
            'course_id' => $course->id,
            'created_by' => $lecturer->id,
            'title' => 'Tugas Submitted',
        ]);
        
        $course->students()->attach($student->id, ['enrolled_at' => now()]);

        $assignedAssignment = Assignment::factory()->create([
            'course_id' => $course->id,
            'created_by' => $lecturer->id,
            'title' => 'Tugas Assigned',
        ]);
        Submission::factory()->create([
            'assignment_id' => $submittedAssignment->id,
            'user_id' => $student->id,
        ]);

        $this->actingAs($student)
            ->get(route('mahasiswa.courses.assignments.index', $course->id))
            ->assertOk()
            ->assertSee('Tugas Submitted')
            ->assertSee('Tugas Assigned')
            ->assertSee('Submitted')
            ->assertSee('Assigned')
            ->assertDontSee('Lihat nilai');

        $this->get(route('mahasiswa.courses.assignments.submissions.create', [$course->id, $assignedAssignment->id]))
            ->assertOk()
            ->assertSee('Tugas yang dikumpulkan')
            ->assertSee('Detail jawaban')
            ->assertSee('Tenggat')
            ->assertSee('Pastikan tautan dapat dibuka oleh dosen.')
            ->assertSee('Kumpulkan sekarang');
    }
}
