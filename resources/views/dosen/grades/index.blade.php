<x-layout title="Monitoring Nilai - Dosen" active-nav="grades">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <header class="mb-space-lg">
            <div class="flex items-center gap-space-xs mb-space-2xs">
                <span class="px-space-xs py-0.5 rounded-full bg-secondary-container text-on-secondary-container font-label-sm text-label-sm uppercase">Akademik</span>
                <span class="w-1 h-1 rounded-full bg-outline"></span>
                <span class="font-label-sm text-label-sm text-on-surface-variant">Kelas yang diampu</span>
            </div>
            <h1 class="font-headline-lg text-headline-lg text-on-surface">Monitoring Nilai</h1>
            <p class="font-body-md text-body-md text-on-surface-variant mt-0.5">Penilaian dan rekap nilai dibatasi pada mata kuliah yang Anda ampu.</p>
        </header>

        <section class="rounded-2xl border border-outline/10 bg-surface-container-low p-space-lg shadow-sm" aria-label="Pilih mata kuliah">
            <div class="flex items-start gap-space-md">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-tertiary-container/30 text-tertiary">
                    <span class="material-symbols-outlined">grading</span>
                </span>
                <div>
                    <h2 class="font-headline-sm text-headline-sm text-on-surface">Pilih mata kuliah</h2>
                    <p class="mt-space-xs font-body-sm text-body-sm text-on-surface-variant">Buka daftar kelas untuk mengelola submission dan nilai pada kelas yang diampu.</p>
                    <a href="{{ route('dosen.courses.index') }}" class="mt-space-md inline-flex items-center gap-space-xs rounded-xl bg-primary px-space-md py-2.5 font-label-md text-on-primary hover:bg-primary-container">
                        Lihat mata kuliah <span class="material-symbols-outlined text-base">arrow_forward</span>
                    </a>
                </div>
            </div>
        </section>
    </div>
</x-layout>