<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_email_and_is_redirected_to_role_dashboard(): void
    {
        $dashboards = [
            'admin' => 'admin.dashboard',
            'dosen' => 'dosen.dashboard',
            'mahasiswa' => 'mahasiswa.dashboard',
        ];

        foreach ($dashboards as $role => $dashboard) {
            $user = User::factory()->create([
                'role' => $role,
                'email' => "login-{$role}@example.test",
            ]);

            $response = $this->post('/login', [
                'identity' => $user->email,
                'password' => 'password',
            ]);

            $response->assertRedirect(route($dashboard));
            $this->assertAuthenticatedAs($user);

            auth()->logout();
            $this->assertGuest();
        }
    }

    public function test_user_can_login_with_nim_or_nip(): void
    {
        $user = User::factory()->mahasiswa()->create([
            'nim_nip' => '20250001',
        ]);

        $this->post('/login', [
            'identity' => $user->nim_nip,
            'password' => 'password',
        ])
            ->assertRedirect(route('mahasiswa.dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_invalid_credentials_keep_user_logged_out_and_show_error(): void
    {
        $this->assertGuest();

        $this->from('/login')
            ->post('/login', [
                'identity' => 'pengguna-tidak-valid',
                'password' => 'kata-sandi-salah',
            ])
            ->assertRedirect('/login')
            ->assertSessionHasErrors('auth');

        $this->assertGuest();
    }

    public function test_guest_cannot_access_protected_dashboard(): void
    {
        $this->get(route('mahasiswa.dashboard'))
            ->assertUnauthorized();

        $this->assertGuest();
    }
}
