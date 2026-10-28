<?php

namespace Tests\Feature;

use App\Events\SubmissionCreated;
use App\Events\SubmissionGraded;
use App\Listeners\SendGradeNotification;
use App\Listeners\SendNewSubmissionNotification;
use App\Models\Assignment;
use App\Models\Course;
use App\Models\Grade;
use App\Models\Submission;
use App\Models\User;
use App\Notifications\NilaiDiberikan;
use App\Notifications\PengumpulanBaru;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;

class GradeNotificationFlowTest extends TestCase
{
    use RefreshDatabase;

    /** Tugas lewat tenggat + satu pengumpulan, siap dinilai. */
    private function gradingScenario(): array
    {
        $lecturer = User::factory()->dosen()->create();
        $student = User::factory()->mahasiswa()->create();
        $course = Course::factory()->for($lecturer, 'lecturer')->create();
        $assignment = Assignment::factory()->for($course)->past()->create();
        $submission = Submission::factory()->for($assignment)->for($student, 'student')->create();

        return [$lecturer, $student, $course, $assignment, $submission];
    }

    /** Tugas aktif, mahasiswa terdaftar, siap mengumpulkan. */
    private function submittingScenario(): array
    {
        Storage::fake('local');

        $lecturer = User::factory()->dosen()->create();
        $student = User::factory()->mahasiswa()->create();
        $course = Course::factory()->create(['lecturer_id' => $lecturer->id]);
        $course->students()->attach($student->id, ['enrolled_at' => now()]);
        $assignment = Assignment::factory()->create([
            'course_id' => $course->id,
            'created_by' => $lecturer->id,
        ]);

        return [$lecturer, $student, $course, $assignment];
    }

    private function submitAs(User $student, Course $course, Assignment $assignment): void
    {
        $this->actingAs($student)
            ->post(route('mahasiswa.courses.assignments.submissions.store', [$course, $assignment]), [
                'file' => UploadedFile::fake()->create('tugas.pdf', 100, 'application/pdf'),
                'note' => 'Selesai.',
            ])
            ->assertRedirect();
    }

    public function test_grading_dispatches_event_and_marks_regrade_as_updated(): void
    {
        Event::fake([SubmissionGraded::class]);
        [$lecturer, , $course, $assignment, $submission] = $this->gradingScenario();
        $url = route('dosen.courses.assignments.submissions.grade', [$course, $assignment, $submission]);

        $this->actingAs($lecturer)->put($url, ['score' => 80])->assertRedirect();
        Event::assertDispatched(
            SubmissionGraded::class,
            fn ($e) => $e->updated === false && $e->grade->submission_id === $submission->id
        );

        $this->put($url, ['score' => 90])->assertRedirect();
        Event::assertDispatchedTimes(SubmissionGraded::class, 2);
        Event::assertDispatched(SubmissionGraded::class, fn ($e) => $e->updated === true);
    }

    public function test_grading_notifies_only_the_student_and_payload_has_no_score_or_feedback(): void
    {
        [$lecturer, $student, $course, $assignment, $submission] = $this->gradingScenario();
        $bystander = User::factory()->mahasiswa()->create();
        $url = route('dosen.courses.assignments.submissions.grade', [$course, $assignment, $submission]);

        $this->actingAs($lecturer)
            ->put($url, ['score' => 87.65, 'feedback' => 'Feedback rahasia unik'])
            ->assertRedirect();

        $this->assertSame(1, $student->notifications()->count());
        $this->assertSame(0, $bystander->notifications()->count());
        $this->assertSame(0, $lecturer->notifications()->count());

        $notification = $student->notifications()->first();
        $this->assertSame('NilaiDiberikan', class_basename($notification->type));
        $this->assertSame($submission->id, $notification->data['submission_id']);
        $this->assertSame($assignment->course_id, $notification->data['course_id']);
        $this->assertArrayNotHasKey('score', $notification->data);
        $this->assertArrayNotHasKey('feedback', $notification->data);

        $json = json_encode($notification->data);
        $this->assertStringNotContainsString('87.65', $json);
        $this->assertStringNotContainsString('Feedback rahasia unik', $json);
        $this->assertStringContainsString('diberikan', $notification->data['message']);

        // Nilai diubah: notifikasi kedua memakai kata "diperbarui".
        $this->put($url, ['score' => 95])->assertRedirect();
        $messages = $student->notifications()->get()->map(fn ($n) => $n->data['message']);
        $this->assertCount(2, $messages);
        $this->assertTrue($messages->contains(fn ($m) => str_contains($m, 'diperbarui')));
    }

