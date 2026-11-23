<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCourseManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            VerifyCsrfToken::class,
            ValidateCsrfToken::class,
        ]);
    }

    public function test_admin_can_view_courses_index_create_and_show_pages(): void
    {
        $admin = User::factory()->admin()->create();
        $dosen = User::factory()->create(['role' => 'dosen']);
        $course = Course::factory()->for($dosen, 'lecturer')->create([
            'code' => 'SI251401',
            'name' => 'Pemrograman Web',
        ]);

        $this->actingAs($admin);

        $this->get(route('admin.courses.index'))
            ->assertOk()
            ->assertSee('SI251401')
            ->assertSee('Pemrograman Web');

        $this->get(route('admin.courses.create'))
            ->assertOk()
            ->assertSee(route('admin.courses.store'));

        $this->get(route('admin.courses.show', $course))
            ->assertOk()
            ->assertSee('SI251401')
            ->assertSee('Pemrograman Web');
    }

    public function test_admin_can_create_a_course(): void
    {
        $admin = User::factory()->admin()->create();
        $dosen = User::factory()->create(['role' => 'dosen']);

        $response = $this->actingAs($admin)
            ->post(route('admin.courses.store'), [
                'code' => 'SI251499',
                'name' => 'Arsitektur Enterprise',
                'description' => 'Mata kuliah perancangan arsitektur sistem informasi.',
                'sks' => 3,
                'lecturer_id' => $dosen->id,
                'status' => 'active',
            ]);

        $course = Course::where('code', 'SI251499')->firstOrFail();

        $response->assertRedirect(route('admin.courses.show', $course))
            ->assertSessionHas('success', 'Mata kuliah berhasil ditambahkan.');

        $this->assertDatabaseHas('courses', [
            'id' => $course->id,
            'code' => 'SI251499',
            'name' => 'Arsitektur Enterprise',
            'sks' => 3,
            'lecturer_id' => $dosen->id,
            'status' => 'active',
        ]);
    }

    public function test_admin_can_update_a_course(): void
    {
        $admin = User::factory()->admin()->create();
        $dosenAwal = User::factory()->create(['role' => 'dosen']);
        $dosenBaru = User::factory()->create(['role' => 'dosen']);

        $course = Course::factory()->for($dosenAwal, 'lecturer')->create([
            'code' => 'SI251410',
            'name' => 'Basis Data Dasar',
            'sks' => 3,
            'status' => 'draft',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.courses.edit', $course))
            ->assertOk()
            ->assertSee(route('admin.courses.update', $course));

        $this->actingAs($admin)
            ->put(route('admin.courses.update', $course), [
                'code' => 'SI251410',
                'name' => 'Basis Data Lanjut',
                'description' => 'Pembaruan deskripsi mata kuliah.',
                'sks' => 4,
                'lecturer_id' => $dosenBaru->id,
                'status' => 'active',
            ])
            ->assertRedirect(route('admin.courses.show', $course))
            ->assertSessionHas('success', 'Mata kuliah berhasil diperbarui.');

        $this->assertDatabaseHas('courses', [
            'id' => $course->id,
            'name' => 'Basis Data Lanjut',
            'sks' => 4,
            'lecturer_id' => $dosenBaru->id,
            'status' => 'active',
        ]);
    }

    public function test_admin_can_delete_a_course(): void
    {
        $admin = User::factory()->admin()->create();
        $dosen = User::factory()->create(['role' => 'dosen']);
        $course = Course::factory()->for($dosen, 'lecturer')->create();

        $this->actingAs($admin)
            ->delete(route('admin.courses.destroy', $course))
            ->assertRedirect(route('admin.courses.index'))
            ->assertSessionHas('success', 'Mata kuliah berhasil dihapus.');

        $this->assertDatabaseMissing('courses', [
            'id' => $course->id,
        ]);
    }

    public function test_admin_cannot_delete_a_course_with_existing_submissions(): void
    {
        $admin = User::factory()->admin()->create();
        $dosen = User::factory()->create(['role' => 'dosen']);
        $mahasiswa = User::factory()->mahasiswa()->create();
        $course = Course::factory()->for($dosen, 'lecturer')->create();

        $assignment = Assignment::factory()->create([
            'course_id' => $course->id,
        ]);

        Submission::factory()->create([
            'assignment_id' => $assignment->id,
            'user_id' => $mahasiswa->id,
        ]);

        $this->actingAs($admin)
            ->from(route('admin.courses.show', $course))
            ->delete(route('admin.courses.destroy', $course))
            ->assertRedirect(route('admin.courses.show', $course))
            ->assertSessionHas('error', 'Mata kuliah tidak dapat dihapus karena sudah memiliki pengumpulan tugas.');

        $this->assertDatabaseHas('courses', [
            'id' => $course->id,
        ]);
    }

    public function test_dosen_cannot_access_admin_course_crud_operations(): void
    {
        $dosen = User::factory()->create(['role' => 'dosen']);
        $course = Course::factory()->for($dosen, 'lecturer')->create([
            'name' => 'Mata Kuliah Awal',
        ]);

        $this->actingAs($dosen);

        $this->get(route('admin.courses.index'))->assertForbidden();
        $this->get(route('admin.courses.create'))->assertForbidden();
        $this->post(route('admin.courses.store'), [
            'code' => 'SI999901',
            'name' => 'Mata Kuliah Ilegal Dosen',
            'description' => '',
            'sks' => 3,
            'lecturer_id' => $dosen->id,
            'status' => 'active',
        ])->assertForbidden();

        $this->get(route('admin.courses.show', $course))->assertForbidden();
        $this->get(route('admin.courses.edit', $course))->assertForbidden();
        $this->put(route('admin.courses.update', $course), [
            'code' => $course->code,
            'name' => 'Diubah Lewat Route Admin',
            'description' => '',
            'sks' => 3,
            'lecturer_id' => $dosen->id,
            'status' => 'active',
        ])->assertForbidden();

        $this->delete(route('admin.courses.destroy', $course))->assertForbidden();

        $this->assertDatabaseHas('courses', [
            'id' => $course->id,
            'name' => 'Mata Kuliah Awal',
        ]);
    }

    public function test_mahasiswa_cannot_access_admin_course_crud_operations(): void
    {
        $dosen = User::factory()->create(['role' => 'dosen']);
        $mahasiswa = User::factory()->mahasiswa()->create();
        $course = Course::factory()->for($dosen, 'lecturer')->create([
            'name' => 'Mata Kuliah Terproteksi',
        ]);

        $this->actingAs($mahasiswa);

        $this->get(route('admin.courses.index'))->assertForbidden();
        $this->get(route('admin.courses.create'))->assertForbidden();
        $this->post(route('admin.courses.store'), [
            'code' => 'SI999902',
            'name' => 'Mata Kuliah Ilegal Mahasiswa',
            'description' => '',
            'sks' => 3,
            'lecturer_id' => $dosen->id,
            'status' => 'active',
        ])->assertForbidden();

        $this->get(route('admin.courses.show', $course))->assertForbidden();
        $this->get(route('admin.courses.edit', $course))->assertForbidden();
        $this->put(route('admin.courses.update', $course), [
            'code' => $course->code,
            'name' => 'Diubah Mahasiswa',
            'description' => '',
            'sks' => 3,
            'lecturer_id' => $dosen->id,
            'status' => 'active',
        ])->assertForbidden();

        $this->delete(route('admin.courses.destroy', $course))->assertForbidden();

        $this->assertDatabaseHas('courses', [
            'id' => $course->id,
            'name' => 'Mata Kuliah Terproteksi',
        ]);
    }

    public function test_guest_cannot_access_admin_course_routes(): void
    {
        $dosen = User::factory()->create(['role' => 'dosen']);
        $course = Course::factory()->for($dosen, 'lecturer')->create();

        $this->get(route('admin.courses.index'))->assertUnauthorized();
        $this->post(route('admin.courses.store'), [])->assertUnauthorized();
        $this->put(route('admin.courses.update', $course), [])->assertUnauthorized();
        $this->delete(route('admin.courses.destroy', $course))->assertUnauthorized();
    }
}