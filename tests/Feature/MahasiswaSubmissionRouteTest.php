<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MahasiswaSubmissionRouteTest extends TestCase
{
    use RefreshDatabase;

    public function test_enrolled_student_can_submit_using_the_mahasiswa_route(): void
    {
        $student = User::factory()->mahasiswa()->create();
        $lecturer = User::factory()->dosen()->create();
        $course = Course::factory()->create(['lecturer_id' => $lecturer->id]);
        $course->students()->attach($student->id, ['enrolled_at' => now()]);
        $assignment = Assignment::factory()->create([
            'course_id' => $course->id,
            'created_by' => $lecturer->id,
        ]);

        $this->actingAs($student)
            ->post(route('mahasiswa.courses.assignments.submissions.store', [$course, $assignment]), [
                'file_path' => 'https://example.test/tugas.pdf',
                'original_name' => 'tugas.pdf',
                'note' => 'Tugas selesai.',
            ])
            ->assertRedirect(route('mahasiswa.courses.assignments.submissions.index', [$course, $assignment]));

        $this->assertDatabaseHas('submissions', [
            'assignment_id' => $assignment->id,
            'user_id' => $student->id,
            'original_name' => 'tugas.pdf',
        ]);
    }

    public function test_student_not_enrolled_in_course_cannot_submit(): void
    {
        $submissionCount = Submission::count();
        $student = User::factory()->mahasiswa()->create();
        $lecturer = User::factory()->dosen()->create();
        $course = Course::factory()->create(['lecturer_id' => $lecturer->id]);
        $assignment = Assignment::factory()->create([
            'course_id' => $course->id,
            'created_by' => $lecturer->id,
        ]);

        $this->actingAs($student)
            ->post(route('mahasiswa.courses.assignments.submissions.store', [$course, $assignment]), [
                'file_path' => 'https://example.test/tugas.pdf',
                'original_name' => 'tugas.pdf',
            ])
            ->assertForbidden();

        $this->assertSame($submissionCount, Submission::count());
    }
}
