<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\GradeComponent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiLazyLoadingTest extends TestCase
{
    use RefreshDatabase;

    public function test_assignment_update_and_delete_do_not_lazy_load_course(): void
    {
        Event::fake();
        $lecturer = User::factory()->dosen()->create();
        $course = Course::factory()->for($lecturer, 'lecturer')->create();
        $assignment = Assignment::factory()->for($course)->draft()->create();
        Sanctum::actingAs($lecturer);

        $this->putJson("/api/v1/assignments/{$assignment->id}", [
            'title' => 'Tugas diperbarui',
        ])->assertOk();

        $this->assertDatabaseHas('assignments', [
            'id' => $assignment->id,
            'title' => 'Tugas diperbarui',
        ]);

        $this->deleteJson("/api/v1/assignments/{$assignment->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('assignments', ['id' => $assignment->id]);
    }

    public function test_grade_component_endpoints_do_not_lazy_load_course_or_assignments(): void
    {
        $lecturer = User::factory()->dosen()->create();
        $course = Course::factory()->for($lecturer, 'lecturer')->create();
        $student = User::factory()->mahasiswa()->create();
        $course->students()->attach($student, ['enrolled_at' => now()]);
        $gradeComponent = GradeComponent::create([
            'course_id' => $course->id,
            'name' => 'Tugas',
            'weight' => 100,
        ]);

        Sanctum::actingAs($student);

        $this->getJson("/api/v1/courses/{$course->id}/grade-components")
            ->assertOk()
            ->assertJsonPath('data.0.assignments_count', 0);

        $this->getJson("/api/v1/grade-components/{$gradeComponent->id}")
            ->assertOk()
            ->assertJsonPath('data.assignments_count', 0);
    }
}
