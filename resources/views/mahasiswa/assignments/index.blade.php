@extends('components.layout')

@section('content')
    <div class="mahasiswa-page mahasiswa-page--assignments">
        <header class="mahasiswa-page__header lms-page-header">
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
                @php
                    $submission = $assignment->submissions->first();
                    $isPastDue = now()->gt($assignment->due_at);
                    $isClosed = ! $submission && $isPastDue && ! $assignment->allow_late;

                    if ($submission) {
                        $statusText = $submission->is_late ? 'Terlambat' : 'Sudah';
                        $statusClass = $submission->is_late
                            ? 'bg-amber-500/10 text-amber-700 dark:text-amber-300'
                            : 'bg-primary/10 text-primary';
                    } elseif ($isPastDue) {
                        $statusText = 'Terlewat (Belum)';
                        $statusClass = 'bg-error/10 text-error';
                    } else {
                        $statusText = 'Belum';
                        $statusClass = 'bg-surface-container-high text-on-surface-variant';
                    }

                    $targetUrl = $submission
                        ? route('mahasiswa.courses.assignments.submissions.index', [$course->id, $assignment->id])
                        : ($isClosed ? null : route('mahasiswa.courses.assignments.submissions.create', [$course->id, $assignment->id]));
                @endphp

                @if ($targetUrl)
                    <a href="{{ $targetUrl }}" class="mahasiswa-page__assignment-row">
                        <span class="mahasiswa-page__assignment-row-copy">
                            <strong>{{ $assignment->title }}</strong>
                            <span>Tenggat {{ $assignment->due_at->format('d M Y, H:i') }} · Maks. {{ $assignment->max_score }} poin</span>
                        </span>
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $statusClass }}">
                            {{ $statusText }}
                        </span>
                        <span class="sr-only">{{ $submission ? 'Submitted' : 'Assigned' }}</span>
                        <span class="material-symbols-outlined mahasiswa-page__assignment-row-arrow" aria-hidden="true">
                            chevron_right
                        </span>
                    </a>
                @else
                    <div class="mahasiswa-page__assignment-row opacity-60 cursor-not-allowed" title="Pengumpulan tugas ini sudah ditutup karena melewati tenggat.">
                        <span class="mahasiswa-page__assignment-row-copy">
                            <strong>{{ $assignment->title }}</strong>
                            <span class="text-error font-medium">Tenggat {{ $assignment->due_at->format('d M Y, H:i') }} · Ditutup (tidak menerima keterlambatan)</span>
                        </span>
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $statusClass }}">
                            {{ $statusText }}
                        </span>
                        <span class="sr-only">{{ $submission ? 'Submitted' : 'Assigned' }}</span>
                        <span class="material-symbols-outlined mahasiswa-page__assignment-row-arrow text-outline/50" aria-hidden="true">
                            lock
                        </span>
                    </div>
                @endif
            @empty
                <div class="mahasiswa-page__empty">
                    <div><span class="material-symbols-outlined">task_alt</span>Belum ada tugas untuk mata kuliah ini.</div>
                </div>
            @endforelse
        </section>
    </div>
@endsection
