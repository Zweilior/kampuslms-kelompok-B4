<x-layout :title="'Kelola Mata Kuliah - ' . $course->name">
    <main id="admin-course-detail" class="admin-course-detail">
        <header class="admin-course-detail__hero">
            <div>
                <span class="admin-course-detail__eyebrow">Kelola Mata Kuliah</span>
                <h1 class="admin-course-detail__title">{{ $course->name }} <span>({{ $course->code }})</span></h1>
                <p class="admin-course-detail__subtitle">Dosen: {{ $course->lecturer?->name ?? 'Belum ditentukan' }}</p>
            </div>
            <a href="{{ route('admin.courses.index') }}" class="admin-course-detail__back">
                <span class="material-symbols-outlined" aria-hidden="true">arrow_back</span>
                Kembali
            </a>
        </header>

        {{-- Section Enrollment --}}
        <section class="admin-course-detail__panel" aria-labelledby="course-students-title">
            <div class="admin-course-detail__panel-heading">
                <div>
                    <h2 id="course-students-title">
                        <span class="material-symbols-outlined" aria-hidden="true">groups</span>
                        Enrollment Mahasiswa
                    </h2>
                    <p>Daftar peserta mata kuliah ini, diurutkan berdasarkan nama.</p>
                </div>
                <span class="admin-course-detail__count">{{ $students->count() }} mahasiswa</span>
            </div>

            @if (session('success'))
                <div class="admin-course-detail__notice admin-course-detail__notice--success" role="status">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="admin-course-detail__notice admin-course-detail__notice--error" role="alert">
                    {{ $errors->first() }}
                </div>
            @endif

            @if ($availableStudents->isNotEmpty())
                <form action="{{ route('admin.courses.enrollments.store', $course) }}" method="POST" class="admin-course-detail__enroll-form">
                    @csrf
                    <div>
                        <label for="student_id">Tambah mahasiswa</label>
                        <select id="student_id" name="student_id" required>
                            <option value="">Pilih mahasiswa yang belum terdaftar</option>
                            @foreach ($availableStudents as $student)
                                <option value="{{ $student->id }}" @selected((string) old('student_id') === (string) $student->id)>
                                    {{ $student->name }} · {{ $student->nim_nip }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="admin-course-detail__button admin-course-detail__button--primary">
                        <span class="material-symbols-outlined" aria-hidden="true">person_add</span>
                        Enroll Mahasiswa
                    </button>
                </form>
            @else
                <p class="admin-course-detail__empty-note">Semua mahasiswa sudah terdaftar atau belum ada akun mahasiswa.</p>
            @endif

            @if ($students->isNotEmpty())
                <div class="admin-course-detail__table-wrap">
                    <table class="admin-course-detail__table">
                        <thead>
                            <tr>
                                <th scope="col">No.</th>
                                <th scope="col">Nama</th>
                                <th scope="col">NIM</th>
                                <th scope="col">Email</th>
                                <th scope="col"><span class="sr-only">Aksi</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($students as $student)
                                <tr>
                                    <td class="admin-course-detail__number">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</td>
                                    <td class="admin-course-detail__student-name">{{ $student->name }}</td>
                                    <td>{{ $student->nim_nip ?? '—' }}</td>
                                    <td class="admin-course-detail__email">{{ $student->email }}</td>
                                    <td class="admin-course-detail__actions">
                                        <button type="button"
                                                class="admin-course-detail__button admin-course-detail__button--remove"
                                                data-remove-url="{{ route('admin.courses.enrollments.destroy', [$course, $student]) }}"
                                                data-student-name="{{ $student->name }}">
                                            <span class="material-symbols-outlined" aria-hidden="true">person_remove</span>
                                            Keluarkan
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="admin-course-detail__empty">Belum ada mahasiswa terdaftar di mata kuliah ini.</p>
            @endif
        </section>

        {{-- Section Materi --}}
        <section class="admin-course-detail__panel" aria-labelledby="course-materials-title">
            <div class="admin-course-detail__panel-heading">
                <div>
                    <h2 id="course-materials-title">
                        <span class="material-symbols-outlined" aria-hidden="true">library_books</span>
                        Materi
                    </h2>
                    <p>{{ $course->materials->count() }} materi · diurutkan berdasarkan judul</p>
                </div>
                <a href="{{ route('admin.materials.create', ['course_id' => $course->id]) }}" class="admin-course-detail__button admin-course-detail__button--primary">
                    <span class="material-symbols-outlined" aria-hidden="true">add</span>
                    Tambah Materi
                </a>
            </div>
            @if ($course->materials->isNotEmpty())
                <div class="admin-course-detail__table-wrap">
                    <table class="admin-course-detail__table">
                        <thead>
                            <tr>
                                <th scope="col">No.</th>
                                <th scope="col">Judul Materi</th>
                                <th scope="col">Jenis</th>
                                <th scope="col">Tanggal Ditambahkan</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($course->materials as $material)
                                <tr>
                                    <td class="admin-course-detail__number">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</td>
                                    <td class="admin-course-detail__student-name">{{ $material->title }}</td>
                                    <td><span class="admin-course-detail__badge">{{ $material->type === 'link' ? 'Link' : 'File' }}</span></td>
                                    <td>{{ $material->created_at?->format('d M Y') ?? '—' }}</td>
                                    <td class="admin-course-detail__actions">
                                        <a href="{{ route('admin.materials.edit', $material) }}" class="admin-course-detail__button admin-course-detail__button--edit">
                                            <span class="material-symbols-outlined" aria-hidden="true">edit</span>
                                            Edit
                                        </a>
                                        <form action="{{ route('admin.materials.destroy', $material) }}" method="POST" onsubmit="return confirm('Hapus materi ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="admin-course-detail__button admin-course-detail__button--remove">
                                                <span class="material-symbols-outlined" aria-hidden="true">delete</span>
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="admin-course-detail__empty">Belum ada materi untuk mata kuliah ini.</p>
            @endif
        </section>

        {{-- Section Tugas --}}
        <section class="admin-course-detail__panel" aria-labelledby="course-assignments-title">
            <div class="admin-course-detail__panel-heading">
                <div>
                    <h2 id="course-assignments-title">
                        <span class="material-symbols-outlined" aria-hidden="true">assignment</span>
                        Tugas
                    </h2>
                    <p>{{ $course->assignments->count() }} tugas · diurutkan berdasarkan judul</p>
                </div>
                <a href="{{ route('admin.assignments.create', ['course_id' => $course->id]) }}" class="admin-course-detail__button admin-course-detail__button--primary">
                    <span class="material-symbols-outlined" aria-hidden="true">add</span>
                    Buat Tugas
                </a>
            </div>
            @if ($course->assignments->isNotEmpty())
                <div class="admin-course-detail__table-wrap">
                    <table class="admin-course-detail__table">
                        <thead>
                            <tr>
                                <th scope="col">No.</th>
                                <th scope="col">Judul Tugas</th>
                                <th scope="col">Deadline</th>
                                <th scope="col">Status</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($course->assignments as $assignment)
                                <tr>
                                    <td class="admin-course-detail__number">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</td>
                                    <td class="admin-course-detail__student-name">{{ $assignment->title }}</td>
                                    <td>{{ $assignment->due_at?->format('d M Y, H:i') ?? '—' }}</td>
                                    <td><span class="admin-course-detail__badge">{{ ucfirst($assignment->status) }}</span></td>
                                    <td class="admin-course-detail__actions">
                                        <a href="{{ route('admin.assignments.edit', $assignment) }}" class="admin-course-detail__button admin-course-detail__button--edit">
                                            <span class="material-symbols-outlined" aria-hidden="true">edit</span>
                                            Edit
                                        </a>
                                        @if ($assignment->submissions_count === 0)
                                            <form action="{{ route('admin.assignments.destroy', $assignment) }}" method="POST" onsubmit="return confirm('Hapus tugas ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="admin-course-detail__button admin-course-detail__button--remove">
                                                    <span class="material-symbols-outlined" aria-hidden="true">delete</span>
                                                    Hapus
                                                </button>
                                            </form>
                                        @else
                                            <span class="admin-course-detail__locked" title="Tugas memiliki submission">Tidak dapat dihapus</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="admin-course-detail__empty">Belum ada tugas untuk mata kuliah ini.</p>
            @endif
        </section>

        {{-- Section Nilai --}}
        <section class="admin-course-detail__panel" aria-labelledby="course-grades-title">
            <div class="admin-course-detail__panel-heading">
                <div>
                    <h2 id="course-grades-title">
                        <span class="material-symbols-outlined" aria-hidden="true">grade</span>
                        Rekap Nilai
                    </h2>
                    <p>Nilai akhir mahasiswa yang telah tersedia.</p>
                </div>
            </div>
            <div class="admin-course-detail__table-wrap">
                <table class="admin-course-detail__table">
                    <thead>
                        <tr>
                            <th scope="col">No.</th>
                            <th scope="col">Nama</th>
                            <th scope="col">NIM</th>
                            <th scope="col">Nilai</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($students as $student)
                            @php($finalGrade = $finalGrades->get($student->id))
                            <tr>
                                <td class="admin-course-detail__number">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</td>
                                <td class="admin-course-detail__student-name">{{ $student->name }}</td>
                                <td>{{ $student->nim_nip ?? '—' }}</td>
                                <td class="admin-course-detail__grade">{{ $finalGrade?->letter_grade ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="admin-course-detail__empty">Belum ada mahasiswa terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <div id="remove-student-dialog" class="logout-dialog-backdrop admin-course-detail__dialog" hidden role="presentation">
        <section class="logout-dialog logout-dialog--confirmation" role="dialog" aria-modal="true" aria-labelledby="remove-course-student-title">
            <h2 class="logout-dialog__title" id="remove-course-student-title">Keluarkan mahasiswa?</h2>
            <p class="logout-dialog__message">
                <strong id="remove-student-name"></strong> tidak lagi dapat mengakses mata kuliah ini. Apakah Anda yakin ingin mengeluarkannya?
            </p>
            <div class="logout-dialog__actions">
                <button type="button" id="cancel-remove-student" class="logout-dialog__button">Batal</button>
                <form id="remove-student-form" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="logout-dialog__button logout-dialog__button--confirm">Ya, keluarkan</button>
                </form>
            </div>
        </section>
    </div>

    <script>
        (() => {
            const dialog = document.getElementById('remove-student-dialog');
            const studentName = document.getElementById('remove-student-name');
            const removeForm = document.getElementById('remove-student-form');
            const cancelButton = document.getElementById('cancel-remove-student');
            let triggerButton = null;

            if (!dialog || !studentName || !removeForm || !cancelButton) {
                return;
            }

            const closeDialog = () => {
                dialog.hidden = true;
                triggerButton?.focus();
            };

            document.querySelectorAll('[data-remove-url]').forEach((button) => {
                button.addEventListener('click', () => {
                    triggerButton = button;
                    studentName.textContent = button.dataset.studentName ?? '';
                    removeForm.action = button.dataset.removeUrl ?? '';
                    dialog.hidden = false;
                    cancelButton.focus();
                });
            });

            cancelButton.addEventListener('click', closeDialog);
            dialog.addEventListener('click', (event) => {
                if (event.target === dialog) closeDialog();
            });
            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && !dialog.hidden) closeDialog();
            });
        })();
    </script>
</x-layout>