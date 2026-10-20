@extends('components.layout')

@section('content')
    <div class="mahasiswa-page mahasiswa-page--submissions">
        <header class="mahasiswa-page__header lms-page-header">
            <div class="mahasiswa-page__header-copy">
                <a href="{{ route('mahasiswa.courses.assignments.index', $course->id) }}" class="mahasiswa-page__back">
                    <span class="material-symbols-outlined">arrow_back</span>
                    Kembali ke daftar tugas
                </a>
                <h1 class="mahasiswa-page__title">Status pengumpulan & nilai</h1>
                <p class="mahasiswa-page__description">{{ $assignment->title }} · {{ $course->name }}</p>
            </div>
            <span class="mahasiswa-page__icon-box"><span class="material-symbols-outlined">grade</span></span>
        </header>

        <section class="mahasiswa-page__card mahasiswa-page__table-wrap">
            <table class="mahasiswa-page__table lms-data-table">
                <thead>
                    <tr>
                        <th>Berkas Tugas</th>
                        <th>Waktu Dikumpul</th>
                        <th>Catatan</th>
                        <th>Nilai</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($submissions as $submission)
                        @php
                            $canResubmit = ! $submission->grade && (now()->lte($assignment->due_at) || $assignment->allow_late);
                        @endphp
                        <tr>
                            <td>
                                <div class="font-bold text-on-surface">{{ $submission->original_name }}</div>
                                <div class="flex items-center gap-2 mt-0.5">
                                    @if ($submission->fileSizeForHumans)
                                        <span class="text-xs text-on-surface-variant">{{ $submission->fileSizeForHumans }}</span>
                                    @endif
                                    @if ($submission->is_late)
                                        <span class="inline-flex items-center rounded-full bg-error/10 px-2 py-0.5 text-xs font-semibold text-error">
                                            Terlambat
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($submission->submitted_at)->format('d M Y, H:i') }}</td>
                            <td>{{ $submission->note ?? '-' }}</td>
                            <td>
                                @if ($submission->score !== null)
                                    <span class="mahasiswa-page__pill mahasiswa-page__pill--success">
                                        {{ $submission->score }} / {{ $assignment->max_score }}
                                    </span>
                                    @if ($submission->grade?->feedback)
                                        <p class="mt-1 max-w-xs text-xs text-on-surface-variant italic">
                                            "{{ $submission->grade->feedback }}"
                                        </p>
                                    @endif
                                @else
                                    <span class="mahasiswa-page__pill">Belum dinilai</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <div class="inline-flex flex-wrap items-center justify-end gap-2">
                                    <a href="{{ route('submissions.download', $submission) }}"
                                       class="inline-flex items-center gap-1 rounded-lg bg-surface-container-high px-3 py-1.5 font-label-sm text-xs text-on-surface hover:text-primary transition-colors"
                                       title="Unduh berkas saya">
                                        <span class="material-symbols-outlined text-sm">download</span>
                                        Unduh berkas saya
                                    </a>

                                    @if ($canResubmit)
                                        <a href="{{ route('mahasiswa.courses.assignments.submissions.create', [$course->id, $assignment->id]) }}"
                                           class="inline-flex items-center gap-1 rounded-lg bg-primary px-3 py-1.5 font-label-sm text-xs text-on-primary hover:bg-primary-container transition-colors"
                                           title="Kumpulkan ulang jawaban tugas">
                                            <span class="material-symbols-outlined text-sm">replay</span>
                                            Kumpulkan ulang
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="mahasiswa-page__empty py-space-lg text-center text-on-surface-variant">
                                <div><span class="material-symbols-outlined text-3xl mb-1">upload_file</span></div>
                                <p class="mb-2">Kamu belum mengumpulkan tugas ini.</p>
                                @if (now()->lte($assignment->due_at) || $assignment->allow_late)
                                    <a href="{{ route('mahasiswa.courses.assignments.submissions.create', [$course->id, $assignment->id]) }}"
                                       class="inline-flex items-center gap-1 rounded-xl bg-primary px-space-md py-2 text-xs font-semibold text-on-primary hover:bg-primary-container">
                                        <span class="material-symbols-outlined text-sm">upload</span>
                                        Kumpulkan sekarang
                                    </a>
                                @else
                                    <span class="inline-block rounded-full bg-error/10 px-3 py-1 text-xs font-medium text-error">
                                        Pengumpulan telah ditutup karena melewati tenggat waktu.
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </section>
    </div>
@endsection
