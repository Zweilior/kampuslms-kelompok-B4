@extends('components.layout')

@section('content')
    <div class="mahasiswa-page mahasiswa-page--materials">
        <header class="mahasiswa-page__header">
            <div class="mahasiswa-page__header-copy">
                <a href="{{ route('mahasiswa.courses.show', $course->id) }}" class="mahasiswa-page__back">
                    <span class="material-symbols-outlined">arrow_back</span>
                    Kembali ke kelas
                </a>
                <h1 class="mahasiswa-page__title">Materi perkuliahan</h1>
                <p class="mahasiswa-page__description">{{ $course->name }} · {{ $materials->count() }} materi tersedia</p>
            </div>
            <span class="mahasiswa-page__icon-box"><span class="material-symbols-outlined">auto_stories</span></span>
        </header>

        <section class="mahasiswa-page__card mahasiswa-page__list">
            @forelse($materials as $material)
                <article class="mahasiswa-page__list-item">
                    <div class="mahasiswa-page__icon-box"><span class="material-symbols-outlined">description</span></div>
                    <div class="mahasiswa-page__list-copy">
                        <strong>{{ $material->title }}</strong>
                        <p>{{ $material->description }}</p>
                    </div>
                    @if ($material->external_url)
                        <a href="{{ $material->external_url }}" target="_blank" rel="noopener noreferrer"
                            class="mahasiswa-page__button mahasiswa-page__button--primary">
                            Buka materi
                            <span class="material-symbols-outlined">open_in_new</span>
                        </a>
                    @endif
                </article>
            @empty
                <div class="mahasiswa-page__empty">
                    <div><span class="material-symbols-outlined">folder_open</span>Belum ada materi yang diunggah dosen.
                    </div>
                </div>
            @endforelse
        </section>
    </div>
@endsection
