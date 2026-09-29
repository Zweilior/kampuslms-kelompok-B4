<x-layout title="Dashboard Dosen - KampusLMS" active-nav="dashboard">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <header class="mb-space-lg">
            <div class="flex items-center gap-space-xs mb-space-2xs">
                <span class="px-space-xs py-0.5 rounded-full bg-secondary-container text-on-secondary-container font-label-sm text-label-sm uppercase">Ruang dosen</span>
                <span class="w-1 h-1 rounded-full bg-outline"></span>
                <span class="font-label-sm text-label-sm text-on-surface-variant">Pengelolaan akademik</span>
            </div>
            <h1 class="font-headline-lg text-headline-lg text-on-surface">Dashboard Dosen</h1>
            <p class="font-body-md text-body-md text-on-surface-variant mt-0.5">Aktivitas dan penilaian untuk mata kuliah yang Anda ampu.</p>
        </header>

        <section aria-label="Akses dosen" class="grid grid-cols-1 md:grid-cols-3 gap-bento-gap-desktop">
            <a href="{{ route('dosen.courses.index', ['focus' => 'materials']) }}" class="group rounded-2xl border border-outline/10 bg-surface-container-low p-space-lg shadow-sm transition-colors hover:bg-surface-container">
                <span class="mb-space-md flex h-11 w-11 items-center justify-center rounded-xl bg-primary/10 text-primary">
                    <span class="material-symbols-outlined">description</span>
                </span>
                <h2 class="font-headline-sm text-headline-sm text-on-surface group-hover:text-primary">Materi</h2>
                <p class="mt-space-xs font-body-sm text-body-sm text-on-surface-variant">Pilih mata kuliah yang Anda ampu untuk mengelola materi.</p>
                <span class="mt-space-md inline-flex items-center gap-1 font-label-md text-label-md text-primary">Pilih mata kuliah <span class="material-symbols-outlined text-base">arrow_forward</span></span>
            </a>

            <a href="{{ route('dosen.courses.index', ['focus' => 'assignments']) }}" class="group rounded-2xl border border-outline/10 bg-surface-container-low p-space-lg shadow-sm transition-colors hover:bg-surface-container">
                <span class="mb-space-md flex h-11 w-11 items-center justify-center rounded-xl bg-secondary-container text-on-secondary-container">
                    <span class="material-symbols-outlined">assignment</span>
                </span>
                <h2 class="font-headline-sm text-headline-sm text-on-surface group-hover:text-primary">Tugas</h2>
                <p class="mt-space-xs font-body-sm text-body-sm text-on-surface-variant">Pilih mata kuliah yang Anda ampu untuk mengelola tugas.</p>
                <span class="mt-space-md inline-flex items-center gap-1 font-label-md text-label-md text-primary">Pilih mata kuliah <span class="material-symbols-outlined text-base">arrow_forward</span></span>
            </a>

            <a href="{{ route('dosen.grades.index') }}" class="group rounded-2xl border border-outline/10 bg-surface-container-low p-space-lg shadow-sm transition-colors hover:bg-surface-container">
                <span class="mb-space-md flex h-11 w-11 items-center justify-center rounded-xl bg-tertiary-container/30 text-tertiary">
                    <span class="material-symbols-outlined">grading</span>
                </span>
                <h2 class="font-headline-sm text-headline-sm text-on-surface group-hover:text-primary">Nilai</h2>
                <p class="mt-space-xs font-body-sm text-body-sm text-on-surface-variant">Penilaian submission pada kelas yang diampu.</p>
                <span class="mt-space-md inline-flex items-center gap-1 font-label-md text-label-md text-primary">Buka penilaian <span class="material-symbols-outlined text-base">arrow_forward</span></span>
            </a>
        </section>
    </div>
</x-layout>