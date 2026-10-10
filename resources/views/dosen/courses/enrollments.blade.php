<x-layout title="Enrolment Mahasiswa - Dosen" active-nav="courses">
    <main class="dosen-enrollments"
          x-data="{ removeOpen: false, removeAction: '', removeName: '' }"
          @keydown.escape.window="removeOpen = false">
        <a href="{{ route('dosen.courses.show', $course) }}" class="dosen-enrollments__back">
            <span class="material-symbols-outlined" aria-hidden="true">arrow_back</span>
            Kembali ke mata kuliah
        </a>

        <header class="dosen-enrollments__header">
            <div class="dosen-enrollments__eyebrow">
                <span>{{ $course->code }}</span>
                <span aria-hidden="true"></span>
                <span>ENROLLMENT</span>
            </div>
            <h1>Mahasiswa Terdaftar</h1>
            <p>Kelola peserta untuk mata kuliah {{ $course->name }}.</p>
        </header>

        @if (session('success'))
            <div class="dosen-enrollments__notice" role="status">
                <span class="material-symbols-outlined" aria-hidden="true">check_circle</span>
                {{ session('success') }}
            </div>
        @endif

        <section class="dosen-enrollments__panel" aria-labelledby="enroll-student-title">
            <div class="dosen-enrollments__panel-heading">
                <div>
                    <h2 id="enroll-student-title">Daftarkan Mahasiswa</h2>
                    <p>Pilih akun mahasiswa yang belum terdaftar pada kelas ini.</p>
                </div>
            </div>
            @if ($availableStudents->isNotEmpty())
                <form action="{{ route('dosen.courses.enrollments.store', $course) }}" method="POST" class="dosen-enrollments__form">
                    @csrf
                    <div class="dosen-enrollments__select-group">
                        <label for="student_id">Mahasiswa</label>
                        <select id="student_id" name="student_id" required>
                            <option value="">Pilih mahasiswa</option>
                            @foreach ($availableStudents as $student)
                                <option value="{{ $student->id }}" @selected((string) old('student_id') === (string) $student->id)>
                                    {{ $student->name }} · {{ $student->nim_nip }}
                                </option>
                            @endforeach
                        </select>
                        @error('student_id')
                            <p class="dosen-enrollments__error">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" class="dosen-course-edit__button dosen-course-edit__button--primary">
                        <span class="material-symbols-outlined" aria-hidden="true">person_add</span>
                        Daftarkan
                    </button>
                </form>
            @else
                <p class="dosen-enrollments__empty-option">Semua mahasiswa sudah terdaftar atau belum ada akun mahasiswa.</p>
            @endif
        </section>

        <section class="dosen-enrollments__panel" aria-labelledby="enrolled-students-title">
            <div class="dosen-enrollments__panel-heading">
                <div>
                    <h2 id="enrolled-students-title">Daftar Peserta</h2>
                    <p>Mahasiswa yang dapat mengakses materi, tugas, dan nilai kelas.</p>
                </div>
                <span class="dosen-enrollments__count">{{ $students->count() }} mahasiswa</span>
            </div>

            @if ($students->isNotEmpty())
                <div class="dosen-enrollments__list">
                    @foreach ($students as $student)
                        <article class="dosen-enrollments__student">
                            <span class="dosen-enrollments__avatar material-symbols-outlined" aria-hidden="true">person</span>
                            <div class="dosen-enrollments__identity">
                                <strong>{{ $student->name }}</strong>
                                <span>{{ $student->nim_nip }} · {{ $student->email }}</span>
                            </div>
                            <form action="{{ route('dosen.courses.enrollments.destroy', [$course, $student]) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="button"
                                        class="dosen-enrollments__remove"
                                        data-remove-url="{{ route('dosen.courses.enrollments.destroy', [$course, $student]) }}"
                                        data-student-name="{{ $student->name }}"
                                        @click="removeAction = $el.dataset.removeUrl; removeName = $el.dataset.studentName; removeOpen = true">
                                    <span class="material-symbols-outlined" aria-hidden="true">person_remove</span>
                                    Keluarkan
                                </button>
                            </form>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="dosen-enrollments__empty">
                    <span class="material-symbols-outlined" aria-hidden="true">group_off</span>
                    <p>Belum ada mahasiswa terdaftar di mata kuliah ini.</p>
                </div>
            @endif
        </section>

        <template x-teleport="body">
            <div class="logout-dialog-backdrop"
                 x-cloak
                 x-show="removeOpen"
                 x-transition.opacity
                 @click.self="removeOpen = false"
                 role="presentation">
                <section class="logout-dialog" role="dialog" aria-modal="true" aria-labelledby="remove-student-title" @click.stop>
                    <div class="logout-dialog__icon" aria-hidden="true">
                        <span class="material-symbols-outlined">person_remove</span>
                    </div>
                    <h2 class="logout-dialog__title" id="remove-student-title">Keluarkan mahasiswa?</h2>
                    <p class="logout-dialog__message"><strong x-text="removeName"></strong> tidak lagi bisa mengakses mata kuliah ini. Riwayat tugas dan nilai tetap tersimpan.</p>
                    <div class="logout-dialog__actions">
                        <button type="button" class="logout-dialog__button" @click="removeOpen = false">Batal</button>
                        <form method="POST" :action="removeAction">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="logout-dialog__button logout-dialog__button--confirm">Ya, keluarkan</button>
                        </form>
                    </div>
                </section>
            </div>
        </template>
    </main>
</x-layout>
