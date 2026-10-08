<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Course;
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
        $assignment = Assignment::factory()->for($course)->create([
            'title' => 'Proyek Antarmuka',
            'allow_late' => false,
        ]);

        $this->actingAs($lecturer)
            ->get(route('dosen.courses.assignments.edit', [$course, $assignment]))
            ->assertOk()
            ->assertSee('Proyek Antarmuka')
            ->assertSee('datetime-local', false)
            ->assertSee('Pengumpulan Terlambat')
            ->assertSee('Tidak diizinkan')
            ->assertSee('dosen-assignment-form__card', false);
    }

    public function test_assignment_create_page_uses_the_styled_form_and_keeps_submitted_values(): void
    {
        $lecturer = User::factory()->dosen()->create();
        $course = Course::factory()->for($lecturer, 'lecturer')->create();

        $this->actingAs($lecturer)
            ->withSession([
                '_old_input' => [
                    'title' => 'Tugas Praktikum',
                    'due_at' => '2026-10-20T18:00',
                    'max_score' => 80,
                    'allow_late' => '0',
                    'instructions' => 'Kumpulkan laporan praktikum.',
                ],
            ])
            ->get(route('dosen.courses.assignments.create', $course))
            ->assertOk()
            ->assertSee('Buat Tugas Baru')
            ->assertSee('Tugas Praktikum')
            ->assertSee('datetime-local', false)
            ->assertSee('Pengumpulan Terlambat')
            ->assertSee('Kumpulkan laporan praktikum.')
            ->assertSee('dosen-assignment-form__card', false);
    }
}
