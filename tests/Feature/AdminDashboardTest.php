<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_shows_greeting_inside_the_green_hero(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Halo, ' . $admin->name)
            ->assertSee('admin-dashboard__header', false)
            ->assertSee(route('admin.users.index'))
            ->assertSee('Kelola pengguna');
    }
}
