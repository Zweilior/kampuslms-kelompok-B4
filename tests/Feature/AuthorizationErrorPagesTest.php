<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AuthorizationErrorPagesTest extends TestCase
{
    public function test_guest_sees_the_401_page_for_a_protected_web_route(): void
    {
        $this->get('/admin/dashboard')
            ->assertStatus(401)
            ->assertSee('401')
            ->assertSee('Anda Perlu Login Terlebih Dahulu');

        $this->get('/courses')->assertUnauthorized();
    }

    public function test_user_without_the_required_role_sees_the_403_page(): void
    {
        $user = new User();
        $user->role = 'mahasiswa';

        $this->actingAs($user)
            ->get('/admin/dashboard')
            ->assertForbidden()
            ->assertSee('403')
            ->assertSee('Akses ke Halaman Ini Ditolak');
    }

    public function test_api_unauthenticated_response_remains_json(): void
    {
        $this->getJson('/api/v1/me')
            ->assertUnauthorized()
            ->assertJsonStructure(['message']);
    }

    public function test_invalid_login_message_is_shown_below_the_password_field(): void
    {
        Auth::shouldReceive('attempt')->once()->andReturnFalse();

        $this->from('/login')
            ->followingRedirects()
            ->post('/login', [
                'identity' => 'nim-tidak-valid',
                'password' => 'kata-sandi-salah',
            ])
            ->assertOk()
            ->assertSeeInOrder([
                'Kata Sandi',
                'NIM/Email atau kata sandi salah. Silakan periksa kembali.',
            ]);
    }

    public function test_authenticated_navbar_has_a_logout_confirmation_dialog(): void
    {
        Route::get('/test-navbar', fn () => view('components.navbar'))
            ->middleware('auth');

        foreach (['admin', 'dosen', 'mahasiswa'] as $role) {
            $user = new User();
            $user->name = 'Pengguna Tes';
            $user->role = $role;

            $this->actingAs($user)
                ->get('/test-navbar')
                ->assertOk()
                ->assertSee('Yakin ingin keluar?')
                ->assertSee('Ya, keluar')
                ->assertSee('Batal')
                ->assertSee('logout-dialog--confirmation', false)
                ->assertDontSee('logout-dialog__icon', false)
                ->assertSee('x-teleport="body"', false);
        }
    }
}
