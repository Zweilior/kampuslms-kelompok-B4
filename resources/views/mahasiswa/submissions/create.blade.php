@extends('components.layout')

@section('content')
    <div class="mahasiswa-page mahasiswa-page--submission-create">
        <header class="mahasiswa-page__header">
            <div class="mahasiswa-page__header-copy">
                <a href="{{ route('mahasiswa.courses.assignments.index', $course->id) }}" class="mahasiswa-page__back">
                    <span class="material-symbols-outlined">arrow_back</span>
                    Kembali ke daftar tugas
                </a>
            </div>
            <span class="mahasiswa-page__icon-box"><span class="material-symbols-outlined">upload_file</span></span>
        </header>

        <section class="mahasiswa-page__card mahasiswa-page__submission-context" aria-label="Informasi tugas">
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
                    <p class="mahasiswa-page__form-intro">Lengkapi informasi pengumpulan sebelum mengirim ke dosen.</p>
                </div>
            </div>

            <form action="{{ route('mahasiswa.courses.assignments.submissions.store', [$course->id, $assignment->id]) }}"
                method="POST" class="mahasiswa-page__form">
                @csrf
                <div class="mahasiswa-page__field">
                    <label for="original_name">Nama file / judul dokumen <span aria-hidden="true">*</span></label>
                    <input id="original_name" type="text" name="original_name" value="{{ old('original_name') }}"
                        placeholder="Contoh: Tugas1_NIM.pdf" required @class(['is-invalid' => $errors->has('original_name')])>
                    @error('original_name')
                        <span class="mahasiswa-page__field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mahasiswa-page__field">
                    <label for="file_path">Link file <span aria-hidden="true">*</span></label>
                    <input id="file_path" type="text" name="file_path" value="{{ old('file_path') }}"
                        placeholder="Tempel tautan Drive, GitHub, atau path file" required @class(['is-invalid' => $errors->has('file_path')])>
                    <span class="mahasiswa-page__field-hint">Pastikan tautan dapat dibuka oleh dosen.</span>
                    @error('file_path')
                        <span class="mahasiswa-page__field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mahasiswa-page__field">
                    <label for="note">Catatan <span>(opsional)</span></label>
                    <textarea id="note" name="note" rows="4" placeholder="Tambahkan pesan atau catatan jika ada...">{{ old('note') }}</textarea>
                    @error('note')
                        <span class="mahasiswa-page__field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mahasiswa-page__form-actions">
                    <button type="submit" class="mahasiswa-page__button mahasiswa-page__button--primary">
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
