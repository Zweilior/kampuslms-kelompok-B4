<x-layout title="Manajemen Mata Kuliah">
    <div class="admin-courses">
        <header class="admin-courses__page-header">
            <div>
                <span class="admin-courses__eyebrow">Manajemen Mata Kuliah</span>
                <h1 class="admin-courses__page-title">Daftar Mata Kuliah</h1>
                <p class="admin-courses__page-subtitle">Kelola mata kuliah, dosen pengampu, dan status perkuliahan di sini.</p>
            </div>

            <a href="{{ route('admin.courses.create') }}" class="admin-courses__btn-add">
                <span class="material-symbols-outlined" aria-hidden="true">add</span>
                Tambah MK
            </a>
        </header>

        <div class="admin-courses__card">
            <div class="admin-courses__toolbar">
                <h2 class="admin-courses__title">
                    <span class="material-symbols-outlined admin-courses__header-icon" aria-hidden="true">menu_book</span>
                    Daftar Mata Kuliah
                </h2>

                <form method="GET" action="{{ route('admin.courses.index') }}" class="admin-courses__filter-form" data-live-search data-live-target="#courses-table-body" data-live-count=".admin-courses__count" data-live-pagination=".admin-courses__pagination">
                    <label class="admin-courses__status-filter" for="course-status">
                        <span class="sr-only">Filter berdasarkan status mata kuliah</span>
                        <select id="course-status" name="status">
                            <option value="">Semua Status</option>
                            <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                            <option value="active" @selected(request('status') === 'active')>Aktif</option>
                            <option value="archived" @selected(request('status') === 'archived')>Arsip</option>
                        </select>
                    </label>

                    <label class="admin-courses__search" for="course-search">
                        <span class="material-symbols-outlined" aria-hidden="true">search</span>
                        <input
                            type="search"
                            id="course-search"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Cari kode atau nama MK"
                            aria-label="Cari kode atau nama MK"
                        >
                    </label>

                    <span class="admin-courses__count">
                        {{ number_format($courses->total()) }} mata kuliah
                    </span>
                    <span class="admin-courses__search-feedback" data-live-feedback role="status" aria-live="polite"></span>
                </form>
            </div>

            {{-- Tabel --}}
            <div class="admin-courses__table-wrap">
                <table class="admin-courses__table">
                    <thead>
                        <tr>
                            <th>KODE</th>
                            <th>NAMA MK</th>
                            <th>SKS</th>
                            <th>DOSEN</th>
                            <th>AKSI</th>
                        </tr>
                    </thead>
                    <tbody id="courses-table-body">
                        @forelse ($courses ?? [
                            (object)['id' => 1, 'code' => 'IF-2023', 'name' => 'Pemrograman Web', 'sks' => 3, 'dosen' => 'Budi Santoso'],
                            (object)['id' => 2, 'code' => 'IF-2024', 'name' => 'Basis Data', 'sks' => 3, 'dosen' => 'Siti Aminah'],
                            (object)['id' => 3, 'code' => 'IF-2025', 'name' => 'Rekayasa Perangkat Lunak', 'sks' => 3, 'dosen' => 'Tara Schuppe PhD'],
                        ] as $course)
                        <tr>
                            <td class="admin-courses__code">{{ $course->code }}</td>
                            <td class="admin-courses__name">{{ $course->name }}</td>
                            <td class="admin-courses__sks">{{ $course->sks }}</td>
                            <td class="admin-courses__dosen">{{ $course->lecturer?->name ?? 'Belum ditentukan' }}</td>
                            <td class="admin-courses__actions">
                                <a href="{{ route('admin.courses.show', $course->id) }}" class="admin-courses__btn admin-courses__btn--detail">
                                    <span class="material-symbols-outlined">visibility</span>
                                    Kelola
                                </a>
                                <button
                                    type="button"
                                    class="admin-courses__btn admin-courses__btn--edit"
                                    data-edit-course
                                    data-update-url="{{ route('admin.courses.update', $course->id) }}"
                                    data-course-id="{{ $course->id }}"
                                    data-course-code="{{ $course->code }}"
                                    data-course-name="{{ $course->name }}"
                                    data-course-description="{{ $course->description }}"
                                    data-course-sks="{{ $course->sks }}"
                                    data-course-lecturer="{{ $course->lecturer_id }}"
                                    data-course-status="{{ $course->status }}"
                                >
                                    <span class="material-symbols-outlined">edit</span>
                                    Edit
                                </button>
                                <button
                                    type="button"
                                    class="admin-courses__btn admin-courses__btn--danger"
                                    data-delete-course
                                    data-delete-url="{{ route('admin.courses.destroy', $course->id) }}"
                                    data-course-name="{{ $course->name }}"
                                    data-course-code="{{ $course->code }}"
                                >
                                    <span class="material-symbols-outlined">delete</span>
                                    Hapus
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="admin-courses__empty">Belum ada mata kuliah.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="admin-courses__pagination">
                {{ $courses->links('vendor.pagination.custom-pagination') }}
            </div>

        </div>
    </div>

    <div id="course-edit-modal" class="course-modal" data-auto-open="{{ $errors->any() && old('course_id') ? 'true' : 'false' }}" aria-hidden="true">
        <section class="course-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="course-edit-title">
            <header class="course-modal__header course-modal__header--edit">
                <div class="course-modal__heading">
                    <span class="material-symbols-outlined" aria-hidden="true">edit</span>
                    <div>
                        <span class="course-modal__eyebrow">Kelola data akademik</span>
                        <h2 id="course-edit-title">Edit Mata Kuliah</h2>
                    </div>
                </div>
                <button type="button" class="course-modal__close" data-close-course-modal aria-label="Tutup dialog">
                    <span class="material-symbols-outlined" aria-hidden="true">close</span>
                </button>
            </header>

            <form id="course-edit-form" method="POST" action="{{ old('course_id') ? route('admin.courses.update', old('course_id')) : '' }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="course_id" value="{{ old('course_id') }}">
                <div class="course-modal__body">
                    @if ($errors->any())
                        <div class="course-modal__alert" role="alert">
                            <span class="material-symbols-outlined" aria-hidden="true">error</span>
                            Periksa kembali data mata kuliah yang diisi.
                        </div>
                    @endif

                    <div class="course-modal__form-grid">
                        <div class="course-modal__field">
                            <label for="edit-course-code">Kode MK</label>
                            <input id="edit-course-code" name="code" type="text" maxlength="20" required value="{{ old('code') }}" placeholder="Contoh: SI241945">
                            @error('code')<span class="course-modal__error">{{ $message }}</span>@enderror
                        </div>
                        <div class="course-modal__field">
                            <label for="edit-course-name">Nama Mata Kuliah</label>
                            <input id="edit-course-name" name="name" type="text" required value="{{ old('name') }}" placeholder="Nama mata kuliah">
                            @error('name')<span class="course-modal__error">{{ $message }}</span>@enderror
                        </div>
                        <div class="course-modal__field course-modal__field--full">
                            <label for="edit-course-description">Deskripsi</label>
                            <textarea id="edit-course-description" name="description" rows="3" placeholder="Deskripsi mata kuliah (opsional)">{{ old('description') }}</textarea>
                            @error('description')<span class="course-modal__error">{{ $message }}</span>@enderror
                        </div>
                        <div class="course-modal__field">
                            <label for="edit-course-sks">SKS</label>
                            <input id="edit-course-sks" name="sks" type="number" min="1" max="6" required value="{{ old('sks') }}">
                            @error('sks')<span class="course-modal__error">{{ $message }}</span>@enderror
                        </div>
                        <div class="course-modal__field">
                            <label for="edit-course-lecturer">Dosen Pengampu</label>
                            <select id="edit-course-lecturer" name="lecturer_id" required>
                                <option value="">Pilih dosen</option>
                                @foreach ($lecturers as $lecturer)
                                    <option value="{{ $lecturer->id }}" @selected((string) old('lecturer_id') === (string) $lecturer->id)>
                                        {{ $lecturer->name }} ({{ $lecturer->nim_nip }})
                                    </option>
                                @endforeach
                            </select>
                            @error('lecturer_id')<span class="course-modal__error">{{ $message }}</span>@enderror
                        </div>
                        <div class="course-modal__field course-modal__field--full">
                            <label for="edit-course-status">Status</label>
                            <select id="edit-course-status" name="status" required>
                                <option value="draft" @selected(old('status') === 'draft')>Draft</option>
                                <option value="active" @selected(old('status') === 'active')>Aktif</option>
                                <option value="archived" @selected(old('status') === 'archived')>Arsip</option>
                            </select>
                            @error('status')<span class="course-modal__error">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>
                <footer class="course-modal__footer">
                    <button type="button" class="course-modal__button course-modal__button--secondary" data-close-course-modal>Batal</button>
                    <button type="submit" class="course-modal__button course-modal__button--edit">
                        <span class="material-symbols-outlined" aria-hidden="true">save</span>
                        Simpan Perubahan
                    </button>
                </footer>
            </form>
        </section>
    </div>

    <div id="course-delete-modal" class="course-modal" aria-hidden="true">
        <section class="course-modal__dialog course-modal__dialog--confirm" role="dialog" aria-modal="true" aria-labelledby="course-delete-title">
            <div class="course-modal__warning-icon" aria-hidden="true">
                <span class="material-symbols-outlined">warning</span>
            </div>
            <h2 id="course-delete-title">Hapus mata kuliah?</h2>
            <p class="course-modal__confirm-copy">
                Anda akan menghapus <strong id="delete-course-name"></strong>
                <span id="delete-course-code"></span>. Tindakan ini tidak dapat dibatalkan.
            </p>
            <form id="course-delete-form" method="POST">
                @csrf
                @method('DELETE')
                <footer class="course-modal__footer course-modal__footer--center">
                    <button type="button" class="course-modal__button course-modal__button--secondary" data-close-course-modal>Batal</button>
                    <button type="submit" class="course-modal__button course-modal__button--danger">
                        <span class="material-symbols-outlined" aria-hidden="true">delete</span>
                        Ya, hapus
                    </button>
                </footer>
            </form>
        </section>
    </div>

    <script>
        (() => {
            const editModal = document.getElementById('course-edit-modal');
            const deleteModal = document.getElementById('course-delete-modal');
            const editForm = document.getElementById('course-edit-form');
            const deleteForm = document.getElementById('course-delete-form');
            const modalTriggers = new Map();
            let activeModal = null;

            if (!editModal || !deleteModal || !editForm || !deleteForm) return;

            const openModal = (modal, trigger) => {
                modalTriggers.set(modal, trigger);
                document.body.append(modal);
                modal.classList.add('is-open');
                modal.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
                activeModal = modal;
                modal.querySelector('[data-close-course-modal]')?.focus();
            };

            const closeModal = (modal) => {
                modal.classList.remove('is-open');
                modal.setAttribute('aria-hidden', 'true');
                if (activeModal === modal) activeModal = null;
                if (!activeModal) document.body.style.overflow = '';
                modalTriggers.get(modal)?.focus();
            };

            document.addEventListener('click', (event) => {
                if (!(event.target instanceof Element)) return;

                const editButton = event.target.closest('[data-edit-course]');
                if (editButton) {
                    editForm.action = editButton.dataset.updateUrl;
                    editForm.elements.course_id.value = editButton.dataset.courseId;
                    document.getElementById('edit-course-code').value = editButton.dataset.courseCode;
                    document.getElementById('edit-course-name').value = editButton.dataset.courseName;
                    document.getElementById('edit-course-description').value = editButton.dataset.courseDescription ?? '';
                    document.getElementById('edit-course-sks').value = editButton.dataset.courseSks;
                    document.getElementById('edit-course-lecturer').value = editButton.dataset.courseLecturer;
                    document.getElementById('edit-course-status').value = editButton.dataset.courseStatus;
                    openModal(editModal, editButton);
                    return;
                }

                const deleteButton = event.target.closest('[data-delete-course]');
                if (deleteButton) {
                    deleteForm.action = deleteButton.dataset.deleteUrl;
                    document.getElementById('delete-course-name').textContent = deleteButton.dataset.courseName;
                    document.getElementById('delete-course-code').textContent = `(${deleteButton.dataset.courseCode})`;
                    openModal(deleteModal, deleteButton);
                }
            });

            document.querySelectorAll('[data-close-course-modal]').forEach((button) => {
                button.addEventListener('click', () => closeModal(button.closest('.course-modal')));
            });

            [editModal, deleteModal].forEach((modal) => {
                modal.addEventListener('click', (event) => {
                    if (event.target === modal) closeModal(modal);
                });
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && activeModal) closeModal(activeModal);
            });

            if (editModal.dataset.autoOpen === 'true') {
                openModal(editModal, null);
                document.getElementById('edit-course-code').focus();
            }
        })();
    </script>
</x-layout>