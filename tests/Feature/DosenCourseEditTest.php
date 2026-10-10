<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DosenCourseEditTest extends TestCase
{
    use RefreshDatabase;

    public function test_course_detail_links_to_edit_form_for_its_lecturer(): void
    {
        $lecturer = User::factory()->dosen()->create();
        $course = Course::factory()->for($lecturer, 'lecturer')->create([
            'name' => 'Pemrograman Web',
        ]);

        $this->actingAs($lecturer)
            ->get(route('dosen.courses.show', $course))
            ->assertOk()
            ->assertSee('Edit Mata Kuliah')
            ->assertSee(route('dosen.courses.edit', $course))
            ->assertSee('Enrolment')
            ->assertSee(route('dosen.courses.enrollments.index', $course));

        $this->get(route('dosen.courses.edit', $course))
            ->assertOk()
            ->assertSee('Pemrograman Web')
            ->assertSee('Dosen pengampu dan status hanya dapat diubah oleh admin.');
    }

    public function test_lecturer_can_update_own_course_details_but_not_lecturer_or_status(): void
    {
        $lecturer = User::factory()->dosen()->create();
        $otherLecturer = User::factory()->dosen()->create();
        $course = Course::factory()->for($lecturer, 'lecturer')->create([
            'status' => 'active',
        ]);

        $this->actingAs($lecturer)
            ->put(route('dosen.courses.update', $course), [
                'code' => 'WEB205',
                'name' => 'Pemrograman Web Lanjut',
                'description' => 'Materi pengembangan web.',
                'sks' => 4,
                'lecturer_id' => $otherLecturer->id,
                'status' => 'archived',
            ])
            ->assertRedirect(route('dosen.courses.show', $course))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('courses', [
            'id' => $course->id,
            'code' => 'WEB205',
            'name' => 'Pemrograman Web Lanjut',
            'description' => 'Materi pengembangan web.',
            'sks' => 4,
            'lecturer_id' => $lecturer->id,
            'status' => 'active',
        ]);
    }

    public function test_lecturer_cannot_edit_course_owned_by_another_lecturer(): void
    {
        $lecturer = User::factory()->dosen()->create();
        $owner = User::factory()->dosen()->create();
        $course = Course::factory()->for($owner, 'lecturer')->create();

        $this->actingAs($lecturer)
            ->get(route('dosen.courses.edit', $course))
            ->assertForbidden();

        $this->put(route('dosen.courses.update', $course), [
            'code' => 'WEB205',
            'name' => 'Tidak boleh',
            'sks' => 2,
        ])->assertForbidden();
    }
}
