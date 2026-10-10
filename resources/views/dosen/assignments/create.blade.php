<x-layout title="Buat Tugas - Dosen" active-nav="courses">
    <div class="dosen-assignment-form">
        <a href="{{ route('dosen.courses.assignments.index', $course) }}" class="dosen-assignment-form__back">
            <span class="material-symbols-outlined" aria-hidden="true">arrow_back</span>
            Kembali ke daftar tugas
        </a>

        <header class="dosen-assignment-form__header">
            <span class="dosen-assignment-form__icon material-symbols-outlined" aria-hidden="true">assignment_add</span>
            <div>
                <div class="dosen-assignment-form__eyebrow">
                    <span>{{ $course->code }}</span>
                    <span class="dosen-assignment-form__dot" aria-hidden="true"></span>
                    <span>{{ $course->name }}</span>
                </div>
                <h1 class="dosen-assignment-form__title">Buat Tugas Baru</h1>
                <p class="dosen-assignment-form__description">Atur instruksi, tenggat, dan nilai untuk tugas kelas ini.</p>
            </div>
        </header>

        @if ($errors->any())
            <div class="dosen-assignment-form__errors" role="alert">
                <span class="material-symbols-outlined" aria-hidden="true">error</span>
                <div>
                    <p class="dosen-assignment-form__errors-title">Tugas belum berhasil dibuat.</p>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form action="{{ route('dosen.courses.assignments.store', $course) }}" method="POST" class="dosen-assignment-form__card">
            @csrf

            <div class="dosen-assignment-form__fields">
                <div class="dosen-assignment-form__field dosen-assignment-form__field--full">
                    <label for="title">Judul Tugas <span aria-hidden="true">*</span></label>
                    <input id="title" type="text" name="title" value="{{ old('title') }}" maxlength="255" autocomplete="off" required>
                    @error('title')
                        <p class="dosen-assignment-form__field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="dosen-assignment-form__field">
                    <label for="due_at">Tenggat Pengumpulan <span aria-hidden="true">*</span></label>
                    <input id="due_at" type="datetime-local" name="due_at" value="{{ old('due_at') }}" required>
                    <p class="dosen-assignment-form__hint">Atur tanggal dan waktu terakhir mahasiswa dapat mengumpulkan.</p>
                    @error('due_at')
                        <p class="dosen-assignment-form__field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="dosen-assignment-form__field">
                    <label for="max_score">Nilai Maksimal <span aria-hidden="true">*</span></label>
                    <div class="dosen-assignment-form__number-wrap">
                        <input id="max_score" type="number" name="max_score" value="{{ old('max_score', 100) }}" min="0" max="255" required>
                        <span>poin</span>
                    </div>
                    @error('max_score')
                        <p class="dosen-assignment-form__field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="dosen-assignment-form__field dosen-assignment-form__field--full">
                    <label for="grade_component_id">Rubrik Penilaian</label>
                    <div class="dosen-assignment-form__select-wrap">
                        <select id="grade_component_id" name="grade_component_id">
                            <option value="">Tanpa rubrik</option>
                            @foreach ($gradeComponents as $component)
                                <option value="{{ $component->id }}" {{ (string) old('grade_component_id') === (string) $component->id ? 'selected' : '' }}>
                                    {{ $component->name }} ({{ number_format((float) $component->weight, 2) }}%)
                                </option>
                            @endforeach
                        </select>
                        <span class="material-symbols-outlined" aria-hidden="true">expand_more</span>
                    </div>
                    @if ($gradeComponents->isEmpty())
                        <p class="dosen-assignment-form__hint">
                            Belum ada rubrik untuk mata kuliah ini.
                            <a href="{{ route('dosen.courses.grade-components.index', $course) }}">Buat rubrik terlebih dahulu</a>.
                        </p>
                    @else
                        <p class="dosen-assignment-form__hint">Pilih komponen rubrik untuk mengelompokkan nilai tugas ini.</p>
                    @endif
                    @error('grade_component_id')
                        <p class="dosen-assignment-form__field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="dosen-assignment-form__field dosen-assignment-form__field--full">
                    <label for="instructions">Instruksi</label>
                    <textarea id="instructions" name="instructions" rows="5" placeholder="Tuliskan petunjuk atau kriteria pengerjaan tugas...">{{ old('instructions') }}</textarea>
                    @error('instructions')
                        <p class="dosen-assignment-form__field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="dosen-assignment-form__field dosen-assignment-form__field--full">
                    <label for="allow_late">Pengumpulan Terlambat</label>
                    <div class="dosen-assignment-form__select-wrap">
                        <select id="allow_late" name="allow_late" required>
                            <option value="1" {{ old('allow_late', '1') == '1' ? 'selected' : '' }}>Diizinkan</option>
                            <option value="0" {{ old('allow_late') === '0' ? 'selected' : '' }}>Tidak diizinkan</option>
                        </select>
                        <span class="material-symbols-outlined" aria-hidden="true">expand_more</span>
                    </div>
                    @error('allow_late')
                        <p class="dosen-assignment-form__field-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <input type="hidden" name="status" value="published">

            <footer class="dosen-assignment-form__footer">
                <a href="{{ route('dosen.courses.assignments.index', $course) }}" class="dosen-assignment-form__button dosen-assignment-form__button--secondary">
                    Batal
                </a>
                <button type="submit" class="dosen-assignment-form__button dosen-assignment-form__button--primary">
                    <span class="material-symbols-outlined" aria-hidden="true">add_task</span>
                    Buat Tugas
                </button>
            </footer>
        </form>
    </div>
</x-layout>
