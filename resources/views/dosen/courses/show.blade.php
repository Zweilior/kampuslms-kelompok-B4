<x-layout title="Detail Mata Kuliah - Dosen" active-nav="courses">
    <main class="dosen-course-detail-page">
        @php
            $focus = request('focus') === 'assignments' ? 'assignments' : 'materials';
        @endphp
        <header class="dosen-course-detail__header">
            <a href="{{ route('dosen.courses.index', ['focus' => $focus]) }}" class="dosen-course-detail__back">
                <span class="material-symbols-outlined text-base">arrow_back</span>Mata Kuliah Diampu
            </a>
            <div class="dosen-course-hero">
                <span class="dosen-course-hero__icon material-symbols-outlined" aria-hidden="true">menu_book</span>
                <div class="dosen-course-hero__content">
                    <span class="dosen-course-hero__eyebrow">SEMESTER BERJALAN · {{ $course->code }}</span>
                    <h1 class="dosen-course-hero__title">{{ $course->name }}</h1>
                    <p class="dosen-course-hero__description">{{ $course->sks }} SKS · Kelas yang Anda ampu</p>
                    <div class="dosen-course-hero__actions">
                        <a href="{{ route('dosen.courses.edit', $course) }}" class="dosen-course-hero__action">
                            <span class="material-symbols-outlined" aria-hidden="true">edit</span>
                            Edit Mata Kuliah
                        </a>
                        <a href="{{ route('dosen.courses.enrollments.index', $course) }}" class="dosen-course-hero__action">
                            <span class="material-symbols-outlined" aria-hidden="true">group_add</span>
                            Enrollment
                        </a>
                        <a href="{{ route('dosen.courses.grade-components.index', $course) }}" class="dosen-course-hero__action">
                            <span class="material-symbols-outlined" aria-hidden="true">edit_note</span>
                            Edit Rubrik
                        </a>
                    </div>
                </div>
                <span class="dosen-course-hero__badge">Aktif</span>
            </div>
        </header>

        <section aria-label="Pengelolaan mata kuliah" class="dosen-course-management-grid">
            <a href="{{ route('dosen.courses.materials.index', ['course' => $course, 'focus' => 'materials']) }}" class="dosen-course-management-card group">
                <span class="dosen-course-management-card__icon dosen-course-management-card__icon--materials">
                    <span class="material-symbols-outlined" aria-hidden="true">description</span>
                </span>
                <h2>Materi</h2>
                <p>Tambah, ubah, dan hapus materi kelas ini.</p>
                <span class="dosen-course-management-card__link">Kelola materi <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span></span>
            </a>

            <a href="{{ route('dosen.courses.assignments.index', ['course' => $course, 'focus' => 'assignments']) }}" class="dosen-course-management-card group">
                <span class="dosen-course-management-card__icon dosen-course-management-card__icon--assignments">
                    <span class="material-symbols-outlined" aria-hidden="true">assignment</span>
                </span>
                <h2>Tugas dan Nilai</h2>
                <p>Kelola tugas, pantau pengumpulan, dan beri nilai setelah tenggat.</p>
                <span class="dosen-course-management-card__link">Kelola tugas dan nilai <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span></span>
            </a>
        </section>
    </main>
</x-layout>