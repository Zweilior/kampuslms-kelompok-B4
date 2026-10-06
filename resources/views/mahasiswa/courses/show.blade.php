@extends('components.layout')

@section('content')
    <div class="mahasiswa-page mahasiswa-page--course-detail">
        <header class="mahasiswa-page__header">
            <div class="mahasiswa-page__header-copy">
                <a href="{{ route('mahasiswa.courses.index') }}" class="mahasiswa-page__back">
                    <span class="material-symbols-outlined">arrow_back</span>
                    Daftar mata kuliah
                </a>
                <h1 class="mahasiswa-page__title">{{ $course->name }}</h1>
                <div class="mahasiswa-page__pills mahasiswa-page__course-meta">
                    <span class="mahasiswa-page__pill mahasiswa-page__pill--code">{{ $course->code }}</span>
                    <span class="mahasiswa-page__pill">{{ $course->sks }} SKS</span>
                </div>
            </div>
            <span class="mahasiswa-page__pill mahasiswa-page__pill--success">{{ ucfirst($course->status) }}</span>
        </header>

        <div class="mahasiswa-page__grid mahasiswa-page__grid--quick-links mahasiswa-page__grid--course-actions">
            <a href="{{ route('mahasiswa.courses.materials.index', $course->id) }}"
                class="mahasiswa-page__card mahasiswa-page__quick-card">
                <span class="mahasiswa-page__icon-box"><span class="material-symbols-outlined">auto_stories</span></span>
                <span class="mahasiswa-page__quick-card-copy">
                    <strong>Materi perkuliahan</strong>
                    <span>Lihat dan buka modul atau slide yang dibagikan dosen.</span>
                </span>
                <span class="material-symbols-outlined mahasiswa-page__quick-card-arrow" aria-hidden="true">chevron_right</span>
            </a>
            <a href="{{ route('mahasiswa.courses.assignments.index', $course->id) }}"
                class="mahasiswa-page__card mahasiswa-page__quick-card">
                <span class="mahasiswa-page__icon-box"><span class="material-symbols-outlined">assignment</span></span>
                <span class="mahasiswa-page__quick-card-copy">
                    <strong>Tugas dan pengumpulan</strong>
                    <span>Cek instruksi tugas, tenggat, dan status pengumpulan.</span>
                </span>
                <span class="material-symbols-outlined mahasiswa-page__quick-card-arrow" aria-hidden="true">chevron_right</span>
            </a>
            <a href="{{ route('mahasiswa.courses.grades.show', $course->id) }}"
                class="mahasiswa-page__card mahasiswa-page__quick-card">
                <span class="mahasiswa-page__icon-box"><span class="material-symbols-outlined">grading</span></span>
                <span class="mahasiswa-page__quick-card-copy">
                    <strong>Rincian nilai</strong>
                    <span>Lihat nilai tiap tugas, bobot penilaian, dan predikat mata kuliah.</span>
                </span>
                <span class="material-symbols-outlined mahasiswa-page__quick-card-arrow" aria-hidden="true">chevron_right</span>
            </a>
        </div>
    </div>
@endsection
