@extends('components.layout')

@section('content')
    <div class="mahasiswa-page mahasiswa-page--courses">
        <header class="mahasiswa-page__header">
            <div class="mahasiswa-page__header-copy">
                <span class="mahasiswa-page__eyebrow">Semester aktif</span>
                <h1 class="mahasiswa-page__title">Mata kuliah saya</h1>
                <p class="mahasiswa-page__description">Daftar mata kuliah yang kamu ikuti semester ini.</p>
            </div>
            <span class="mahasiswa-page__pill mahasiswa-page__pill--success">
                <span class="material-symbols-outlined">library_books</span>
                {{ $courses->count() }} kelas
            </span>
        </header>

        <div class="mahasiswa-page__grid mahasiswa-page__grid--courses">
            @forelse($courses as $course)
                <article class="mahasiswa-page__card mahasiswa-page__course-card">
                    <div class="mahasiswa-page__card-top">
                        <div class="mahasiswa-page__pills">
                            <span class="mahasiswa-page__pill mahasiswa-page__pill--code">{{ $course->code }}</span>
                            <span class="mahasiswa-page__pill">{{ $course->sks }} SKS</span>
                        </div>
                        <span
                            class="mahasiswa-page__pill mahasiswa-page__pill--success">{{ ucfirst($course->status) }}</span>
                    </div>
                    <h2 class="mahasiswa-page__course-title">{{ $course->name }}</h2>
                    <p class="mahasiswa-page__course-description">{{ Str::limit($course->description, 120) }}</p>
                    <div class="mahasiswa-page__teacher">
                        <span
                            class="mahasiswa-page__teacher-avatar">{{ strtoupper(substr($course->lecturer?->name ?? 'D', 0, 1)) }}</span>
                        <span class="mahasiswa-page__teacher-copy">
                            <strong>{{ $course->lecturer?->name ?? 'Belum ditentukan' }}</strong>
                            <span>Dosen pengampu</span>
                        </span>
                    </div>
                    <a href="{{ route('mahasiswa.courses.show', $course->id) }}"
                        class="mahasiswa-page__button mahasiswa-page__button--primary mahasiswa-page__button--wide">
                        Masuk kelas
                        <span class="material-symbols-outlined">arrow_forward</span>
                    </a>
                </article>
            @empty
                <div class="mahasiswa-page__empty">
                    <div><span class="material-symbols-outlined">auto_stories</span>Belum ada mata kuliah yang terdaftar.
                    </div>
                </div>
            @endforelse
        </div>
    </div>
@endsection
