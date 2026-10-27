@extends('components.layout')

@section('content')
    <div class="mahasiswa-page mahasiswa-page--submissions">
        <header class="mahasiswa-page__header lms-page-header">
            <div class="mahasiswa-page__header-copy">
                <a href="{{ route('mahasiswa.courses.assignments.index', $course->id) }}" class="mahasiswa-page__back">
                    <span class="material-symbols-outlined">arrow_back</span>
                    Kembali ke daftar tugas
                </a>
                <h1 class="mahasiswa-page__title">Status Pengumpulan & Nilai</h1>
                <p class="mahasiswa-page__description">{{ $assignment->title }} · {{ $course->name }}</p>
            </div>
            <span class="mahasiswa-page__icon-box"><span class="material-symbols-outlined">grade</span></span>
        </header>

        @php
            $latestSubmission = $submissions->first();
        @endphp

        {{-- Panel Sorotan Nilai & Feedback jika sudah dinilai --}}
        @if ($latestSubmission && $latestSubmission->grade)
            <section class="mb-6 rounded-2xl border border-primary/20 bg-gradient-to-br from-primary/[0.06] to-surface-container-low p-5 sm:p-6 shadow-sm" aria-label="Hasil penilaian tugas">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-4 border-b border-outline/10">
                    <div class="flex items-center gap-3.5">
                        <div class="w-12 h-12 rounded-2xl bg-primary/15 text-primary flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-2xl">verified</span>
                        </div>
                        <div>
                            <span class="inline-flex items-center gap-1 text-xs font-semibold uppercase tracking-wider text-primary">
                                Tugas Telah Dinilai
                            </span>
                            <h2 class="text-xl sm:text-2xl font-bold text-on-surface">Hasil Penilaian Tugas</h2>
                            <p class="text-xs text-on-surface-variant mt-0.5">
                                Dinilai pada {{ $latestSubmission->grade->graded_at ? \Carbon\Carbon::parse($latestSubmission->grade->graded_at)->format('d M Y, H:i') : '-' }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-baseline gap-1.5 self-start sm:self-auto bg-surface-container px-4 py-2.5 rounded-2xl border border-primary/20 shadow-sm">
                        <span class="text-3xl sm:text-4xl font-extrabold text-primary">
                            {{ $latestSubmission->grade->score }}
                        </span>
                        <span class="text-sm font-semibold text-on-surface-variant">
                            / {{ $assignment->max_score }} poin
                        </span>
                    </div>
                </div>

                {{-- Umpan Balik Dosen --}}
                <div class="mt-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-on-surface-variant flex items-center gap-1.5 mb-2">
                        <span class="material-symbols-outlined text-base text-primary">feedback</span>
                        Umpan Balik / Catatan Dosen
                    </h3>
                    @if ($latestSubmission->grade->feedback)
                        <div class="rounded-xl border border-primary/15 bg-surface-container-high/60 p-4 text-sm text-on-surface italic shadow-sm leading-relaxed">
                            "{{ $latestSubmission->grade->feedback }}"
                        </div>
                    @else
                        <p class="text-xs text-on-surface-variant italic">
                            Dosen tidak menyertakan catatan atau umpan balik tertulis untuk tugas ini.
                        </p>
                    @endif
                </div>
            </section>
        @elseif ($latestSubmission)
            <section class="mb-6 rounded-2xl border border-outline/15 bg-surface-container-low p-4 sm:p-5 flex items-center gap-3.5 shadow-sm" aria-label="Status menunggu penilaian">
                <div class="w-11 h-11 rounded-2xl bg-amber-500/10 text-amber-600 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-2xl">pending</span>
                </div>
                <div class="min-w-0 flex-1">
                    <h2 class="text-sm sm:text-base font-bold text-on-surface">Tugas Sedang Menunggu Penilaian</h2>
                    <p class="text-xs text-on-surface-variant mt-0.5">
                        Tugas Anda telah berhasil dikumpulkan. Anda akan menerima notifikasi otomatis begitu dosen selesai memeriksa dan memberikan nilai.
                    </p>
                </div>
            </section>
        @endif

        <section class="mahasiswa-page__card mahasiswa-page__table-wrap" aria-label="Riwayat pengumpulan tugas">
            <table class="mahasiswa-page__table lms-data-table">
                <thead>
                    <tr>
                        <th>Berkas Tugas</th>
                        <th>Waktu Dikumpul</th>
                        <th>Catatan</th>
                        <th>Nilai & Umpan Balik</th>
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
                                    <div class="space-y-1">
                                        <span class="mahasiswa-page__pill mahasiswa-page__pill--success">
                                            {{ $submission->score }} / {{ $assignment->max_score }}
                                        </span>
                                        @if ($submission->grade?->feedback)
                                            <p class="max-w-xs text-xs text-on-surface-variant italic truncate" title="{{ $submission->grade->feedback }}">
                                                "{{ $submission->grade->feedback }}"
                                            </p>
                                        @endif
                                    </div>
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
