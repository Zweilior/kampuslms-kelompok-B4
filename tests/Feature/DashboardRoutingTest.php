<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardRoutingTest extends TestCase
{
    use RefreshDatabase;

    public function test_legacy_dashboard_path_redirects_each_role_to_its_dashboard(): void
    {
        foreach ([
            'admin' => 'admin.dashboard',
            'dosen' => 'dosen.dashboard',
            'mahasiswa' => 'mahasiswa.dashboard',
        ] as $role => $dashboardRoute) {
            $user = User::factory()->create(['role' => $role]);

            $this->actingAs($user)
                ->get('/dashboard')
                ->assertRedirect(route($dashboardRoute));
        }
    }

    public function test_guest_cannot_open_the_legacy_dashboard_path(): void
    {
        $this->get('/dashboard')->assertUnauthorized();
    }
}
