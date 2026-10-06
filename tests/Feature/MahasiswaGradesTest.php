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
            ->assertSee('86.50')
            ->assertSee('A')
            ->assertDontSee('52.25');
        $this->assertMatchesRegularExpression(
            '/href="'.preg_quote(route('mahasiswa.grades.index'), '/').'"\s+class="navbar__link is-active"/',
            $monitoringResponse->getContent()
        );

        $this->get(route('mahasiswa.grades.index', ['status' => 'archived']))
            ->assertOk()
            ->assertSee($archivedCourse->name)
            ->assertSee('52.25')
            ->assertDontSee('86.50');
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

        $this->actingAs($student)
            ->get(route('mahasiswa.courses.show', $course->id))
            ->assertOk()
            ->assertSee('Basis Data')
            ->assertSee('BD101')
            ->assertSee('3 SKS')
            ->assertSee('mahasiswa-page__grid--course-actions', false)
            ->assertSee('Materi perkuliahan')
            ->assertSee('Tugas dan pengumpulan')
            ->assertSee('Rincian nilai')
            ->assertDontSee('Tentang mata kuliah')
            ->assertDontSee('Dosen pengampu');
    }

    public function test_course_grade_detail_shows_assignment_score_and_final_predicate(): void
    {
        $student = User::factory()->mahasiswa()->create();
        $lecturer = User::factory()->dosen()->create();
        $course = Course::factory()->create(['lecturer_id' => $lecturer->id]);
        $assignment = Assignment::factory()->create([
            'course_id' => $course->id,
            'created_by' => $lecturer->id,
            'title' => 'Tugas Analisis',
        ]);
        $component = GradeComponent::create([
            'course_id' => $course->id,
            'name' => 'Proyek',
            'weight' => 40,
        ]);
        $assignment->update(['grade_component_id' => $component->id]);
        $submission = Submission::factory()->create([
            'assignment_id' => $assignment->id,
            'user_id' => $student->id,
        ]);
        Grade::create([
            'submission_id' => $submission->id,
            'graded_by' => $lecturer->id,
            'score' => 88,
            'feedback' => 'Analisis sudah baik.',
            'graded_at' => now(),
        ]);
        FinalGrade::create([
            'course_id' => $course->id,
            'user_id' => $student->id,
            'total_score' => 91.5,
            'letter_grade' => 'A',
        ]);

        $gradeResponse = $this->actingAs($student)
            ->get(route('mahasiswa.courses.grades.show', $course->id));

        $gradeResponse
            ->assertOk()
            ->assertSee('Tugas Analisis')
            ->assertSee('Proyek')
            ->assertSee('40.00%')
            ->assertSee('88.00')
            ->assertSee('91.50')
            ->assertSee('Terhitung')
            ->assertSee('Total')
            ->assertSee('Predikat')
            ->assertSee('A');

        $this->assertMatchesRegularExpression(
            '/href="'.preg_quote(route('mahasiswa.courses.index'), '/').'"\s+class="navbar__link is-active"/',
            $gradeResponse->getContent()
        );
        $gradeResponse->assertSee(
            route('mahasiswa.courses.assignments.submissions.index', [$course->id, $assignment->id])
        );

        $this->get(route('mahasiswa.courses.assignments.submissions.index', [$course->id, $assignment->id]))
            ->assertOk()
            ->assertSee('Kembali ke daftar tugas')
            ->assertSee(route('mahasiswa.courses.assignments.index', $course->id));
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
