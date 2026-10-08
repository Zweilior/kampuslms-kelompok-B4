<x-layout title="Edit Materi - Dosen" active-nav="courses">
    <main class="dosen-material-form">
        <a href="{{ route('dosen.courses.materials.index', $course) }}" class="dosen-assignment-form__back">
            <span class="material-symbols-outlined" aria-hidden="true">arrow_back</span>
            Kembali ke daftar materi
        </a>

        <header class="dosen-assignment-form__header">
            <span class="dosen-assignment-form__icon material-symbols-outlined" aria-hidden="true">edit_document</span>
            <div>
                <div class="dosen-assignment-form__eyebrow">
                    <span>{{ $course->code }}</span>
                    <span class="dosen-assignment-form__dot" aria-hidden="true"></span>
                    <span>{{ $course->name }}</span>
                </div>
                <h1 class="dosen-assignment-form__title">Edit Materi</h1>
                <p class="dosen-assignment-form__description">Perbarui informasi atau sumber materi mata kuliah.</p>
            </div>
        </header>

        @if ($errors->any())
            <div class="dosen-assignment-form__errors" role="alert">
                <span class="material-symbols-outlined" aria-hidden="true">error</span>
                <div>
                    <p class="dosen-assignment-form__errors-title">Materi belum berhasil diperbarui.</p>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form action="{{ route('dosen.courses.materials.update', [$course, $material]) }}" method="POST" enctype="multipart/form-data" class="dosen-assignment-form__card">
            @csrf
            @method('PUT')
            <div class="dosen-assignment-form__fields">
                <div class="dosen-assignment-form__field dosen-assignment-form__field--full">
                    <label for="material-title">Judul Materi <span aria-hidden="true">*</span></label>
                    <input id="material-title" type="text" name="title" value="{{ old('title', $material->title) }}" maxlength="255" autocomplete="off" required>
                    @error('title')
                        <p class="dosen-assignment-form__field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="dosen-assignment-form__field dosen-assignment-form__field--full">
                    <label for="description">Deskripsi</label>
                    <textarea id="description" name="description" rows="4" placeholder="Jelaskan isi atau kegunaan materi ini...">{{ old('description', $material->description) }}</textarea>
                    @error('description')
                        <p class="dosen-assignment-form__field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="dosen-assignment-form__field dosen-assignment-form__field--full" x-data="{ type: @js(old('type', $material->type)) }">
                    <label for="type">Tipe Materi <span aria-hidden="true">*</span></label>
                    <div class="dosen-assignment-form__select-wrap">
                        <select id="type" name="type" x-model="type" required>
                            <option value="file">File</option>
                            <option value="link">Tautan eksternal</option>
                        </select>
                        <span class="material-symbols-outlined" aria-hidden="true">expand_more</span>
                    </div>
                    @error('type')
                        <p class="dosen-assignment-form__field-error">{{ $message }}</p>
                    @enderror

                    <div class="dosen-material-form__conditional" x-cloak x-show="type === 'link'" x-transition.opacity>
                        <label for="external_url">URL Eksternal <span aria-hidden="true">*</span></label>
                        <input id="external_url" type="url" name="external_url" value="{{ old('external_url', $material->external_url) }}" placeholder="https://..." :required="type === 'link'">
                        @error('external_url')
                            <p class="dosen-assignment-form__field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="dosen-material-form__conditional" x-cloak x-show="type === 'file'" x-transition.opacity>
                        @if ($material->original_name)
                            <div class="dosen-material-form__current-file">
                                <span class="material-symbols-outlined" aria-hidden="true">description</span>
                                <span><small>File saat ini</small><strong>{{ $material->original_name }}</strong></span>
                            </div>
                        @endif
                        <label for="material-file">Ganti File</label>
                        <input id="material-file" class="dosen-material-form__file-input" type="file" name="file" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip,.png,.jpg,.jpeg">
                        <p class="dosen-assignment-form__hint">Kosongkan jika tidak ingin mengganti file. Ukuran maksimal 20 MB.</p>
                        @error('file')
                            <p class="dosen-assignment-form__field-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <footer class="dosen-assignment-form__footer">
                <a href="{{ route('dosen.courses.materials.index', $course) }}" class="dosen-assignment-form__button dosen-assignment-form__button--secondary">Batal</a>
                <button type="submit" class="dosen-assignment-form__button dosen-assignment-form__button--primary">
                    <span class="material-symbols-outlined" aria-hidden="true">save</span>Simpan Perubahan
                </button>
            </footer>
        </form>
    </main>
</x-layout>
