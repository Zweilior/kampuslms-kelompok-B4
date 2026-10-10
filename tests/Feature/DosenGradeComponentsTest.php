<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\GradeComponent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DosenGradeComponentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_course_detail_has_edit_rubric_action_and_active_course_header(): void
    {
        $lecturer = User::factory()->dosen()->create();
        $course = Course::factory()->for($lecturer, 'lecturer')->create([
            'code' => 'WEB201',
            'name' => 'Pemrograman Web',
        ]);

        $this->actingAs($lecturer)
            ->get(route('dosen.courses.show', $course))
            ->assertOk()
            ->assertSee('Kelas yang Anda ampu')
            ->assertSee('dosen-course-hero', false)
            ->assertSee('SEMESTER BERJALAN')
            ->assertSee('Edit Rubrik')
            ->assertSee(route('dosen.courses.grade-components.index', $course));
    }

    public function test_lecturer_can_view_and_manage_course_rubric_components(): void
    {
        $lecturer = User::factory()->dosen()->create();
        $course = Course::factory()->for($lecturer, 'lecturer')->create();
        $component = GradeComponent::create([
            'course_id' => $course->id,
            'name' => 'Ujian Tengah Semester',
            'weight' => 30,
        ]);

        $this->actingAs($lecturer)
            ->get(route('dosen.courses.grade-components.index', $course))
            ->assertOk()
            ->assertSee('Rubrik Penilaian')
            ->assertSee('Ujian Tengah Semester')
            ->assertSee('Tambah Komponen');

        $this->post(route('dosen.courses.grade-components.store', $course), [
            'name' => 'Ujian Akhir Semester',
            'weight' => 40,
        ])->assertRedirect(route('dosen.courses.grade-components.index', $course));

        $this->put(route('dosen.courses.grade-components.update', [$course, $component]), [
            'name' => 'UTS',
            'weight' => 35,
        ])->assertRedirect(route('dosen.courses.grade-components.index', $course));

        $this->assertDatabaseHas('grade_components', [
            'id' => $component->id,
            'name' => 'UTS',
            'weight' => 35,
        ]);
        $this->assertDatabaseHas('grade_components', [
            'course_id' => $course->id,
            'name' => 'Ujian Akhir Semester',
            'weight' => 40,
        ]);
    }
}
