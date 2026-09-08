<x-layout title="Detail Mata Kuliah - {{ $course['name'] }}" active-nav="courses">

    {{-- BREADCRUMB / BACK --}}
    <a href="{{ route('courses.index') }}"
       class="inline-flex items-center gap-space-2xs font-label-lg text-label-lg text-on-surface-variant hover:text-primary transition-all mb-space-lg">
        <span class="material-symbols-outlined text-sm">arrow_back</span>
        <span>Kembali ke Daftar Mata Kuliah</span>
    </a>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-bento-gap-desktop items-start">

        {{-- KARTU DETAIL UTAMA --}}
        <div class="xl:col-span-8 bg-surface-container-low p-space-2xl rounded-2xl shadow-md relative overflow-hidden">
            <div class="absolute -right-10 -top-10 w-48 h-48 rounded-full bg-primary/5 blur-3xl pointer-events-none"></div>

            <div class="flex flex-wrap items-center gap-space-xs mb-space-md">
                <span class="px-2.5 py-1 rounded-md bg-tertiary-container/30 text-tertiary font-label-md text-label-md font-bold tracking-wide">
                    {{ $course['code'] }}
                </span>
                <span class="px-2 py-0.5 rounded-full bg-surface-container-high text-on-surface-variant font-label-sm text-label-sm">
                    Semester {{ $course['semester'] }}
                </span>
            </div>

            <h1 class="font-display-lg text-display-lg tracking-tight font-bold leading-tight">
                {{ $course['name'] }}
            </h1>

            {{-- Dosen --}}
            <div class="mt-space-xl p-space-md rounded-xl bg-surface-container flex items-center gap-space-sm">
                @php
                    $initials = collect(explode(' ', $course['lecturer']))->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->implode('');
                @endphp
                <div class="w-12 h-12 rounded-full bg-secondary-container text-on-secondary-container flex items-center justify-center font-bold text-headline-sm shrink-0">
                    {{ $initials }}
                </div>
                <div class="flex flex-col min-w-0 flex-1">
                    <span class="font-headline-sm text-headline-sm">{{ $course['lecturer'] }}</span>
                    <span class="font-body-sm text-body-sm text-outline">Dosen Pengampu Utama</span>
                </div>
                <span class="material-symbols-outlined text-primary">verified</span>
            </div>
        </div>

        {{-- PANEL INFO SAMPING --}}
        <div class="xl:col-span-4 flex flex-col gap-bento-gap-desktop">

            <div class="bg-surface-container-low p-space-lg rounded-2xl shadow-md">
                <div class="flex items-center gap-space-xs mb-space-md">
                    <div class="w-9 h-9 rounded-xl bg-surface-container flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined text-headline-sm">info</span>
                    </div>
                    <h3 class="font-headline-sm text-headline-sm">Informasi Mata Kuliah</h3>
                </div>

                <div class="flex flex-col gap-space-sm">
                    <div class="flex items-center justify-between py-space-xs border-b border-surface-container">
                        <span class="font-body-sm text-body-sm text-on-surface-variant">Kode MK</span>
                        <span class="font-label-lg text-label-lg text-primary">{{ $course['code'] }}</span>
                    </div>
                    <div class="flex items-center justify-between py-space-xs border-b border-surface-container">
                        <span class="font-body-sm text-body-sm text-on-surface-variant">Semester</span>
                        <span class="font-label-lg text-label-lg">{{ $course['semester'] }}</span>
                    </div>
                    <div class="flex items-center justify-between py-space-xs">
                        <span class="font-body-sm text-body-sm text-on-surface-variant">Dosen</span>
                        <span class="font-label-lg text-label-lg text-right max-w-[60%] truncate">{{ $course['lecturer'] }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-surface-container-low p-space-lg rounded-2xl shadow-md flex flex-col gap-space-sm">
                <div class="flex items-center gap-space-xs">
                    <div class="w-9 h-9 rounded-xl bg-secondary-container/40 text-secondary flex items-center justify-center">
                        <span class="material-symbols-outlined text-headline-sm">event_note</span>
                    </div>
                    <h3 class="font-headline-sm text-headline-sm">Aksi Cepat</h3>
                </div>
                <button class="w-full py-2.5 rounded-xl bg-primary text-on-primary font-label-lg text-label-lg hover:bg-primary-container transition-all flex items-center justify-center gap-1 shadow-[0_0_20px_rgba(173,210,134,0.2)]" type="button">
                    <span class="material-symbols-outlined text-sm">login</span>
                    <span>Masuk Kelas</span>
                </button>
                <button class="w-full py-2.5 rounded-xl bg-surface-container-high text-on-surface hover:bg-surface-container-highest font-label-lg text-label-lg transition-all flex items-center justify-center gap-1" type="button">
                    <span class="material-symbols-outlined text-sm">description</span>
                    <span>Unduh Silabus (RPS)</span>
                </button>
            </div>

        </div>
    </div>

</x-layout>