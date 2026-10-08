<x-layout title="Edit Nilai Tugas - Dosen" active-nav="courses">
    <div class="dosen-grades">
        <a href="{{ route('dosen.courses.assignments.index', $course) }}" class="dosen-assignment-form__back">
            <span class="material-symbols-outlined" aria-hidden="true">arrow_back</span>
            Kembali ke daftar tugas
        </a>

        <header class="dosen-grades__header">
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
                <span>{{ $submissions->count() }} pengumpulan</span>
            </div>
        </header>

        @if (session('success'))
            <div class="dosen-grades__notice dosen-grades__notice--success" role="status">
                <span class="material-symbols-outlined" aria-hidden="true">check_circle</span>{{ session('success') }}
            </div>
        @endif

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

        @if ($assignment->due_at->gt(now()))
            <div class="dosen-grades__notice dosen-grades__notice--info" role="alert">
                <span class="material-symbols-outlined" aria-hidden="true">schedule</span>
                Penilaian baru dapat dilakukan setelah tenggat tugas selesai.
            </div>
        @else
            <section class="dosen-grades__list" aria-label="Daftar pengumpulan mahasiswa">
                @forelse ($submissions as $submission)
                    <article class="dosen-grade-card">
                        <header class="dosen-grade-card__student">
                            <span class="dosen-grade-card__avatar" aria-hidden="true">
                                {{ strtoupper(substr($submission->student->name, 0, 1)) }}
                            </span>
                            <div class="dosen-grade-card__student-info">
                                <h2>{{ $submission->student->name }}</h2>
                                <p>{{ $submission->student->nim_nip }}</p>
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
                                <a href="{{ \Illuminate\Support\Facades\Storage::url($submission->file_path) }}" target="_blank" rel="noopener" class="dosen-grade-card__file">
                                    <span class="material-symbols-outlined" aria-hidden="true">description</span>
                                    <span>{{ $submission->original_name }}</span>
                                    <span class="material-symbols-outlined dosen-grade-card__open" aria-hidden="true">open_in_new</span>
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
                                                  placeholder="Berikan catatan untuk mahasiswa...">{{ old('feedback', $submission->grade?->feedback) }}</textarea>
                                    </div>
                                </div>
                                <button type="submit" class="dosen-assignment-form__button dosen-assignment-form__button--primary">
                                    <span class="material-symbols-outlined" aria-hidden="true">{{ $submission->grade ? 'save' : 'grading' }}</span>
                                    {{ $submission->grade ? 'Simpan Perubahan' : 'Beri Nilai' }}
                                </button>
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
        @endif
    </div>
</x-layout>
