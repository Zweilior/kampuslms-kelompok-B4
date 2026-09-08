<<<<<<< HEAD
{{-- 
    Halaman daftar mata kuliah.
    View ini menerima data $courses dari CourseController@index.
--}}

<x-layout title="Daftar Mata Kuliah">
-- Active: 1758509644542@@127.0.0.1@3306@kampus_db
<x-layout title="Daftar Mata Kuliah" active-nav="courses">

    {{-- HERO HEADER --}}
    <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-y-space-sm mb-space-lg">
        <div class="flex flex-col">
            <div class="flex items-center gap-space-xs mb-space-2xs">
                <span class="px-space-xs py-0.5 rounded-full bg-secondary-container text-on-secondary-container font-label-sm text-label-sm tracking-wide uppercase">
                    Semester Aktif
                </span>
                <span class="w-1 h-1 rounded-full bg-outline"></span>
                <span class="font-label-sm text-label-sm text-on-surface-variant">{{ count($courses) }} Mata Kuliah Terdaftar</span>
            </div>
            <h1 class="font-headline-lg text-headline-lg tracking-tight">Daftar Mata Kuliah</h1>
            <p class="font-body-md text-body-md text-on-surface-variant mt-0.5">
                Kelola dan pantau seluruh mata kuliah aktif beserta dosen pengampu semester ini.
            </p>
        </div>

        <a href="#"
           class="flex items-center justify-center gap-space-2xs px-space-md py-2.5 rounded-xl bg-primary text-on-primary font-label-lg text-label-lg hover:bg-primary-container transition-all duration-200 shadow-[0_0_20px_rgba(173,210,134,0.3)]">
            <span class="material-symbols-outlined text-headline-sm">add_circle</span>
            <span>Tambah Mata Kuliah</span>
        </a>
    </div>

    {{-- Menampilkan judul halaman daftar mata kuliah. --}}
    <h1>Daftar Mata Kuliah</h1>

    {{-- 
        Melakukan perulangan untuk menampilkan setiap mata kuliah.
        Data berasal dari array statis yang dikirim oleh controller.
    --}}
    @foreach ($courses as $course)
        <div class="course">

            {{-- Menampilkan kode dan nama mata kuliah. --}}
            <h2>{{ $course['code'] }} - {{ $course['name'] }}</h2>

            {{-- Menampilkan dosen pengampu. --}}
            <p>Dosen: {{ $course['lecturer'] }}</p>

            {{-- Menampilkan semester mata kuliah. --}}
            <p>Semester: {{ $course['semester'] }}</p>

            {{-- 
                route() digunakan untuk membuat link menuju halaman detail.
                ID mata kuliah dikirim ke parameter {course}.
            --}}
            <a href="{{ route('courses.show', $course['id']) }}">
                Lihat Detail
            </a>