    public function test_submitting_dispatches_event_and_resubmit_is_marked_updated(): void
    {
        Event::fake([SubmissionCreated::class]);
        [, $student, $course, $assignment] = $this->submittingScenario();

        $this->submitAs($student, $course, $assignment);
        Event::assertDispatched(SubmissionCreated::class, fn ($e) => $e->updated === false);

        $this->submitAs($student, $course, $assignment);
        Event::assertDispatchedTimes(SubmissionCreated::class, 2);
        Event::assertDispatched(SubmissionCreated::class, fn ($e) => $e->updated === true);
    }

    public function test_submitting_notifies_only_the_course_lecturer(): void
    {
        [$lecturer, $student, $course, $assignment] = $this->submittingScenario();
        $otherLecturer = User::factory()->dosen()->create();

        $this->submitAs($student, $course, $assignment);

        $this->assertSame(1, $lecturer->notifications()->count());
        $this->assertSame(0, $otherLecturer->notifications()->count());
        $this->assertSame(0, $student->notifications()->count());

        $notification = $lecturer->notifications()->first();
        $this->assertSame('PengumpulanBaru', class_basename($notification->type));
        $this->assertSame($assignment->id, $notification->data['assignment_id']);
        $this->assertSame($course->id, $notification->data['course_id']);
        $this->assertArrayNotHasKey('score', $notification->data);
        $this->assertArrayNotHasKey('feedback', $notification->data);
    }

    public function test_failure_settings_and_failed_hooks_exist_for_grade_flow(): void
    {
        Log::spy();
        $grade = Grade::factory()->create();
        $submission = $grade->submission;

        $units = [
            new NilaiDiberikan($submission, $grade),
            new PengumpulanBaru($submission),
            new SendGradeNotification(),
            new SendNewSubmissionNotification(),
        ];

        foreach ($units as $unit) {
            $this->assertSame(3, $unit->tries, get_class($unit).' harus tries = 3');
            $this->assertSame([10, 30, 60], $unit->backoff, get_class($unit).' harus punya backoff');
        }

        $boom = new \RuntimeException('boom');
        $units[0]->failed($boom);
        $units[1]->failed($boom);
        $units[2]->failed(new SubmissionGraded($grade), $boom);
        $units[3]->failed(new SubmissionCreated($submission), $boom);

        Log::shouldHaveReceived('error')->times(4);
    }

    public function test_unread_count_does_not_query_assignments_for_new_payloads(): void
    {
        $student = User::factory()->mahasiswa()->create();

        foreach (range(1, 5) as $i) {
            $submission = Submission::factory()->for($student, 'student')->create();
            $grade = Grade::factory()->for($submission)->create();
            $student->notify(new NilaiDiberikan($submission, $grade));
        }

        DB::flushQueryLog();
        DB::enableQueryLog();

        $this->actingAs($student)
            ->getJson(route('notifications.unread-count'))
            ->assertOk()
            ->assertJsonCount(5, 'latest');

        $assignmentQueries = collect(DB::getQueryLog())
            ->filter(fn ($q) => str_contains($q['query'], 'assignments'));

        $this->assertCount(0, $assignmentQueries, 'Polling tidak boleh mencari tugas satu per satu.');
    }
}