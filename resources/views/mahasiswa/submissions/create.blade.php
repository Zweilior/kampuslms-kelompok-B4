@extends('components.layout')

@section('content')
    <div class="mahasiswa-page mahasiswa-page--submission-create">
        <header class="mahasiswa-page__header">
            <div class="mahasiswa-page__header-copy">
                <a href="{{ route('mahasiswa.courses.assignments.index', $course->id) }}" class="mahasiswa-page__back">
                    <span class="material-symbols-outlined">arrow_back</span>
                    Kembali ke daftar tugas
                </a>
                <span class="mahasiswa-page__eyebrow">Pengumpulan tugas</span>
                <h1 class="mahasiswa-page__title">Kirim jawaban</h1>
                <p class="mahasiswa-page__description">{{ $assignment->title }} · {{ $course->name }}</p>
            </div>
            <span class="mahasiswa-page__icon-box"><span class="material-symbols-outlined">upload_file</span></span>
        </header>

        <section class="mahasiswa-page__card mahasiswa-page__form-card">
            <h2 class="mahasiswa-page__form-heading">Detail jawaban</h2>
            <p class="mahasiswa-page__form-intro">Isi informasi berkas yang dikumpulkan untuk tugas ini.</p>

            <form action="{{ route('courses.assignments.submissions.store', [$course->id, $assignment->id]) }}"
                method="POST" class="mahasiswa-page__form">
                @csrf
                <div class="mahasiswa-page__field">
                    <label for="original_name">Nama file / judul dokumen</label>
                    <input id="original_name" type="text" name="original_name" value="{{ old('original_name') }}"
                        placeholder="Contoh: Tugas1_NIM.pdf" required>
                    @error('original_name')
                        <span class="mahasiswa-page__field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mahasiswa-page__field">
                    <label for="file_path">Link file / storage path</label>
                    <input id="file_path" type="text" name="file_path" value="{{ old('file_path') }}"
                        placeholder="Tautan Drive, GitHub, atau path file" required>
                    @error('file_path')
                        <span class="mahasiswa-page__field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mahasiswa-page__field">
                    <label for="note">Catatan untuk dosen <span>(opsional)</span></label>
                    <textarea id="note" name="note" rows="4" placeholder="Tambahkan pesan atau catatan jika ada...">{{ old('note') }}</textarea>
                    @error('note')
                        <span class="mahasiswa-page__field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mahasiswa-page__form-actions">
                    <button type="submit" class="mahasiswa-page__button mahasiswa-page__button--primary">
                        Kumpulkan sekarang
                        <span class="material-symbols-outlined">send</span>
                    </button>
                    <a href="{{ route('mahasiswa.courses.assignments.index', $course->id) }}"
                        class="mahasiswa-page__button">Batal</a>
                </div>
            </form>
        </section>
    </div>
@endsection
