<?php

namespace Tests\Feature;

use App\Events\AssignmentPublished;
use App\Listeners\SendAssignmentNotification;
use App\Models\Assignment;
use App\Models\Course;
use App\Models\User;
use App\Notifications\TugasBaru;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Illuminate\Support\Facades\Event;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Facades\Log;

class AssignmentPublishedTest extends TestCase
{
    use RefreshDatabase;

    private function courseWithStudents(int $count): array
    {
        $lecturer = User::factory()->dosen()->create();
        $course = Course::factory()->for($lecturer, 'lecturer')->create();
        $students = User::factory()->mahasiswa()->count($count)->create();

        foreach ($students as $student) {
            $course->students()->attach($student, ['enrolled_at' => now()]);
        }

        $assignment = Assignment::factory()->for($course)->create();

        return [$assignment, $students];
    }

    public function test_listener_uses_one_query_regardless_of_student_count(): void
    {
        Notification::fake();

        foreach ([3, 15] as $count) {
            [$assignment] = $this->courseWithStudents($count);

            DB::flushQueryLog();
            DB::enableQueryLog();

            (new SendAssignmentNotification())->handle(new AssignmentPublished($assignment));

            $this->assertCount(
                1,
                DB::getQueryLog(),
                "Listener harus 1 query untuk {$count} mahasiswa."
            );
        }
    }

    public function test_only_enrolled_students_are_notified(): void
    {
        Notification::fake();

        [$assignment, $students] = $this->courseWithStudents(3);
        $outsider = User::factory()->mahasiswa()->create();

        (new SendAssignmentNotification())->handle(new AssignmentPublished($assignment));

        Notification::assertSentTo($students, TugasBaru::class);
        Notification::assertNotSentTo($outsider, TugasBaru::class);
        Notification::assertSentTimes(TugasBaru::class, 3);
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Tugas Uji',
            'instructions' => 'Kerjakan soal.',
            'due_at' => now()->addWeek()->toDateTimeString(),
            'max_score' => 100,
            'allow_late' => 0,
            'status' => 'published',
        ], $overrides);
    }

    public function test_dosen_creating_published_assignment_dispatches_event_once(): void
    {
        Event::fake([AssignmentPublished::class]);
        $lecturer = User::factory()->dosen()->create();
        $course = Course::factory()->for($lecturer, 'lecturer')->create();

        $this->actingAs($lecturer)
            ->post(route('dosen.courses.assignments.store', $course), $this->payload())
            ->assertRedirect();

        Event::assertDispatchedTimes(AssignmentPublished::class, 1);
    }

    public function test_dosen_creating_draft_does_not_dispatch_event(): void
    {
        Event::fake([AssignmentPublished::class]);
        $lecturer = User::factory()->dosen()->create();
        $course = Course::factory()->for($lecturer, 'lecturer')->create();

        $this->actingAs($lecturer)
            ->post(route('dosen.courses.assignments.store', $course), $this->payload(['status' => 'draft']))
            ->assertRedirect();

        Event::assertNotDispatched(AssignmentPublished::class);
    }

    public function test_publishing_a_draft_dispatches_event_but_editing_published_does_not(): void
    {
        Event::fake([AssignmentPublished::class]);
        $lecturer = User::factory()->dosen()->create();
        $course = Course::factory()->for($lecturer, 'lecturer')->create();
        $assignment = Assignment::factory()->for($course)->draft()->create();

        // draft -> published: dikirim
        $this->actingAs($lecturer)
            ->put(route('dosen.courses.assignments.update', [$course, $assignment]), $this->payload())
            ->assertRedirect();
        Event::assertDispatchedTimes(AssignmentPublished::class, 1);

        // edit tugas yang sudah published: tidak dikirim lagi
        $this->actingAs($lecturer)
            ->put(route('dosen.courses.assignments.update', [$course, $assignment->fresh()]), $this->payload(['title' => 'Judul Baru']))
            ->assertRedirect();
        Event::assertDispatchedTimes(AssignmentPublished::class, 1);
    }

    public function test_admin_creating_published_assignment_dispatches_event(): void
    {
        Event::fake([AssignmentPublished::class]);
        $admin = User::factory()->admin()->create();
        $course = Course::factory()->for(User::factory()->dosen()->create(), 'lecturer')->create();

        $this->actingAs($admin)
            ->post(route('admin.assignments.store'), $this->payload(['course_id' => $course->id]))
            ->assertRedirect();

        Event::assertDispatchedTimes(AssignmentPublished::class, 1);
    }

    public function test_api_publish_flow_dispatches_event_only_on_transition(): void
    {
        Event::fake([AssignmentPublished::class]);
        $lecturer = User::factory()->dosen()->create();
        $course = Course::factory()->for($lecturer, 'lecturer')->create();
        Sanctum::actingAs($lecturer);

        $id = $this->postJson('/api/v1/assignments', $this->payload(['course_id' => $course->id, 'status' => 'draft']))
            ->assertCreated()
            ->json('data.id');
        Event::assertNotDispatched(AssignmentPublished::class);

        $this->putJson("/api/v1/assignments/{$id}", ['status' => 'published'])->assertOk();
        Event::assertDispatchedTimes(AssignmentPublished::class, 1);

        $this->putJson("/api/v1/assignments/{$id}", ['title' => 'Edit lagi'])->assertOk();
        Event::assertDispatchedTimes(AssignmentPublished::class, 1);
    }

    public function test_failure_settings_and_failed_hook_are_in_place(): void
    {
        Log::spy();
        [$assignment] = $this->courseWithStudents(1);

        $listener = new SendAssignmentNotification();
        $notification = new TugasBaru($assignment);

        $this->assertSame(3, $listener->tries);
        $this->assertSame(10, $listener->backoff);
        $this->assertSame(3, $notification->tries);
        $this->assertSame(10, $notification->backoff);

        $listener->failed(new AssignmentPublished($assignment), new \RuntimeException('boom'));

        Log::shouldHaveReceived('error')->once();
    }
}

