@extends('components.layout')

@section('content')
    <div class="mahasiswa-page mahasiswa-page--dashboard">
        <header class="mahasiswa-page__header">
            <div class="mahasiswa-page__header-copy">
                <span class="mahasiswa-page__eyebrow">Dashboard Mahasiswa</span>
                <h1 class="mahasiswa-page__title">Halo, {{ auth()->user()->name }}</h1>
                <p class="mahasiswa-page__description">Ringkasan mata kuliah, tugas, pengumpulan, dan akses cepat ke nilai akademikmu.</p>
            </div>
            <a href="{{ route('mahasiswa.courses.index') }}" class="mahasiswa-page__action">
                <span class="material-symbols-outlined">menu_book</span>
                Semua mata kuliah
            </a>
        </header>

        <div class="mahasiswa-page__stats">
            <a href="{{ route('mahasiswa.courses.index') }}" class="mahasiswa-page__card mahasiswa-page__stat-card mahasiswa-page__summary-link">
                <div class="mahasiswa-page__stat-label">
                    <span class="mahasiswa-page__icon-box"><span class="material-symbols-outlined">menu_book</span></span>
                    Mata kuliah aktif
                </div>
                <div class="mahasiswa-page__stat-value"><strong>{{ $courses->count() }}</strong><span>Kelas</span></div>
            </a>
            <article class="mahasiswa-page__card mahasiswa-page__stat-card">
                <div class="mahasiswa-page__stat-label">
                    <span class="mahasiswa-page__icon-box"><span class="material-symbols-outlined">pending_actions</span></span>
                    Tugas belum dikumpulkan
                </div>
                <div class="mahasiswa-page__stat-value"><strong>{{ $pendingAssignmentCount }}</strong><span>Tugas</span></div>
            </article>
            <article class="mahasiswa-page__card mahasiswa-page__stat-card">
                <div class="mahasiswa-page__stat-label">
                    <span class="mahasiswa-page__icon-box"><span class="material-symbols-outlined">fact_check</span></span>
                    Tugas sudah dinilai
                </div>
                <div class="mahasiswa-page__stat-value"><strong>{{ $gradedAssignmentCount }}</strong><span>Tugas</span></div>
            </article>
        </div>

        <div class="mahasiswa-page__grid mahasiswa-page__grid--quick-links mahasiswa-page__dashboard-links">
            <a href="{{ route('mahasiswa.grades.index') }}" class="mahasiswa-page__card mahasiswa-page__quick-card">
                <span class="mahasiswa-page__icon-box"><span class="material-symbols-outlined">grading</span></span>
                <strong>Monitoring nilai</strong>
                <span>Lihat nilai akhir, predikat, dan IPS semester.</span>
            </a>
            <a href="{{ route('mahasiswa.courses.index') }}" class="mahasiswa-page__card mahasiswa-page__quick-card">
                <span class="mahasiswa-page__icon-box"><span class="material-symbols-outlined">assignment</span></span>
                <strong>Mata kuliah dan tugas</strong>
                <span>Buka kelas untuk melihat daftar tugas serta mengumpulkan jawaban.</span>
            </a>
        </div>

        <section class="mahasiswa-page__card mahasiswa-page__panel mahasiswa-page__upcoming-panel">
            <div class="mahasiswa-page__panel-heading">
                <h2 class="mahasiswa-page__panel-title">
                    <span class="material-symbols-outlined">event_upcoming</span>
                    Tugas yang perlu dikerjakan
                </h2>
                <a href="{{ route('mahasiswa.courses.index') }}" class="mahasiswa-page__text-link">Lihat kelas</a>
            </div>
            <div class="mahasiswa-page__list">
                @forelse ($upcomingAssignments as $assignment)
                    <a href="{{ route('mahasiswa.courses.assignments.submissions.create', [$assignment->course_id, $assignment->id]) }}"
                        class="mahasiswa-page__list-item mahasiswa-page__dashboard-assignment">
                        <span class="mahasiswa-page__list-copy">
                            <strong>{{ $assignment->title }}</strong>
                            <p>{{ $assignment->course->name }} · Tenggat {{ $assignment->due_at->format('d M Y, H:i') }}</p>
                        </span>
                        <span class="mahasiswa-page__assignment-row-status">Assigned</span>
                    </a>
                @empty
                    <div class="mahasiswa-page__empty">
                        <div><span class="material-symbols-outlined">task_alt</span>Tidak ada tugas mendatang yang belum dikumpulkan.</div>
                    </div>
                @endforelse
            </div>
        </section>
    </div>
@endsection
