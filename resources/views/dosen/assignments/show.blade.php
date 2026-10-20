<x-layout title="Edit Nilai Tugas - Dosen" active-nav="courses">
    @php
        $notSubmitted = $notSubmitted ?? $unsubmittedStudents ?? $course->students()
            ->whereDoesntHave('submissions', fn ($q) => $q->where('assignment_id', $assignment->id))
            ->orderBy('nim_nip')
            ->get();
        $totalEnrolled = $course->students()->count();
        $submittedCount = $submissionCounts['all'] ?? $assignment->submissions()->count();
        $unsubmittedCount = $notSubmitted->count();
        $lateCount = $submissionCounts['late'] ?? $assignment->submissions()->where('is_late', true)->count();
        $isPastDue = $assignment->due_at->lte(now());
    @endphp

    <div class="dosen-grades">
        <a href="{{ route('dosen.courses.assignments.index', $course) }}" class="dosen-assignment-form__back">
            <span class="material-symbols-outlined" aria-hidden="true">arrow_back</span>
            Kembali ke daftar tugas
        </a>

        <header class="dosen-grades__header lms-page-header">
            <div>
                <div class="dosen-assignment-form__eyebrow">
                    <span>{{ $course->code }}</span>
                    <span class="dosen-assignment-form__dot" aria-hidden="true"></span>
                    <span>{{ $course->name }}</span>
                </div>
                <h1 class="dosen-assignment-form__title">{{ $assignment->title }}</h1>
                <p class="dosen-assignment-form__description">
                    Tenggat {{ $assignment->due_at->format('d M Y, H:i') }} · Nilai maksimal {{ $assignment->max_score }} poin
                </p>
            </div>
            <div class="dosen-grades__count">
                <span class="material-symbols-outlined" aria-hidden="true">groups</span>
                <span>{{ $submissionCounts['all'] }} pengumpulan</span>
            </div>
        </header>

        {{-- Ringkasan Pengumpulan --}}
        <div class="mb-space-md grid grid-cols-2 gap-space-sm sm:grid-cols-4">
            <div class="rounded-xl border border-outline/10 bg-surface-container-low p-space-sm text-center shadow-sm">
                <span class="block font-label-sm text-xs uppercase text-on-surface-variant">Total Terdaftar</span>
                <span class="font-headline-sm text-xl font-bold text-on-surface">{{ $totalEnrolled }}</span>
            </div>
            <div class="rounded-xl border border-primary/20 bg-primary/10 p-space-sm text-center shadow-sm">
                <span class="block font-label-sm text-xs uppercase text-primary">Sudah Mengumpulkan</span>
                <span class="font-headline-sm text-xl font-bold text-primary">{{ $submittedCount }}</span>
            </div>
            <div class="rounded-xl border border-outline/10 bg-surface-container-low p-space-sm text-center shadow-sm">
                <span class="block font-label-sm text-xs uppercase text-on-surface-variant">Belum Mengumpulkan</span>
                <span class="font-headline-sm text-xl font-bold text-on-surface">{{ $unsubmittedCount }}</span>
            </div>
            <div class="rounded-xl border border-error/20 bg-error/10 p-space-sm text-center shadow-sm">
                <span class="block font-label-sm text-xs uppercase text-error">Terlambat</span>
                <span class="font-headline-sm text-xl font-bold text-error">{{ $lateCount }}</span>
            </div>
        </div>

        <div class="mb-space-md flex flex-wrap items-center justify-between gap-space-sm">
            <form method="GET" action="{{ route('dosen.courses.assignments.submissions.index', [$course, $assignment]) }}" class="flex flex-wrap items-center gap-space-sm">
                <label for="submission-status" class="font-label-md text-label-md text-on-surface">Status penilaian</label>
                <select id="submission-status" name="status" onchange="this.form.submit()" class="rounded-xl border border-outline/20 bg-surface-container-low px-space-sm py-2 font-body-sm text-body-sm text-on-surface">
                    <option value="all" @selected($status === 'all')>Semua ({{ $submissionCounts['all'] }})</option>
                    <option value="graded" @selected($status === 'graded')>Sudah dinilai ({{ $submissionCounts['graded'] }})</option>
                    <option value="ungraded" @selected($status === 'ungraded')>Belum dinilai ({{ $submissionCounts['ungraded'] }})</option>
                </select>
                <button type="submit" class="rounded-xl border border-outline/20 px-space-sm py-2 font-label-sm text-label-sm text-on-surface hover:bg-surface-container">Terapkan</button>
            </form>
            <a href="{{ route('dosen.courses.assignments.submissions.export', [$course, $assignment, 'status' => $status]) }}"
               class="inline-flex items-center gap-space-xs rounded-xl bg-primary px-space-md py-2.5 font-label-md text-on-primary hover:bg-primary-container">
                <span class="material-symbols-outlined text-base" aria-hidden="true">download</span>
                Unduh CSV
            </a>
        </div>


        @if ($errors->any())
            <div class="dosen-assignment-form__errors" role="alert">
                <span class="material-symbols-outlined" aria-hidden="true">error</span>
                <div>
                    <p class="dosen-assignment-form__errors-title">Nilai belum berhasil disimpan.</p>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        @if (! $isPastDue)
            <div class="dosen-grades__notice dosen-grades__notice--info" role="alert">
                <span class="material-symbols-outlined" aria-hidden="true">schedule</span>
                Penilaian baru dapat dilakukan setelah tenggat tugas selesai.
            </div>
        @endif

        <section class="dosen-grades__list" aria-label="Daftar pengumpulan mahasiswa">
            @forelse ($submissions as $submission)
                <article class="dosen-grade-card">
                    <header class="dosen-grade-card__student">
                        <span class="dosen-grade-card__avatar" aria-hidden="true">
                            {{ strtoupper(substr($submission->student->name, 0, 1)) }}
                        </span>
                        <div class="dosen-grade-card__student-info">
                            <h2>{{ $submission->student->name }}</h2>
                            <p>NIM {{ $submission->student->nim_nip }}</p>
                        </div>
                        @if ($submission->grade)
                            <span class="dosen-grade-card__status dosen-grade-card__status--graded">
                                <span class="material-symbols-outlined" aria-hidden="true">check_circle</span>
                                Dinilai · {{ $submission->grade->score }}
                            </span>
                        @else
                            <span class="dosen-grade-card__status dosen-grade-card__status--pending">
                                <span class="material-symbols-outlined" aria-hidden="true">pending</span>
                                Menunggu nilai
                            </span>
                        @endif
                    </header>

                    <div class="dosen-grade-card__body">
                        <div class="dosen-grade-card__submission">
                            <span class="dosen-grade-card__label">File pengumpulan</span>
                            <a href="{{ route('submissions.download', $submission) }}" class="dosen-grade-card__file" title="Unduh berkas pengumpulan">
                                <span class="material-symbols-outlined" aria-hidden="true">description</span>
                                <span>{{ $submission->original_name }}</span>
                                @if ($submission->fileSizeForHumans)
                                    <span class="text-xs text-on-surface-variant font-normal">({{ $submission->fileSizeForHumans }})</span>
                                @endif
                                <span class="material-symbols-outlined dosen-grade-card__open" aria-hidden="true">download</span>
                            </a>
                            <p class="dosen-grade-card__meta">
                                Dikumpulkan {{ $submission->submitted_at->format('d M Y, H:i') }}
                                @if ($submission->is_late)
                                    <span class="dosen-grade-card__late">Terlambat</span>
                                @endif
                            </p>
                            @if ($submission->note)
                                <p class="dosen-grade-card__note">{{ $submission->note }}</p>
                            @endif
                        </div>

                        <form action="{{ route('dosen.courses.assignments.submissions.grade', [$course, $assignment, $submission]) }}" method="POST" class="dosen-grade-card__form">
                            @csrf
                            @method('PUT')
                            <div class="dosen-grade-card__form-fields">
                                <div class="dosen-assignment-form__field">
                                    <label for="score-{{ $submission->id }}">Nilai <span>(maks. {{ $assignment->max_score }})</span></label>
                                    <div class="dosen-assignment-form__number-wrap">
                                        <input id="score-{{ $submission->id }}"
                                               type="number"
                                               name="score"
                                               min="0"
                                               max="{{ $assignment->max_score }}"
                                               step="0.01"
                                               value="{{ old('score', $submission->grade?->score) }}"
                                               placeholder="0–{{ $assignment->max_score }}"
                                               @disabled(! $isPastDue)
                                               required>
                                        <span>poin</span>
                                    </div>
                                </div>
                                <div class="dosen-assignment-form__field">
                                    <label for="feedback-{{ $submission->id }}">Umpan Balik <span>(opsional)</span></label>
                                    <textarea id="feedback-{{ $submission->id }}"
                                              name="feedback"
                                              rows="2"
                                              maxlength="2000"
                                              placeholder="Berikan catatan untuk mahasiswa..."
                                              @disabled(! $isPastDue)>{{ old('feedback', $submission->grade?->feedback) }}</textarea>
                                </div>
                            </div>
                            <button type="submit"
                                    @disabled(! $isPastDue)
                                    class="dosen-assignment-form__button dosen-assignment-form__button--primary {{ ! $isPastDue ? 'opacity-50 cursor-not-allowed' : '' }}"
                                    title="{{ ! $isPastDue ? 'Penilaian baru dapat dilakukan setelah tenggat' : '' }}">
                                <span class="material-symbols-outlined" aria-hidden="true">{{ $submission->grade ? 'save' : 'grading' }}</span>
                                {{ $submission->grade ? 'Simpan Perubahan' : 'Beri Nilai' }}
                            </button>
                            @if (! $isPastDue)
                                <p class="mt-1 text-xs text-on-surface-variant">Penilaian dinonaktifkan hingga tenggat tugas berakhir.</p>
                            @endif
                        </form>
                    </div>
                </article>
            @empty
                <div class="dosen-grades__empty">
                    <span class="material-symbols-outlined" aria-hidden="true">inbox</span>
                    <h2>Belum ada pengumpulan</h2>
                    <p>Pengumpulan mahasiswa akan muncul di halaman ini.</p>
                </div>
            @endforelse
        </section>

        {{-- Bagian Mahasiswa Belum Mengumpulkan --}}
        <section class="mt-space-lg rounded-2xl border border-outline/10 bg-surface-container-low p-space-md shadow-sm" aria-label="Daftar mahasiswa belum mengumpulkan">
            <div class="mb-space-sm flex items-center justify-between">
                <div class="flex items-center gap-space-xs">
                    <span class="material-symbols-outlined text-on-surface-variant" aria-hidden="true">person_off</span>
                    <h2 class="font-headline-sm text-base font-semibold text-on-surface sm:text-lg">
                        Belum Mengumpulkan ({{ $notSubmitted->count() }})
                    </h2>
                </div>
            </div>

            @if ($notSubmitted->isEmpty())
                <div class="py-space-sm text-center font-body-sm text-sm text-on-surface-variant">
                    <span class="material-symbols-outlined mr-1 align-middle text-primary" aria-hidden="true">check_circle</span>
                    Semua mahasiswa yang terdaftar telah mengumpulkan tugas ini.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-surface-container font-label-sm text-xs uppercase text-on-surface-variant">
                            <tr>
                                <th class="px-space-sm py-2">NIM</th>
                                <th class="px-space-sm py-2">Nama Mahasiswa</th>
                                <th class="px-space-sm py-2 text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-outline/10 text-on-surface">
                            @foreach ($notSubmitted as $student)
                                <tr class="hover:bg-surface-container/50">
                                    <td class="px-space-sm py-2.5 font-mono text-xs text-on-surface-variant">{{ $student->nim_nip ?? '—' }}</td>
                                    <td class="px-space-sm py-2.5 font-medium">{{ $student->name }}</td>
                                    <td class="px-space-sm py-2.5 text-right">
                                        <span class="inline-flex rounded-full bg-surface-container-high px-2.5 py-0.5 text-xs font-medium text-on-surface-variant">
                                            Belum mengumpulkan
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </div>
</x-layout>