<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SubmissionDownloadTest extends TestCase
{
    use RefreshDatabase;

    private User $lecturer;
    private User $student;
    private User $otherStudent;
    private Course $course;
    private Assignment $assignment;
    private Submission $submission;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        $this->lecturer = User::factory()->dosen()->create();
        $this->student = User::factory()->mahasiswa()->create();
        $this->otherStudent = User::factory()->mahasiswa()->create();

        $this->course = Course::factory()->create([
            'lecturer_id' => $this->lecturer->id,
        ]);

        $this->course->students()->attach([
            $this->student->id => ['enrolled_at' => now()],
            $this->otherStudent->id => ['enrolled_at' => now()],
        ]);

        $this->assignment = Assignment::factory()->create([
            'course_id' => $this->course->id,
            'created_by' => $this->lecturer->id,
        ]);

        $this->submission = Submission::factory()->create([
            'assignment_id' => $this->assignment->id,
            'user_id' => $this->student->id,
            'file_path' => 'submissions/tugas-mahasiswa.pdf',
            'original_name' => 'tugas-mahasiswa.pdf',
        ]);

        Storage::disk('local')->put(
            $this->submission->file_path,
            'Isi file tugas mahasiswa.'
        );
    }

    public function test_student_can_download_own_submission(): void
    {
        $this->actingAs($this->student)
            ->get(route('submissions.download', $this->submission))
            ->assertOk()
            ->assertDownload('tugas-mahasiswa.pdf');
    }

    public function test_student_cannot_download_another_students_submission(): void
    {
        $this->actingAs($this->otherStudent)
            ->get(route('submissions.download', $this->submission))
            ->assertForbidden();

        // File tetap tersedia dan tidak boleh terhapus akibat request ditolak.
        Storage::disk('local')->assertExists($this->submission->file_path);
    }

    public function test_course_lecturer_can_download_student_submission(): void
    {
        $this->actingAs($this->lecturer)
            ->get(route('submissions.download', $this->submission))
            ->assertOk()
            ->assertDownload('tugas-mahasiswa.pdf');
    }

    public function test_other_lecturer_cannot_download_submission(): void
    {
        $otherLecturer = User::factory()->dosen()->create();

        $this->actingAs($otherLecturer)
            ->get(route('submissions.download', $this->submission))
            ->assertForbidden();

        Storage::disk('local')->assertExists($this->submission->file_path);
    }
}
