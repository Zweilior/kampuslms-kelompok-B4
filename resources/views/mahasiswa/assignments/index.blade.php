@extends('components.layout')

@section('content')
    <div class="mahasiswa-page mahasiswa-page--assignments">
        <header class="mahasiswa-page__header">
            <div class="mahasiswa-page__header-copy">
                <a href="{{ route('mahasiswa.courses.show', $course->id) }}" class="mahasiswa-page__back">
                    <span class="material-symbols-outlined">arrow_back</span>
                    Kembali ke kelas
                </a>
                <span class="mahasiswa-page__eyebrow">Aktivitas kelas</span>
                <h1 class="mahasiswa-page__title">Daftar tugas</h1>
                <p class="mahasiswa-page__description">{{ $course->name }} · {{ $assignments->count() }} tugas tersedia</p>
            </div>
            <span class="mahasiswa-page__icon-box"><span class="material-symbols-outlined">assignment</span></span>
        </header>

        <div class="mahasiswa-page__grid mahasiswa-page__grid--assignments">
            @forelse($assignments as $assignment)
                <article class="mahasiswa-page__card mahasiswa-page__assignment-card">
                    <div class="mahasiswa-page__assignment-meta">
                        <span class="mahasiswa-page__pill">Maks. nilai {{ $assignment->max_score }}</span>
                        <span class="mahasiswa-page__due">
                            <span class="material-symbols-outlined">schedule</span>
                            {{ \Carbon\Carbon::parse($assignment->due_at)->format('d M Y, H:i') }}
                        </span>
                    </div>
                    <h2 class="mahasiswa-page__assignment-title">{{ $assignment->title }}</h2>
                    <p class="mahasiswa-page__assignment-description">{{ Str::limit($assignment->instructions, 140) }}</p>
                    <div class="mahasiswa-page__button-row">
                        <a href="{{ route('mahasiswa.courses.assignments.submissions.create', [$course->id, $assignment->id]) }}"
                            class="mahasiswa-page__button mahasiswa-page__button--primary">
                            Kumpulkan
                            <span class="material-symbols-outlined">upload_file</span>
                        </a>
                        <a href="{{ route('mahasiswa.courses.assignments.submissions.index', [$course->id, $assignment->id]) }}"
                            class="mahasiswa-page__button">
                            Lihat nilai
                            <span class="material-symbols-outlined">grade</span>
                        </a>
                    </div>
                </article>
            @empty
                <div class="mahasiswa-page__empty">
                    <div><span class="material-symbols-outlined">task_alt</span>Belum ada tugas untuk mata kuliah ini.</div>
                </div>
            @endforelse
        </div>
    </div>
@endsection
