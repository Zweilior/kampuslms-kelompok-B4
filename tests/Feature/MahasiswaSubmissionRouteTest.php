<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MahasiswaSubmissionRouteTest extends TestCase
{
    use RefreshDatabase;

    public function test_enrolled_student_can_submit_using_the_mahasiswa_route(): void
    {
        Storage::fake('local');

        $student = User::factory()->mahasiswa()->create();
        $lecturer = User::factory()->dosen()->create();
        $course = Course::factory()->create(['lecturer_id' => $lecturer->id]);
        $course->students()->attach($student->id, ['enrolled_at' => now()]);
        $assignment = Assignment::factory()->create([
            'course_id' => $course->id,
            'created_by' => $lecturer->id,
        ]);

        $file = UploadedFile::fake()->create('tugas.pdf', 100, 'application/pdf');

        $this->actingAs($student)
            ->post(route('mahasiswa.courses.assignments.submissions.store', [$course, $assignment]), [
                'file' => $file,
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

        $file = UploadedFile::fake()->create('tugas.pdf', 100, 'application/pdf');

        $this->actingAs($student)
            ->post(route('mahasiswa.courses.assignments.submissions.store', [$course, $assignment]), [
                'file' => $file,
            ])
            ->assertForbidden();

        $this->assertSame($submissionCount, Submission::count());
    }


    public function test_student_cannot_submit_after_deadline_when_late_submission_is_disabled(): void
    {
        Storage::fake('local');

        $student = User::factory()->mahasiswa()->create();
        $lecturer = User::factory()->dosen()->create();
        $course = Course::factory()->create(['lecturer_id' => $lecturer->id]);
        $course->students()->attach($student->id, ['enrolled_at' => now()]);

        $assignment = Assignment::factory()->create([
            'course_id' => $course->id,
            'created_by' => $lecturer->id,
            'due_at' => now()->subDay(),
            'allow_late' => false,
            'status' => 'published',
        ]);

        $file = UploadedFile::fake()->create('tugas.pdf', 100, 'application/pdf');

        $this->actingAs($student)
            ->post(route('mahasiswa.courses.assignments.submissions.store', [$course, $assignment]), [
                'file' => $file,
                'note' => 'Tugas terlambat.',
            ])
            ->assertSessionHasErrors('file');

        $this->assertDatabaseMissing('submissions', [
            'assignment_id' => $assignment->id,
            'user_id' => $student->id,
        ]);

        $this->assertSame([], Storage::disk('local')->allFiles());
    }

    public function test_student_can_submit_after_deadline_when_late_submission_is_enabled(): void
    {
        Storage::fake('local');

        $student = User::factory()->mahasiswa()->create();
        $lecturer = User::factory()->dosen()->create();
        $course = Course::factory()->create(['lecturer_id' => $lecturer->id]);
        $course->students()->attach($student->id, ['enrolled_at' => now()]);

        $assignment = Assignment::factory()->create([
            'course_id' => $course->id,
            'created_by' => $lecturer->id,
            'due_at' => now()->subDay(),
            'allow_late' => true,
            'status' => 'published',
        ]);

        $file = UploadedFile::fake()->create('tugas.pdf', 100, 'application/pdf');

        $this->actingAs($student)
            ->post(route('mahasiswa.courses.assignments.submissions.store', [$course, $assignment]), [
                'file' => $file,
                'note' => 'Tugas terlambat dengan izin.',
            ])
            ->assertRedirect(route('mahasiswa.courses.assignments.submissions.index', [$course, $assignment]));

        $submission = Submission::where('assignment_id', $assignment->id)
            ->where('user_id', $student->id)
            ->firstOrFail();

        $this->assertTrue((bool) $submission->is_late);

        Storage::disk('local')->assertExists($submission->file_path);
    }


    public function test_student_cannot_submit_using_assignment_from_another_course(): void
    {
        Storage::fake('local');

        $student = User::factory()->mahasiswa()->create();
        $lecturer = User::factory()->dosen()->create();

        $courseA = Course::factory()->create([
            'lecturer_id' => $lecturer->id,
        ]);

        $courseB = Course::factory()->create([
            'lecturer_id' => $lecturer->id,
        ]);

        $courseA->students()->attach($student->id, [
            'enrolled_at' => now(),
        ]);

        $assignmentB = Assignment::factory()->create([
            'course_id' => $courseB->id,
            'created_by' => $lecturer->id,
        ]);

        $file = UploadedFile::fake()->create(
            'tugas.pdf',
            100,
            'application/pdf'
        );

        $this->actingAs($student)
            ->post(
                route('mahasiswa.courses.assignments.submissions.store', [
                    $courseA,
                    $assignmentB,
                ]),
                ['file' => $file]
            )
            ->assertNotFound();

        $this->assertDatabaseMissing('submissions', [
            'assignment_id' => $assignmentB->id,
            'user_id' => $student->id,
        ]);

        $this->assertSame([], Storage::disk('local')->allFiles());
    }

}