=======
<x-layout title="Daftar Mata Kuliah" active-nav="courses">
>>>>>>> main

    {{-- HERO HEADER --}}
    <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-y-space-sm mb-space-lg">
        <div class="flex flex-col">
            <div class="flex items-center gap-space-xs mb-space-2xs">
                <span class="px-space-xs py-0.5 rounded-full bg-secondary-container text-on-secondary-container font-label-sm text-label-sm tracking-wide uppercase">
                    Semester Aktif
                </span>
                <span class="w-1 h-1 rounded-full bg-outline"></span>
                <span class="font-label-sm text-label-sm text-on-surface-variant">{{ count($courses) }} Mata Kuliah Terdaftar</span>
            </div>
            <h1 class="font-headline-lg text-headline-lg tracking-tight">Daftar Mata Kuliah</h1>
            <p class="font-body-md text-body-md text-on-surface-variant mt-0.5">
                Kelola dan pantau seluruh mata kuliah aktif beserta dosen pengampu semester ini.
            </p>
        </div>

        <a href="#"
           class="flex items-center justify-center gap-space-2xs px-space-md py-2.5 rounded-xl bg-primary text-on-primary font-label-lg text-label-lg hover:bg-primary-container transition-all duration-200 shadow-[0_0_20px_rgba(173,210,134,0.3)]">
            <span class="material-symbols-outlined text-headline-sm">add_circle</span>
            <span>Tambah Mata Kuliah</span>
        </a>
    </div>

    {{-- METRIK RINGKASAN --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-bento-gap-desktop mb-space-lg">
        <div class="bg-surface-container-low p-space-lg rounded-2xl shadow-md relative overflow-hidden">
            <div class="absolute -right-6 -bottom-6 w-28 h-28 rounded-full bg-primary/5 blur-2xl pointer-events-none"></div>
            <div class="flex items-center gap-space-xs">
                <div class="w-9 h-9 rounded-xl bg-surface-container flex items-center justify-center text-primary">
                    <span class="material-symbols-outlined text-headline-sm">library_books</span>
                </div>
                <span class="font-label-lg text-label-lg text-on-surface-variant">Total Mata Kuliah</span>
            </div>
            <div class="mt-space-md flex items-baseline gap-space-xs">
                <span class="font-display-lg text-display-lg tracking-tight font-bold leading-none">{{ count($courses) }}</span>
                <span class="font-headline-sm text-headline-sm text-on-surface-variant font-medium">Kelas Aktif</span>
            </div>
        </div>

        <div class="bg-surface-container-low p-space-lg rounded-2xl shadow-md relative overflow-hidden">
            <div class="absolute -right-6 -bottom-6 w-28 h-28 rounded-full bg-secondary/5 blur-2xl pointer-events-none"></div>
            <div class="flex items-center gap-space-xs">
                <div class="w-9 h-9 rounded-xl bg-surface-container flex items-center justify-center text-secondary">
                    <span class="material-symbols-outlined text-headline-sm">school</span>
                </div>
                <span class="font-label-lg text-label-lg text-on-surface-variant">Semester</span>
            </div>
            <div class="mt-space-md flex items-baseline gap-space-xs">
                <span class="font-display-lg text-display-lg tracking-tight font-bold leading-none">{{ $courses[0]['semester'] ?? '-' }}</span>
                <span class="font-headline-sm text-headline-sm text-on-surface-variant font-medium">Sedang Berjalan</span>
            </div>
        </div>

        <div class="bg-surface-container-low p-space-lg rounded-2xl shadow-md relative overflow-hidden">
            <div class="absolute -right-6 -bottom-6 w-28 h-28 rounded-full bg-primary/10 blur-2xl pointer-events-none"></div>
            <div class="flex items-center gap-space-xs">
                <div class="w-9 h-9 rounded-xl bg-surface-container flex items-center justify-center text-primary">
                    <span class="material-symbols-outlined text-headline-sm">how_to_reg</span>
                </div>
                <span class="font-label-lg text-label-lg text-on-surface-variant">Status KRS</span>
            </div>
            <div class="mt-space-md flex items-baseline gap-space-xs">
                <span class="font-display-lg text-display-lg text-primary tracking-tight font-bold leading-none">Disetujui</span>
                <span class="font-headline-sm text-headline-sm text-on-surface-variant font-medium">Dosen Wali</span>
            </div>
        </div>
    </div>

    {{-- TOOLBAR PENCARIAN --}}
    <div class="bg-surface-container-low p-space-sm rounded-2xl flex items-center gap-space-sm shadow-sm mb-bento-gap-desktop">
        <div class="flex items-center gap-space-xs bg-surface-container px-space-md py-2 rounded-xl flex-1 max-w-xl">
            <span class="material-symbols-outlined text-outline text-headline-sm">search</span>
            <input id="courseSearchInput" class="bg-transparent border-none outline-none font-body-md text-body-md text-on-surface placeholder-outline flex-1 focus:ring-0"
                   placeholder="Cari kode atau nama mata kuliah..." type="text"/>
            <span class="font-label-sm text-label-sm text-outline-variant bg-surface-container-highest px-1.5 py-0.5 rounded">ESC</span>
        </div>
        <div class="flex items-center gap-space-xs ml-auto">
            <span class="font-label-sm text-label-sm text-on-surface-variant">Ditemukan:</span>
            <span id="courseCount" class="font-label-lg text-label-lg text-primary">{{ count($courses) }}</span>
        </div>
    </div>

    {{-- GRID MATA KULIAH --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-bento-gap-desktop" id="courseGrid">
        @foreach ($courses as $course)
            <div class="course-card bg-surface-container-low hover:bg-surface-container p-space-lg rounded-2xl shadow-md transition-all duration-300 flex flex-col justify-between group relative overflow-hidden"
                 data-code="{{ strtolower($course['code']) }}"
                 data-name="{{ strtolower($course['name']) }}">

                <div class="flex flex-col">
                    <div class="flex items-center justify-between mb-space-sm">
                        <span class="px-2.5 py-1 rounded-md bg-tertiary-container/30 text-tertiary font-label-md text-label-md font-bold tracking-wide">
                            {{ $course['code'] }}
                        </span>
                        <span class="px-2 py-0.5 rounded-full bg-surface-container-high text-on-surface-variant font-label-sm text-label-sm">
                            Semester {{ $course['semester'] }}
                        </span>
                    </div>

                    <h2 class="font-headline-sm text-headline-sm group-hover:text-primary transition-colors line-clamp-2">
                        {{ $course['name'] }}
                    </h2>

                    {{-- Dosen Pengampu --}}
                    <div class="mt-space-md p-space-xs rounded-xl bg-surface-container flex items-center gap-space-xs">
                        @php
                            $initials = collect(explode(' ', $course['lecturer']))->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->implode('');
                        @endphp
                        <div class="w-8 h-8 rounded-full bg-secondary-container text-on-secondary-container flex items-center justify-center font-label-md text-label-md font-bold shrink-0">
                            {{ $initials }}
                        </div>
                        <div class="flex flex-col min-w-0 flex-1">
                            <span class="font-label-md text-label-md truncate">{{ $course['lecturer'] }}</span>
                            <span class="font-label-sm text-label-sm text-outline">Dosen Pengampu</span>
                        </div>
                        <span class="material-symbols-outlined text-sm text-primary shrink-0">verified</span>
                    </div>
                </div>

                {{-- Footer Aksi --}}
                <div class="mt-space-md pt-space-sm flex items-center gap-space-xs">
                    <a href="{{ route('courses.show', $course['id']) }}"
                       class="flex-1 py-2 rounded-xl bg-surface-container-high hover:bg-primary hover:text-on-primary text-on-surface font-label-md text-label-md text-center transition-all flex items-center justify-center gap-1">
                        <span>Lihat Detail</span>
                        <span class="material-symbols-outlined text-sm">arrow_forward</span>
                    </a>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Pesan jika tidak ada hasil pencarian --}}
    <div id="noResult" class="hidden py-space-2xl text-center">
        <span class="material-symbols-outlined text-headline-lg text-outline">search_off</span>
        <p class="font-body-md text-body-md text-on-surface-variant mt-space-sm">Tidak ada mata kuliah yang cocok dengan pencarian.</p>
    </div>

    {{-- Script pencarian --}}
    <script>
        (function () {
            const searchInput = document.getElementById('courseSearchInput');
            const cards = document.querySelectorAll('.course-card');
            const countEl = document.getElementById('courseCount');
            const noResult = document.getElementById('noResult');

            function applySearch() {
                const q = (searchInput.value || '').toLowerCase().trim();
                let visible = 0;

                cards.forEach(card => {
                    const name = card.getAttribute('data-name') || '';
                    const code = card.getAttribute('data-code') || '';
                    const match = !q || name.includes(q) || code.includes(q);

                    card.style.display = match ? 'flex' : 'none';
                    if (match) visible++;
                });

                countEl.textContent = visible;
                noResult.classList.toggle('hidden', visible > 0);
            }

            searchInput.addEventListener('input', applySearch);
            searchInput.addEventListener('keydown', e => {
                if (e.key === 'Escape') {
                    searchInput.value = '';
                    applySearch();
                }
            });
        })();
    </script>

</x-layout>