@extends('components.layout')

@section('content')
    <div class="mahasiswa-page mahasiswa-page--submission-create">
        @php
            $isLate = now()->gt($assignment->due_at);
            $existingSubmission = $assignment->submissions()->where('user_id', auth()->id())->first();
        @endphp

        <a href="{{ route('mahasiswa.courses.assignments.index', $course->id) }}" class="mahasiswa-page__submission-back">
            <span class="material-symbols-outlined">arrow_back</span>
            Kembali ke daftar tugas
        </a>

        {{-- Notifikasi Di Atas Informasi Tugas Yang Dikumpulkan --}}
        <div class="w-full max-w-[880px] mx-auto space-y-3 mb-4">
            {{-- Peringatan Lewat Tenggat --}}
            @if ($isLate)
                @if ($assignment->allow_late)
                    <div class="flex items-start gap-3 rounded-2xl border border-amber-500/30 bg-amber-500/10 p-4 text-amber-300 shadow-sm" role="alert">
                        <span class="material-symbols-outlined text-2xl text-amber-400 shrink-0 mt-0.5" aria-hidden="true">warning</span>
                        <div class="text-sm">
                            <strong class="font-semibold block text-amber-200">Perhatian: Tugas telah melewati tenggat waktu ({{ $assignment->due_at->format('d M Y, H:i') }}).</strong>
                            <p class="text-amber-300/90 mt-0.5">Tugas masih menerima pengumpulan, namun berkas Anda akan ditandai terlambat.</p>
                        </div>
                    </div>
                @else
                    <div class="flex items-start gap-3 rounded-2xl border border-error/30 bg-error/10 p-4 text-error shadow-sm" role="alert">
                        <span class="material-symbols-outlined text-2xl text-error shrink-0 mt-0.5" aria-hidden="true">error</span>
                        <div class="text-sm">
                            <strong class="font-semibold block text-red-200">Batas waktu pengumpulan telah berakhir.</strong>
                            <p class="text-red-300/90 mt-0.5">Tugas ini tidak mengizinkan pengumpulan setelah tenggat waktu ({{ $assignment->due_at->format('d M Y, H:i') }}).</p>
                        </div>
                    </div>
                @endif
            @endif

            {{-- Pesan Error Tenggat dari Backend --}}
            @error('assignment')
                <div class="flex items-start gap-3 rounded-2xl border border-error/30 bg-error/10 p-4 text-error shadow-sm" role="alert">
                    <span class="material-symbols-outlined text-2xl text-error shrink-0 mt-0.5" aria-hidden="true">error</span>
                    <span class="text-sm">{{ $message }}</span>
                </div>
            @enderror

            {{-- Info Kumpul Ulang --}}
            @if ($existingSubmission)
                <div class="flex items-start gap-3.5 rounded-2xl border border-primary/30 bg-primary/10 p-4 text-on-surface shadow-sm" role="status">
                    <span class="material-symbols-outlined text-2xl text-primary shrink-0 mt-0.5" aria-hidden="true">info</span>
                    <div class="text-sm leading-relaxed">
                        <strong class="font-semibold block text-primary text-base mb-1">Pengumpulan Ulang</strong>
                        <p class="text-on-surface-variant">
                            Anda telah mengumpulkan berkas: <strong class="text-on-surface font-semibold">{{ $existingSubmission->original_name }}</strong>
                            @if ($existingSubmission->fileSizeForHumans)
                                <span class="text-outline text-xs">({{ $existingSubmission->fileSizeForHumans }})</span>
                            @endif
                            pada <span class="text-on-surface">{{ $existingSubmission->submitted_at?->format('d M Y, H:i') }}</span>.
                            Mengunggah berkas baru akan menggantikan berkas lama Anda.
                        </p>
                    </div>
                </div>
            @endif
        </div>

        <section class="mahasiswa-page__card mahasiswa-page__submission-context mahasiswa-page__submission-context--hero" aria-label="Informasi tugas">
            <span class="mahasiswa-page__icon-box">
                <span class="material-symbols-outlined">assignment</span>
            </span>
            <div class="mahasiswa-page__submission-context-copy">
                <span class="mahasiswa-page__submission-context-label">Tugas yang dikumpulkan</span>
                <h2>{{ $assignment->title }}</h2>
                <p>{{ $assignment->instructions }}</p>
                <div class="mahasiswa-page__submission-meta">
                    <span>
                        <span class="material-symbols-outlined">menu_book</span>
                        {{ $course->name }}
                    </span>
                    <span>
                        <span class="material-symbols-outlined">schedule</span>
                        Tenggat {{ $assignment->due_at->format('d M Y, H:i') }}
                    </span>
                    <span>
                        <span class="material-symbols-outlined">grade</span>
                        Maks. nilai {{ $assignment->max_score }}
                    </span>
                </div>
            </div>
        </section>

        <section class="mahasiswa-page__card mahasiswa-page__form-card">
            <div class="mahasiswa-page__form-card-heading">
                <div>
                    <h2 class="mahasiswa-page__form-heading">Detail jawaban</h2>
                    <p class="mahasiswa-page__form-intro">Unggah berkas tugas Anda sebelum tenggat waktu berakhir.</p>
                </div>
            </div>

            <form action="{{ route('mahasiswa.courses.assignments.submissions.store', [$course->id, $assignment->id]) }}"
                method="POST" enctype="multipart/form-data" class="mahasiswa-page__form">
                @csrf

                <div class="mahasiswa-page__field">
                    <label for="file">Berkas Tugas <span aria-hidden="true">*</span></label>
                    <input id="file" type="file" name="file" accept=".pdf,.doc,.docx" required
                        @disabled($isLate && ! $assignment->allow_late)
                        @class(['is-invalid' => $errors->has('file')])
                        class="w-full rounded-xl border border-outline/10 bg-surface-container px-space-md py-3 text-on-surface file:mr-3 file:rounded-lg file:border-0 file:bg-primary file:px-3 file:py-2 file:text-on-primary">
                    <span class="mahasiswa-page__field-hint">Format yang diizinkan: PDF, DOC, atau DOCX. Ukuran maksimal 5 MB. Pastikan tautan dapat dibuka oleh dosen.</span>
                    @error('file')
                        <span class="mahasiswa-page__field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mahasiswa-page__field">
                    <label for="note">Catatan <span>(opsional)</span></label>
                    <textarea id="note" name="note" rows="4"
                        @disabled($isLate && ! $assignment->allow_late)
                        placeholder="Tambahkan pesan atau catatan jika ada...">{{ old('note', $existingSubmission?->note) }}</textarea>
                    @error('note')
                        <span class="mahasiswa-page__field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mahasiswa-page__form-actions">
                    <button type="submit"
                        @disabled($isLate && ! $assignment->allow_late)
                        class="mahasiswa-page__button mahasiswa-page__button--primary {{ ($isLate && ! $assignment->allow_late) ? 'opacity-50 cursor-not-allowed' : '' }}">
                        Kumpulkan sekarang
                        <span class="material-symbols-outlined">arrow_forward</span>
                    </button>
                    <a href="{{ route('mahasiswa.courses.assignments.index', $course->id) }}"
                        class="mahasiswa-page__button">
                        Batal
                    </a>
                </div>
            </form>
        </section>
    </div>
@endsection
