<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Assignment;
use App\Models\Material;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCourseEnrollmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_course_index_is_ordered_by_course_code(): void
    {
        $admin = User::factory()->admin()->create();
        $lecturer = User::factory()->dosen()->create();

        Course::factory()->for($lecturer, 'lecturer')->create(['code' => 'SI30000']);
        Course::factory()->for($lecturer, 'lecturer')->create(['code' => 'SI10000']);
        Course::factory()->for($lecturer, 'lecturer')->create(['code' => 'SI20000']);

        $this->actingAs($admin)
            ->get(route('admin.courses.index'))
            ->assertOk()
            ->assertSeeInOrder(['SI10000', 'SI20000', 'SI30000']);
    }

    public function test_admin_can_view_course_roster_and_enroll_available_students(): void
    {
        $admin = User::factory()->admin()->create();
        $lecturer = User::factory()->dosen()->create();
        $course = Course::factory()->for($lecturer, 'lecturer')->create([
            'name' => 'Pemrograman Web',
            'code' => 'IF-2023',
        ]);
        $enrolled = User::factory()->mahasiswa()->create(['name' => 'Mahasiswa Terdaftar']);
        $available = User::factory()->mahasiswa()->create(['name' => 'Mahasiswa Tersedia']);
        $course->students()->attach($enrolled->id, ['enrolled_at' => now()]);

        $this->actingAs($admin)
            ->get(route('admin.courses.show', $course))
            ->assertOk()
            ->assertSee('Pemrograman Web')
            ->assertSee('IF-2023')
            ->assertSee('Mahasiswa Terdaftar')
            ->assertSee('Mahasiswa Tersedia')
            ->assertSee('logout-dialog--confirmation')
            ->assertSee('data-remove-url="' . route('admin.courses.enrollments.destroy', [$course, $enrolled]) . '"', false)
            ->assertSee('id="remove-student-form"', false)
            ->assertSee(route('admin.materials.create', ['course_id' => $course->id]))
            ->assertSee(route('admin.assignments.create', ['course_id' => $course->id]))
            ->assertSee('Belum ada materi untuk mata kuliah ini.')
            ->assertSee('Belum ada tugas untuk mata kuliah ini.');

        $this->get(route('admin.materials.create', ['course_id' => $course->id]))
            ->assertOk()
            ->assertSee('<option value="' . $course->id . '" selected', false);

        $this->get(route('admin.assignments.create', ['course_id' => $course->id]))
            ->assertOk()
            ->assertSee('<option value="' . $course->id . '" selected', false);

        $this->post(route('admin.courses.enrollments.store', $course), [
            'student_id' => $available->id,
        ])
            ->assertRedirect(route('admin.courses.show', $course))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('course_user', [
            'course_id' => $course->id,
            'user_id' => $available->id,
        ]);
    }

    public function test_admin_course_detail_tables_are_sorted_alphabetically(): void
    {
        $admin = User::factory()->admin()->create();
        $lecturer = User::factory()->dosen()->create();
        $course = Course::factory()->for($lecturer, 'lecturer')->create();
        $studentZ = User::factory()->mahasiswa()->create([
            'name' => 'Zara Student',
            'email' => 'zara@example.test',
        ]);
        $studentA = User::factory()->mahasiswa()->create([
            'name' => 'Alya Student',
            'email' => 'alya@example.test',
        ]);
        $course->students()->attach([
            $studentZ->id => ['enrolled_at' => now()],
            $studentA->id => ['enrolled_at' => now()],
        ]);

        foreach (['Zebra Notes', 'Algebra Notes'] as $title) {
            $material = new Material([
                'course_id' => $course->id,
                'title' => $title,
                'description' => 'Materi pengujian.',
                'type' => 'link',
                'external_url' => 'https://example.test/' . str($title)->slug(),
            ]);
            $material->uploaded_by = $admin->id;
            $material->save();
        }

        foreach (['Zulu Assignment', 'Alpha Assignment'] as $title) {
            Assignment::query()->create([
                'course_id' => $course->id,
                'created_by' => $admin->id,
                'title' => $title,
                'instructions' => 'Instruksi pengujian.',
                'due_at' => now()->addDay(),
                'max_score' => 100,
                'allow_late' => true,
                'status' => 'draft',
            ]);
        }

        $this->actingAs($admin)
            ->get(route('admin.courses.show', $course))
            ->assertOk()
            ->assertSeeInOrder([
                'Alya Student',
                'Zara Student',
                'Algebra Notes',
                'Zebra Notes',
                'Alpha Assignment',
                'Zulu Assignment',
            ]);
    }

    public function test_course_detail_shows_a_dash_until_a_final_grade_exists(): void
    {
        $admin = User::factory()->admin()->create();
        $lecturer = User::factory()->dosen()->create();
        $course = Course::factory()->for($lecturer, 'lecturer')->create();
        $student = User::factory()->mahasiswa()->create(['name' => 'Belum Dinilai']);
        $course->students()->attach($student->id, ['enrolled_at' => now()]);

        $this->actingAs($admin)
            ->get(route('admin.courses.show', $course))
            ->assertOk()
            ->assertSee('Belum Dinilai')
            ->assertSee('—')
            ->assertDontSee('>A</td>', false);
    }

    public function test_admin_enrollment_rejects_non_students_and_duplicate_enrollment_is_safe(): void
    {
        $admin = User::factory()->admin()->create();
        $lecturer = User::factory()->dosen()->create();
        $course = Course::factory()->for($lecturer, 'lecturer')->create();
        $student = User::factory()->mahasiswa()->create();
        $course->students()->attach($student->id, ['enrolled_at' => now()]);

        $this->actingAs($admin)
            ->post(route('admin.courses.enrollments.store', $course), [
                'student_id' => $admin->id,
            ])
            ->assertSessionHasErrors('student_id');

        $this->post(route('admin.courses.enrollments.store', $course), [
            'student_id' => $student->id,
        ])->assertRedirect(route('admin.courses.show', $course));

        $this->assertSame(1, $course->students()->whereKey($student->id)->count());
    }

    public function test_admin_can_remove_student_enrollment_without_deleting_the_account(): void
    {
        $admin = User::factory()->admin()->create();
        $lecturer = User::factory()->dosen()->create();
        $course = Course::factory()->for($lecturer, 'lecturer')->create();
        $student = User::factory()->mahasiswa()->create();
        $course->students()->attach($student->id, ['enrolled_at' => now()]);

        $this->actingAs($admin)
            ->delete(route('admin.courses.enrollments.destroy', [$course, $student]))
            ->assertRedirect(route('admin.courses.show', $course))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('course_user', [
            'course_id' => $course->id,
            'user_id' => $student->id,
        ]);
        $this->assertDatabaseHas('users', ['id' => $student->id]);
    }
}
