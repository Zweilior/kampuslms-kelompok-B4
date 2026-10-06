<?php

namespace Tests\Feature;

use Tests\TestCase;

class AboutPageTest extends TestCase
{
    public function test_about_page_is_public_and_shows_the_team_and_system_information(): void
    {
        $this->get('/tentang')
            ->assertOk()
            ->assertSee('TENTANG EDUSPACE')
            ->assertSee('Tim Pengembang')
            ->assertSee('Muhammad Arif Saputra')
            ->assertSee('Ketua Tim Pengembang')
            ->assertSee('10241044')
            ->assertSee('Program Studi')
            ->assertSee('Sistem Informasi')
            ->assertSee('Moh. Irsyad Fiqi Ferdiansyah Difa Nanda')
            ->assertSee('Front-end Developer')
            ->assertSee('10241042')
            ->assertSee('Marchelino Senduk Kaunang')
            ->assertSee('Back-end Developer')
            ->assertSee('10241040')
            ->assertSee('Laudya Aprilia Khoirum')
            ->assertSee('10241038')
            ->assertSee('mailto:10241044@student.itk.ac.id')
            ->assertSee('Memimpin koordinasi tim pengembang')
            ->assertSee('Informasi Sistem')
            ->assertSee('Laravel 12');
    }

    public function test_login_page_links_to_about_page(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee(route('tentang'))
            ->assertSee('Tentang Kami');
    }
}
