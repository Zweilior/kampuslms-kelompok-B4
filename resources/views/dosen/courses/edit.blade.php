<x-layout title="Edit Mata Kuliah - Dosen" active-nav="courses">
    <main class="dosen-course-edit">
        <a href="{{ route('dosen.courses.show', $course) }}" class="dosen-course-edit__back">
            <span class="material-symbols-outlined" aria-hidden="true">arrow_back</span>
            Kembali ke mata kuliah
        </a>

        <section class="dosen-course-edit__card">
            <header class="dosen-course-edit__header">
                <h1>Edit Mata Kuliah</h1>
                <p>Perbarui informasi mata kuliah yang Anda ampu. Dosen pengampu dan status hanya dapat diubah oleh admin.</p>
            </header>

            @if ($errors->any())
                <div class="dosen-course-edit__errors" role="alert">
                    <strong>Periksa kembali isian berikut:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('dosen.courses.update', $course) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="dosen-course-edit__fields">
                    <div class="dosen-course-edit__field">
                        <label for="code">Kode Mata Kuliah</label>
                        <input id="code" name="code" type="text" value="{{ old('code', $course->code) }}" maxlength="20" required>
                        @error('code')
                            <p class="dosen-course-edit__error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="dosen-course-edit__field">
                        <label for="sks">Jumlah SKS</label>
                        <input id="sks" name="sks" type="number" value="{{ old('sks', $course->sks) }}" min="1" max="6" required>
                        @error('sks')
                            <p class="dosen-course-edit__error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="dosen-course-edit__field dosen-course-edit__field--full">
                        <label for="name">Nama Mata Kuliah</label>
                        <input id="name" name="name" type="text" value="{{ old('name', $course->name) }}" maxlength="255" required>
                        @error('name')
                            <p class="dosen-course-edit__error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="dosen-course-edit__field dosen-course-edit__field--full">
                        <label for="description">Deskripsi</label>
                        <textarea id="description" name="description" rows="4">{{ old('description', $course->description) }}</textarea>
                        <p class="dosen-course-edit__hint">Dosen pengampu dan status mata kuliah dikelola oleh admin.</p>
                        @error('description')
                            <p class="dosen-course-edit__error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <footer class="dosen-course-edit__actions">
                    <a href="{{ route('dosen.courses.show', $course) }}" class="dosen-course-edit__button">Batal</a>
                    <button type="submit" class="dosen-course-edit__button dosen-course-edit__button--primary">
                        <span class="material-symbols-outlined" aria-hidden="true">save</span>
                        Simpan Perubahan
                    </button>
                </footer>
            </form>
        </section>
    </main>
</x-layout>
