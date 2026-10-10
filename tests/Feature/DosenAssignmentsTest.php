<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\GradeComponent;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DosenAssignmentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_assignment_list_shows_submission_totals_and_only_offers_grading_after_the_deadline(): void
    {
        $lecturer = User::factory()->dosen()->create();
        $course = Course::factory()->for($lecturer, 'lecturer')->create();
        $students = User::factory()->mahasiswa()->count(2)->create();
        foreach ($students as $student) {
            $course->students()->attach($student, ['enrolled_at' => now()]);
        }

        $openAssignment = Assignment::factory()->for($course)->active()->create();
        $closedAssignment = Assignment::factory()->for($course)->past()->create();
        Submission::factory()->for($closedAssignment)->for($students->first(), 'student')->create();

        $this->actingAs($lecturer)
            ->get(route('dosen.courses.assignments.index', $course))
            ->assertOk()
            ->assertSee($openAssignment->title)
            ->assertSee($closedAssignment->title)
            ->assertSee('1 / 2 mahasiswa')
            ->assertSee('Hapus tugas ini?')
            ->assertSee('Ya, hapus tugas')
            ->assertDontSee("confirm('Hapus tugas ini?')")
            ->assertDontSee(route('dosen.courses.assignments.submissions.index', [$course, $openAssignment]))
            ->assertSee(route('dosen.courses.assignments.submissions.index', [$course, $closedAssignment]));

        $this->get(route('dosen.courses.assignments.submissions.index', [$course, $closedAssignment]))
            ->assertOk()
            ->assertSee('Nilai')
            ->assertSee('Umpan Balik')
            ->assertSee('dosen-grade-card', false);
    }

    public function test_lecturer_can_grade_a_submission_after_deadline_and_cannot_grade_before_it(): void
    {
        $lecturer = User::factory()->dosen()->create();
        $student = User::factory()->mahasiswa()->create();
        $course = Course::factory()->for($lecturer, 'lecturer')->create();
        $closedAssignment = Assignment::factory()->for($course)->past()->create();
        $openAssignment = Assignment::factory()->for($course)->active()->create();
        $closedSubmission = Submission::factory()->for($closedAssignment)->for($student, 'student')->create();
        $openSubmission = Submission::factory()->for($openAssignment)->for($student, 'student')->create();

        $this->actingAs($lecturer)
            ->put(route('dosen.courses.assignments.submissions.grade', [$course, $closedAssignment, $closedSubmission]), [
                'score' => 88.5,
                'feedback' => 'Bagus.',
            ])
            ->assertRedirect(route('dosen.courses.assignments.submissions.index', [$course, $closedAssignment]));

        $this->assertDatabaseHas('grades', [
            'submission_id' => $closedSubmission->id,
            'graded_by' => $lecturer->id,
            'score' => 88.5,
            'feedback' => 'Bagus.',
        ]);

        $this->put(route('dosen.courses.assignments.submissions.grade', [$course, $openAssignment, $openSubmission]), [
            'score' => 75,
        ])->assertForbidden();

        $this->assertDatabaseMissing('grades', ['submission_id' => $openSubmission->id]);
    }

    public function test_lecturer_cannot_access_assignment_list_for_another_lecturers_course(): void
    {
        $lecturer = User::factory()->dosen()->create();
        $otherLecturer = User::factory()->dosen()->create();
        $course = Course::factory()->for($otherLecturer, 'lecturer')->create();

        $this->actingAs($lecturer)
            ->get(route('dosen.courses.assignments.index', $course))
            ->assertForbidden();
    }

    public function test_assignment_edit_page_renders_styled_form_with_existing_values(): void
    {
        $lecturer = User::factory()->dosen()->create();
        $course = Course::factory()->for($lecturer, 'lecturer')->create();
        $component = GradeComponent::create([
            'course_id' => $course->id,
            'name' => 'Ujian Tengah Semester',
            'weight' => 30,
        ]);
        $assignment = Assignment::factory()->for($course)->create([
            'title' => 'Proyek Antarmuka',
            'allow_late' => false,
            'grade_component_id' => $component->id,
        ]);

        $this->actingAs($lecturer)
            ->get(route('dosen.courses.assignments.edit', [$course, $assignment]))
            ->assertOk()
            ->assertSee('Proyek Antarmuka')
            ->assertSee('datetime-local', false)
            ->assertSee('Pengumpulan Terlambat')
            ->assertSee('Tidak diizinkan')
            ->assertSee('Ujian Tengah Semester (30.00%)')
            ->assertSee('value="' . $component->id . '" selected', false)
            ->assertSee('dosen-assignment-form__card', false);
    }

    public function test_assignment_create_page_uses_the_styled_form_and_keeps_submitted_values(): void
    {
        $lecturer = User::factory()->dosen()->create();
        $course = Course::factory()->for($lecturer, 'lecturer')->create();
        $component = GradeComponent::create([
            'course_id' => $course->id,
            'name' => 'Proyek',
            'weight' => 70,
        ]);

        $this->actingAs($lecturer)
            ->withSession([
                '_old_input' => [
                    'title' => 'Tugas Praktikum',
                    'due_at' => '2026-10-20T18:00',
                    'max_score' => 80,
                    'allow_late' => '0',
                    'instructions' => 'Kumpulkan laporan praktikum.',
                    'grade_component_id' => (string) $component->id,
                ],
            ])
            ->get(route('dosen.courses.assignments.create', $course))
            ->assertOk()
            ->assertSee('Buat Tugas Baru')
            ->assertSee('Tugas Praktikum')
            ->assertSee('datetime-local', false)
            ->assertSee('Pengumpulan Terlambat')
            ->assertSee('Kumpulkan laporan praktikum.')
            ->assertSee('Proyek (70.00%)')
            ->assertSee('value="' . $component->id . '" selected', false)
            ->assertSee('dosen-assignment-form__card', false);
    }

    public function test_assignment_create_and_edit_save_course_rubric_selection(): void
    {
        $lecturer = User::factory()->dosen()->create();
        $course = Course::factory()->for($lecturer, 'lecturer')->create();
        $otherCourse = Course::factory()->for($lecturer, 'lecturer')->create();
        $component = GradeComponent::create([
            'course_id' => $course->id,
            'name' => 'Tugas Praktik',
            'weight' => 40,
        ]);
        $otherComponent = GradeComponent::create([
            'course_id' => $otherCourse->id,
            'name' => 'Rubrik kelas lain',
            'weight' => 50,
        ]);

        $this->actingAs($lecturer)
            ->post(route('dosen.courses.assignments.store', $course), [
                'title' => 'Implementasi Form',
                'grade_component_id' => $component->id,
                'due_at' => now()->addDays(7)->format('Y-m-d H:i:s'),
                'instructions' => 'Selesaikan implementasi.',
                'max_score' => 100,
                'allow_late' => true,
                'status' => 'published',
            ])
            ->assertRedirect(route('dosen.courses.assignments.index', $course));

        $assignment = Assignment::where('course_id', $course->id)
            ->where('title', 'Implementasi Form')
            ->firstOrFail();
        $this->assertSame($component->id, $assignment->grade_component_id);

        $this->put(route('dosen.courses.assignments.update', [$course, $assignment]), [
            'title' => 'Implementasi Form Revisi',
            'grade_component_id' => null,
            'due_at' => now()->addDays(8)->format('Y-m-d H:i:s'),
            'instructions' => 'Selesaikan implementasi.',
            'max_score' => 100,
            'allow_late' => true,
            'status' => 'published',
        ])->assertRedirect(route('dosen.courses.assignments.index', $course));

        $this->assertDatabaseHas('assignments', [
            'id' => $assignment->id,
            'title' => 'Implementasi Form Revisi',
            'grade_component_id' => null,
        ]);

        $this->from(route('dosen.courses.assignments.create', $course))
            ->post(route('dosen.courses.assignments.store', $course), [
                'title' => 'Invalid rubric',
                'grade_component_id' => $otherComponent->id,
                'due_at' => now()->addDays(7)->format('Y-m-d H:i:s'),
                'instructions' => 'Test scope.',
                'max_score' => 100,
                'allow_late' => true,
                'status' => 'published',
            ])
            ->assertSessionHasErrors('grade_component_id');

        $this->assertDatabaseMissing('assignments', ['title' => 'Invalid rubric']);
    }
}
