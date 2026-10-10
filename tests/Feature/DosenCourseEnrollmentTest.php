<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DosenCourseEnrollmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_view_roster_and_available_students(): void
    {
        $lecturer = User::factory()->dosen()->create();
        $course = Course::factory()->for($lecturer, 'lecturer')->create();
        $enrolled = User::factory()->mahasiswa()->create(['name' => 'Mahasiswa Terdaftar']);
        $available = User::factory()->mahasiswa()->create(['name' => 'Mahasiswa Tersedia']);
        $course->students()->attach($enrolled->id, ['enrolled_at' => now()]);

        $this->actingAs($lecturer)
            ->get(route('dosen.courses.enrollments.index', $course))
            ->assertOk()
            ->assertSee('Mahasiswa Terdaftar')
            ->assertSee('Mahasiswa Tersedia')
            ->assertSee('Daftarkan Mahasiswa')
            ->assertSee('1 mahasiswa');
    }

    public function test_owner_can_enroll_a_student_and_duplicate_enrollment_is_safe(): void
    {
        $lecturer = User::factory()->dosen()->create();
        $course = Course::factory()->for($lecturer, 'lecturer')->create();
        $student = User::factory()->mahasiswa()->create();

        $this->actingAs($lecturer)
            ->post(route('dosen.courses.enrollments.store', $course), [
                'student_id' => $student->id,
            ])
            ->assertRedirect(route('dosen.courses.enrollments.index', $course))
            ->assertSessionHas('success');

        $enrolledAt = $course->students()->whereKey($student->id)->firstOrFail()->pivot->enrolled_at;
        $this->assertNotNull($enrolledAt);

        $this->post(route('dosen.courses.enrollments.store', $course), [
            'student_id' => $student->id,
        ])->assertRedirect(route('dosen.courses.enrollments.index', $course));

        $this->assertSame(1, $course->students()->whereKey($student->id)->count());
    }

    public function test_enrollment_rejects_non_students_and_students_already_enrolled_elsewhere_are_allowed(): void
    {
        $lecturer = User::factory()->dosen()->create();
        $course = Course::factory()->for($lecturer, 'lecturer')->create();
        $otherCourse = Course::factory()->for($lecturer, 'lecturer')->create();
        $student = User::factory()->mahasiswa()->create();
        $otherCourse->students()->attach($student->id, ['enrolled_at' => now()]);
        $admin = User::factory()->admin()->create();

        $this->actingAs($lecturer)
            ->post(route('dosen.courses.enrollments.store', $course), [
                'student_id' => $admin->id,
            ])
            ->assertSessionHasErrors('student_id');

        $this->post(route('dosen.courses.enrollments.store', $course), [
            'student_id' => $student->id,
        ])->assertRedirect(route('dosen.courses.enrollments.index', $course));

        $this->assertDatabaseHas('course_user', [
            'course_id' => $course->id,
            'user_id' => $student->id,
        ]);
    }

    public function test_owner_can_remove_student_without_deleting_the_student_account(): void
    {
        $lecturer = User::factory()->dosen()->create();
        $course = Course::factory()->for($lecturer, 'lecturer')->create();
        $student = User::factory()->mahasiswa()->create();
        $course->students()->attach($student->id, ['enrolled_at' => now()]);

        $this->actingAs($lecturer)
            ->delete(route('dosen.courses.enrollments.destroy', [$course, $student]))
            ->assertRedirect(route('dosen.courses.enrollments.index', $course))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('course_user', [
            'course_id' => $course->id,
            'user_id' => $student->id,
        ]);
        $this->assertDatabaseHas('users', ['id' => $student->id]);
    }

    public function test_other_lecturer_cannot_view_or_change_course_enrollment(): void
    {
        $lecturer = User::factory()->dosen()->create();
        $owner = User::factory()->dosen()->create();
        $course = Course::factory()->for($owner, 'lecturer')->create();
        $student = User::factory()->mahasiswa()->create();

        $this->actingAs($lecturer)
            ->get(route('dosen.courses.enrollments.index', $course))
            ->assertForbidden();

        $this->post(route('dosen.courses.enrollments.store', $course), [
            'student_id' => $student->id,
        ])->assertForbidden();
    }
}
