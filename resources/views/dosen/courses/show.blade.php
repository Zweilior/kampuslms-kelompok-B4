<x-layout title="Detail Mata Kuliah - Dosen" active-nav="courses">
    <div class="max-w-7xl mx-auto px-4">
        @php
            $focus = request('focus') === 'assignments' ? 'assignments' : 'materials';
        @endphp
        <header class="mb-space-lg">
            <a href="{{ route('dosen.courses.index', ['focus' => $focus]) }}" class="mb-space-md inline-flex items-center gap-space-xs font-label-md text-on-surface-variant hover:text-primary">
                <span class="material-symbols-outlined text-base">arrow_back</span>Mata Kuliah Diampu
            </a>
            <div class="rounded-2xl border border-outline/10 bg-surface-container-low p-space-lg shadow-sm">
                <div class="flex flex-wrap items-start justify-between gap-space-md">
                    <div>
                        <span class="inline-flex rounded-md bg-tertiary-container/30 px-2.5 py-1 font-label-md text-label-md font-bold text-tertiary">{{ $course->code }}</span>
                        <h1 class="mt-space-sm font-headline-lg text-headline-lg text-on-surface">{{ $course->name }}</h1>
                        <p class="mt-space-xs font-body-md text-body-md text-on-surface-variant">{{ $course->sks }} SKS · Kelas yang Anda ampu</p>
                    </div>
                    <span class="rounded-full bg-primary/10 px-3 py-1 font-label-sm text-label-sm text-primary">Ruang lingkup: kelas ini</span>
                </div>
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