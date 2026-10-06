<?php

namespace Tests\Feature;

use App\Models\User;
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
}
