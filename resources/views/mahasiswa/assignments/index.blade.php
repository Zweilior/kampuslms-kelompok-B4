@extends('components.layout')

@section('content')
    <div class="mahasiswa-page mahasiswa-page--assignments">
        <header class="mahasiswa-page__header">
            <div class="mahasiswa-page__header-copy">
                <a href="{{ route('mahasiswa.courses.show', $course->id) }}" class="mahasiswa-page__back">
                    <span class="material-symbols-outlined">arrow_back</span>
                    Kembali ke kelas
                </a>
                <h1 class="mahasiswa-page__title">Daftar tugas</h1>
                <p class="mahasiswa-page__description">{{ $course->name }} · {{ $assignments->count() }} tugas tersedia</p>
            </div>
            <span class="mahasiswa-page__icon-box"><span class="material-symbols-outlined">assignment</span></span>
        </header>

        <section class="mahasiswa-page__card mahasiswa-page__assignment-list">
            @forelse($assignments as $assignment)
                @php($submission = $assignment->submissions->first())
                <a href="{{ $submission
                    ? route('mahasiswa.courses.assignments.submissions.index', [$course->id, $assignment->id])
                    : route('mahasiswa.courses.assignments.submissions.create', [$course->id, $assignment->id]) }}"
                    class="mahasiswa-page__assignment-row">
                    <span class="mahasiswa-page__assignment-row-copy">
                        <strong>{{ $assignment->title }}</strong>
                        <span>Tenggat {{ $assignment->due_at->format('d M Y, H:i') }}</span>
                    </span>
                    <span class="mahasiswa-page__assignment-row-status {{ $submission ? 'is-submitted' : '' }}">
                        {{ $submission ? 'Submitted' : 'Assigned' }}
                    </span>
                    <span class="material-symbols-outlined mahasiswa-page__assignment-row-arrow" aria-hidden="true">
                        chevron_right
                    </span>
                </a>
            @empty
                <div class="mahasiswa-page__empty">
                    <div><span class="material-symbols-outlined">task_alt</span>Belum ada tugas untuk mata kuliah ini.</div>
                </div>
            @endforelse
        </section>
    </div>
@endsection
