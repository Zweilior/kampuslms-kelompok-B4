@extends('components.layout')

@section('content')
    <div class="mahasiswa-page mahasiswa-page--course-detail">
        <header class="mahasiswa-page__header">
            <div class="mahasiswa-page__header-copy">
                <a href="{{ route('mahasiswa.courses.index') }}" class="mahasiswa-page__back">
                    <span class="material-symbols-outlined">arrow_back</span>
                    Daftar mata kuliah
                </a>
                <span class="mahasiswa-page__eyebrow">Detail kelas</span>
                <h1 class="mahasiswa-page__title">{{ $course->name }}</h1>
                <p class="mahasiswa-page__description">Informasi dan materi pembelajaran untuk kelas ini.</p>
            </div>
            <span class="mahasiswa-page__pill mahasiswa-page__pill--success">{{ ucfirst($course->status) }}</span>
        </header>

        <section class="mahasiswa-page__card mahasiswa-page__course-hero">
            <div>
                <div class="mahasiswa-page__pills">
                    <span class="mahasiswa-page__pill mahasiswa-page__pill--code">{{ $course->code }}</span>
                    <span class="mahasiswa-page__pill">{{ $course->sks }} SKS</span>
                </div>
                <h2 class="mahasiswa-page__course-title">Tentang mata kuliah</h2>
                <p class="mahasiswa-page__course-description">{{ $course->description }}</p>
                <div class="mahasiswa-page__course-lecturer">
                    <span
                        class="mahasiswa-page__teacher-avatar">{{ strtoupper(substr($course->lecturer?->name ?? 'D', 0, 1)) }}</span>
                    <span class="mahasiswa-page__teacher-copy">
                        <strong>{{ $course->lecturer?->name ?? 'Belum ditentukan' }}</strong>
                        <span>Dosen pengampu</span>
                    </span>
                </div>
            </div>
            <span class="mahasiswa-page__icon-box"><span class="material-symbols-outlined">school</span></span>
        </section>

        <div class="mahasiswa-page__grid mahasiswa-page__grid--quick-links">
            <a href="{{ route('mahasiswa.courses.materials.index', $course->id) }}"
                class="mahasiswa-page__card mahasiswa-page__quick-card">
                <span class="mahasiswa-page__icon-box"><span class="material-symbols-outlined">auto_stories</span></span>
                <strong>Materi perkuliahan</strong>
                <span>Lihat dan buka modul atau slide yang dibagikan dosen.</span>
            </a>
            <a href="{{ route('mahasiswa.courses.assignments.index', $course->id) }}"
                class="mahasiswa-page__card mahasiswa-page__quick-card">
                <span class="mahasiswa-page__icon-box"><span class="material-symbols-outlined">assignment</span></span>
                <strong>Tugas dan pengumpulan</strong>
                <span>Cek instruksi tugas, tenggat, dan status pengumpulan.</span>
            </a>
        </div>
    </div>
@endsection
