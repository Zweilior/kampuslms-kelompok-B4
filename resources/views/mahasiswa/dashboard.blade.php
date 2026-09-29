@extends('components.layout')

@section('content')
    <div class="mahasiswa-page mahasiswa-page--dashboard">
        <header class="mahasiswa-page__header">
            <div class="mahasiswa-page__header-copy">
                <span class="mahasiswa-page__eyebrow">Dashboard Mahasiswa</span>
                <h1 class="mahasiswa-page__title">Selamat datang kembali!</h1>
                <p class="mahasiswa-page__description">Pantau jadwal kuliah, deadline tugas, dan aktivitas akademikmu di
                    sini.</p>
            </div>
            <a href="{{ route('mahasiswa.courses.index') }}" class="mahasiswa-page__action">
                <span class="material-symbols-outlined">menu_book</span>
                Jelajahi kelas
            </a>
        </header>

        <div class="mahasiswa-page__stats">
            <article class="mahasiswa-page__card mahasiswa-page__stat-card">
                <div class="mahasiswa-page__stat-label">
                    <span class="mahasiswa-page__icon-box"><span class="material-symbols-outlined">menu_book</span></span>
                    Mata kuliah diikuti
                </div>
                <div class="mahasiswa-page__stat-value"><strong>6</strong><span>Kelas</span></div>
            </article>
            <article class="mahasiswa-page__card mahasiswa-page__stat-card">
                <div class="mahasiswa-page__stat-label">
                    <span class="mahasiswa-page__icon-box"><span
                            class="material-symbols-outlined">pending_actions</span></span>
                    Tugas perlu dikumpul
                </div>
                <div class="mahasiswa-page__stat-value"><strong>2</strong><span>Pending</span></div>
            </article>
            <article class="mahasiswa-page__card mahasiswa-page__stat-card">
                <div class="mahasiswa-page__stat-label">
                    <span class="mahasiswa-page__icon-box"><span class="material-symbols-outlined">fact_check</span></span>
                    Tugas sudah dinilai
                </div>
                <div class="mahasiswa-page__stat-value"><strong>5</strong><span>Tugas</span></div>
            </article>
        </div>

        <section class="mahasiswa-page__card mahasiswa-page__panel">
            <div class="mahasiswa-page__panel-heading">
                <h2 class="mahasiswa-page__panel-title">
                    <span class="material-symbols-outlined">event_upcoming</span>
                    Tugas mendatang
                </h2>
                <span class="mahasiswa-page__pill">Prioritas</span>
            </div>
            <div class="mahasiswa-page__deadline">
                <div>
                    <p class="mahasiswa-page__deadline-title">Pemrograman Web - Making CRUD UI</p>
                    <span class="mahasiswa-page__deadline-date">
                        <span class="material-symbols-outlined">schedule</span>
                        Tenggat: Besok, 23:59 WIB
                    </span>
                </div>
                <a href="{{ route('mahasiswa.courses.assignments.index', 1) }}"
                    class="mahasiswa-page__button mahasiswa-page__button--primary">
                    Kerjakan tugas
                    <span class="material-symbols-outlined">arrow_forward</span>
                </a>
            </div>
        </section>
    </div>
@endsection
