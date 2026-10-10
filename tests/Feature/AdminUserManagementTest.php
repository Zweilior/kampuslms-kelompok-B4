<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_manage_users_from_the_admin_views(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);

        $this->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee($admin->name)
            ->assertSee(route('admin.users.store'));

        $this->get(route('admin.users.create'))
            ->assertOk()
            ->assertSee(route('admin.users.store'))
            ->assertSee('NIM/NIP');

        $this->post(route('admin.users.store'), [
            'name' => 'Mahasiswa Baru',
            'email' => 'mahasiswa@example.test',
            'password' => 'password123',
            'nim_nip' => 'NIM2026001',
            'role' => 'mahasiswa',
        ])->assertRedirect(route('admin.users.index'));

        $user = User::where('email', 'mahasiswa@example.test')->firstOrFail();

        $this->get(route('admin.users.show', $user))
            ->assertOk()
            ->assertSee('Mahasiswa Baru')
            ->assertSee('NIM2026001');

        $this->get(route('admin.users.edit', $user))
            ->assertOk()
            ->assertSee(route('admin.users.update', $user))
            ->assertSee('mahasiswa@example.test');

        $this->put(route('admin.users.update', $user), [
            'name' => 'Mahasiswa Diperbarui',
            'email' => 'mahasiswa@example.test',
            'password' => '',
            'nim_nip' => 'NIM2026001',
            'role' => 'mahasiswa',
        ])->assertRedirect(route('admin.users.show', $user));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Mahasiswa Diperbarui',
        ]);

        $this->delete(route('admin.users.destroy', $user))
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_users_are_admin_only_and_general_user_route_is_removed(): void
    {
        $student = User::factory()->mahasiswa()->create();

        $this->actingAs($student)
            ->get(route('admin.users.index'))
            ->assertForbidden();

        $this->get('/users')->assertNotFound();
    }

    public function test_legacy_courses_path_redirects_by_role_and_general_course_views_are_gone(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get('/courses')
            ->assertRedirect(route('admin.courses.index'));
    }
}
