<x-layout title="Detail Mata Kuliah - Dosen" active-nav="courses">
    <div class="max-w-7xl mx-auto px-4">
        @php
            $focus = request('focus') === 'assignments' ? 'assignments' : 'materials';
        @endphp
        <header class="dosen-course-detail__header mb-space-lg">
            <a href="{{ route('dosen.courses.index', ['focus' => $focus]) }}" class="mb-space-md inline-flex items-center gap-space-xs font-label-md text-on-surface-variant hover:text-primary">
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

        <section aria-label="Pengelolaan mata kuliah" class="grid grid-cols-1 md:grid-cols-2 gap-bento-gap-desktop">
            <a href="{{ route('dosen.courses.materials.index', ['course' => $course, 'focus' => 'materials']) }}" class="group rounded-2xl border border-outline/10 bg-surface-container-low p-space-lg shadow-sm transition-colors hover:bg-surface-container">
                <span class="mb-space-md flex h-11 w-11 items-center justify-center rounded-xl bg-primary/10 text-primary"><span class="material-symbols-outlined">description</span></span>
                <h2 class="font-headline-sm text-headline-sm text-on-surface group-hover:text-primary">Materi</h2>
                <p class="mt-space-xs font-body-sm text-body-sm text-on-surface-variant">Tambah, ubah, dan hapus materi kelas ini.</p>
                <span class="mt-space-md inline-flex items-center gap-1 font-label-md text-label-md text-primary">Kelola materi <span class="material-symbols-outlined text-base">arrow_forward</span></span>
            </a>

            <a href="{{ route('dosen.courses.assignments.index', ['course' => $course, 'focus' => 'assignments']) }}" class="group rounded-2xl border border-outline/10 bg-surface-container-low p-space-lg shadow-sm transition-colors hover:bg-surface-container">
                <span class="mb-space-md flex h-11 w-11 items-center justify-center rounded-xl bg-secondary-container text-on-secondary-container"><span class="material-symbols-outlined">assignment</span></span>
                <h2 class="font-headline-sm text-headline-sm text-on-surface group-hover:text-primary">Tugas dan Nilai</h2>
                <p class="mt-space-xs font-body-sm text-body-sm text-on-surface-variant">Kelola tugas, pantau pengumpulan, dan beri nilai setelah tenggat.</p>
                <span class="mt-space-md inline-flex items-center gap-1 font-label-md text-label-md text-primary">Kelola tugas dan nilai <span class="material-symbols-outlined text-base">arrow_forward</span></span>
            </a>
        </section>
    </div>
</x-layout>