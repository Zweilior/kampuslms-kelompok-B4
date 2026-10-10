<x-layout title="Dashboard Dosen - KampusLMS" active-nav="dashboard">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <header class="dosen-dashboard__header">
            <div class="dosen-dashboard__header-copy">
                <h1 class="dosen-dashboard__title">Halo, {{ auth()->user()->name }}</h1>
                <p class="dosen-dashboard__description">Aktivitas dan penilaian untuk mata kuliah yang Anda ampu.</p>
            </div>
            <a href="{{ route('dosen.courses.index') }}" class="dosen-dashboard__action">
                <span class="material-symbols-outlined" aria-hidden="true">menu_book</span>
                Mata kuliah saya
            </a>
        </header>

        <section aria-label="Ringkasan aktivitas dosen" class="grid grid-cols-1 md:grid-cols-3 gap-bento-gap-desktop">
            <a href="{{ route('dosen.courses.index') }}" class="group rounded-2xl border border-outline/10 bg-surface-container-low p-space-lg shadow-sm transition-colors hover:bg-surface-container">
                <span class="mb-space-md flex h-11 w-11 items-center justify-center rounded-xl bg-primary/10 text-primary">
                    <span class="material-symbols-outlined">menu_book</span>
                </span>
                <h2 class="font-headline-sm text-headline-sm text-on-surface group-hover:text-primary">Mata Kuliah</h2>
                <p class="mt-space-xs font-body-sm text-body-sm text-on-surface-variant">Jumlah mata kuliah yang Anda ampu.</p>
                <span class="mt-space-md block font-headline-lg text-headline-lg text-primary">{{ $totalCourses }}</span>
                <span class="mt-space-sm inline-flex items-center gap-1 font-label-md text-label-md text-primary">Lihat mata kuliah <span class="material-symbols-outlined text-base">arrow_forward</span></span>
            </a>

            <a href="{{ route('dosen.courses.index', ['focus' => 'assignments']) }}" class="group rounded-2xl border border-outline/10 bg-surface-container-low p-space-lg shadow-sm transition-colors hover:bg-surface-container">
                <span class="mb-space-md flex h-11 w-11 items-center justify-center rounded-xl bg-secondary-container text-on-secondary-container">
                    <span class="material-symbols-outlined">assignment</span>
                </span>
                <h2 class="font-headline-sm text-headline-sm text-on-surface group-hover:text-primary">Tugas Dibuka</h2>
                <p class="mt-space-xs font-body-sm text-body-sm text-on-surface-variant">Tugas terbit yang masih dalam masa pengumpulan.</p>
                <span class="mt-space-md block font-headline-lg text-headline-lg text-primary">{{ $openAssignments }}</span>
                <span class="mt-space-sm inline-flex items-center gap-1 font-label-md text-label-md text-primary">Lihat tugas <span class="material-symbols-outlined text-base">arrow_forward</span></span>
            </a>

            <a href="{{ route('dosen.grades.index') }}" class="group rounded-2xl border border-outline/10 bg-surface-container-low p-space-lg shadow-sm transition-colors hover:bg-surface-container">
                <span class="mb-space-md flex h-11 w-11 items-center justify-center rounded-xl bg-tertiary-container/30 text-tertiary">
                    <span class="material-symbols-outlined">grading</span>
                </span>
                <h2 class="font-headline-sm text-headline-sm text-on-surface group-hover:text-primary">Nilai</h2>
                <p class="mt-space-xs font-body-sm text-body-sm text-on-surface-variant">Pengumpulan tugas yang belum dinilai.</p>
                <span class="mt-space-md block font-headline-lg text-headline-lg text-primary">{{ $ungradedSubmissions }}</span>
                <span class="mt-space-sm inline-flex items-center gap-1 font-label-md text-label-md text-primary">Buka penilaian <span class="material-symbols-outlined text-base">arrow_forward</span></span>
            </a>
        </section>
    </div>
</x-layout>