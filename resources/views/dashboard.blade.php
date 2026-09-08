<x-layout title="Dashboard - Akademia LMS" active-nav="dashboard">

    {{-- HERO HEADER --}}
    <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-y-space-sm mb-space-lg">
        <div class="flex flex-col">
            <div class="flex items-center gap-space-xs mb-space-2xs">
                <span class="px-space-xs py-0.5 rounded-full bg-secondary-container text-on-secondary-container font-label-sm text-label-sm tracking-wide uppercase">
                    Selamat Datang
                </span>
                <span class="w-1 h-1 rounded-full bg-outline"></span>
                <span class="font-label-sm text-label-sm text-on-surface-variant">Semester Ganjil 2026/2027</span>
            </div>
            <h1 class="font-headline-lg text-headline-lg tracking-tight">Dashboard</h1>
            <p class="font-body-md text-body-md text-on-surface-variant mt-0.5">
                Ringkasan aktivitas dan statistik akademik semester ini.
            </p>
        </div>

        <div class="flex items-center gap-space-sm">
            <span class="flex items-center gap-space-2xs px-space-md py-2 rounded-xl bg-surface-container-high text-on-surface font-label-md text-label-md">
                <span class="material-symbols-outlined text-sm">event</span>
                <span>{{ date('d F Y') }}</span>
            </span>
        </div>
    </div>

    {{-- STATISTIK KARTU --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-bento-gap-desktop mb-space-lg">
        
        {{-- Kartu 1: Total Mata Kuliah --}}
        <div class="bg-surface-container-low p-space-lg rounded-2xl shadow-md relative overflow-hidden transition-all hover:bg-surface-container">
            <div class="absolute -right-6 -bottom-6 w-28 h-28 rounded-full bg-primary/5 blur-2xl pointer-events-none"></div>
            <div class="flex items-center gap-space-xs">
                <div class="w-9 h-9 rounded-xl bg-primary/20 flex items-center justify-center text-primary">
                    <span class="material-symbols-outlined text-headline-sm">menu_book</span>
                </div>
                <span class="font-label-lg text-label-lg text-on-surface-variant">Mata Kuliah</span>
            </div>
            <div class="mt-space-md flex items-baseline gap-space-xs">
                <span class="font-display-lg text-display-lg tracking-tight font-bold leading-none">4</span>
                <span class="font-headline-sm text-headline-sm text-on-surface-variant font-medium">Total</span>
            </div>
            <div class="mt-space-xs flex items-center gap-space-xs">
                <span class="w-2 h-2 rounded-full bg-success"></span>
                <span class="font-label-sm text-label-sm text-outline">3 Aktif · 1 Draft</span>
            </div>
        </div>

        {{-- Kartu 2: Total Dosen --}}
        <div class="bg-surface-container-low p-space-lg rounded-2xl shadow-md relative overflow-hidden transition-all hover:bg-surface-container">
            <div class="absolute -right-6 -bottom-6 w-28 h-28 rounded-full bg-secondary/5 blur-2xl pointer-events-none"></div>
            <div class="flex items-center gap-space-xs">
                <div class="w-9 h-9 rounded-xl bg-secondary/20 flex items-center justify-center text-secondary">
                    <span class="material-symbols-outlined text-headline-sm">school</span>
                </div>
                <span class="font-label-lg text-label-lg text-on-surface-variant">Dosen</span>
            </div>
            <div class="mt-space-md flex items-baseline gap-space-xs">
                <span class="font-display-lg text-display-lg tracking-tight font-bold leading-none">4</span>
                <span class="font-headline-sm text-headline-sm text-on-surface-variant font-medium">Pengampu</span>
            </div>
            <div class="mt-space-xs flex items-center gap-space-xs">
                <span class="w-2 h-2 rounded-full bg-secondary"></span>
                <span class="font-label-sm text-label-sm text-outline">Aktif mengajar</span>
            </div>
        </div>

        {{-- Kartu 3: Total Mahasiswa --}}
        <div class="bg-surface-container-low p-space-lg rounded-2xl shadow-md relative overflow-hidden transition-all hover:bg-surface-container">
            <div class="absolute -right-6 -bottom-6 w-28 h-28 rounded-full bg-tertiary/5 blur-2xl pointer-events-none"></div>
            <div class="flex items-center gap-space-xs">
                <div class="w-9 h-9 rounded-xl bg-tertiary/20 flex items-center justify-center text-tertiary">
                    <span class="material-symbols-outlined text-headline-sm">groups</span>
                </div>
                <span class="font-label-lg text-label-lg text-on-surface-variant">Mahasiswa</span>
            </div>
            <div class="mt-space-md flex items-baseline gap-space-xs">
                <span class="font-display-lg text-display-lg tracking-tight font-bold leading-none">30</span>
                <span class="font-headline-sm text-headline-sm text-on-surface-variant font-medium">Terdaftar</span>
            </div>
            <div class="mt-space-xs flex items-center gap-space-xs">
                <span class="w-2 h-2 rounded-full bg-tertiary"></span>
                <span class="font-label-sm text-label-sm text-outline">Aktif semester ini</span>
            </div>
        </div>

        {{-- Kartu 4: Tugas --}}
        <div class="bg-surface-container-low p-space-lg rounded-2xl shadow-md relative overflow-hidden transition-all hover:bg-surface-container">
            <div class="absolute -right-6 -bottom-6 w-28 h-28 rounded-full bg-error/5 blur-2xl pointer-events-none"></div>
            <div class="flex items-center gap-space-xs">
                <div class="w-9 h-9 rounded-xl bg-error/20 flex items-center justify-center text-error">
                    <span class="material-symbols-outlined text-headline-sm">assignment</span>
                </div>
                <span class="font-label-lg text-label-lg text-on-surface-variant">Tugas</span>
            </div>
            <div class="mt-space-md flex items-baseline gap-space-xs">
                <span class="font-display-lg text-display-lg tracking-tight font-bold leading-none">12</span>
                <span class="font-headline-sm text-headline-sm text-on-surface-variant font-medium">Total</span>
            </div>
            <div class="mt-space-xs flex items-center gap-space-xs">
                <span class="w-2 h-2 rounded-full bg-error"></span>
                <span class="font-label-sm text-label-sm text-outline">5 Belum dinilai</span>
            </div>
        </div>
    </div>

    {{-- DAFTAR MATA KULIAH TERBARU --}}
    <div class="bg-surface-container-low p-space-lg rounded-2xl shadow-md mb-space-lg">
        <div class="flex items-center justify-between mb-space-md">
            <div class="flex items-center gap-space-xs">
                <span class="material-symbols-outlined text-primary text-headline-sm">bookmark</span>
                <h2 class="font-headline-sm text-headline-sm">Mata Kuliah Terbaru</h2>
            </div>
            <a href="{{ route('courses.index') }}" 
               class="flex items-center gap-space-2xs text-primary font-label-md text-label-md hover:text-primary-fixed transition-all">
                <span>Lihat Semua</span>
                <span class="material-symbols-outlined text-sm">arrow_forward</span>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-surface-container">
                        <th class="font-label-sm text-label-sm text-outline uppercase tracking-wider pb-2">Kode</th>
                        <th class="font-label-sm text-label-sm text-outline uppercase tracking-wider pb-2">Nama Mata Kuliah</th>
                        <th class="font-label-sm text-label-sm text-outline uppercase tracking-wider pb-2">SKS</th>
                        <th class="font-label-sm text-label-sm text-outline uppercase tracking-wider pb-2">Dosen</th>
                        <th class="font-label-sm text-label-sm text-outline uppercase tracking-wider pb-2 text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-surface-container/50 hover:bg-surface-container transition-all">
                        <td class="py-2.5 font-label-md text-label-md text-primary">SI2514024</td>
                        <td class="py-2.5 font-body-md text-body-md">Pemrograman Web</td>
                        <td class="py-2.5 font-body-md text-body-md">3</td>
                        <td class="py-2.5 font-body-md text-body-md">Dr. Aidil Saputra</td>
                        <td class="py-2.5 text-center">
                            <span class="px-2 py-0.5 rounded-full bg-success/20 text-success font-label-sm text-label-sm">Aktif</span>
                        </td>
                    </tr>
                    <tr class="border-b border-surface-container/50 hover:bg-surface-container transition-all">
                        <td class="py-2.5 font-label-md text-label-md text-primary">SI2514020</td>
                        <td class="py-2.5 font-body-md text-body-md">Basis Data</td>
                        <td class="py-2.5 font-body-md text-body-md">3</td>
                        <td class="py-2.5 font-body-md text-body-md">Prof. Siti Rahayu</td>
                        <td class="py-2.5 text-center">
                            <span class="px-2 py-0.5 rounded-full bg-success/20 text-success font-label-sm text-label-sm">Aktif</span>
                        </td>
                    </tr>
                    <tr class="border-b border-surface-container/50 hover:bg-surface-container transition-all">
                        <td class="py-2.5 font-label-md text-label-md text-primary">SI2514022</td>
                        <td class="py-2.5 font-body-md text-body-md">Jaringan Komputer</td>
                        <td class="py-2.5 font-body-md text-body-md">3</td>
                        <td class="py-2.5 font-body-md text-body-md">Dr. Budi Santoso</td>
                        <td class="py-2.5 text-center">
                            <span class="px-2 py-0.5 rounded-full bg-warning/20 text-warning font-label-sm text-label-sm">Draft</span>
                        </td>
                    </tr>
                    <tr class="hover:bg-surface-container transition-all">
                        <td class="py-2.5 font-label-md text-label-md text-primary">SI2514028</td>
                        <td class="py-2.5 font-body-md text-body-md">Kecerdasan Buatan</td>
                        <td class="py-2.5 font-body-md text-body-md">3</td>
                        <td class="py-2.5 font-body-md text-body-md">Dr. Dian Purnama</td>
                        <td class="py-2.5 text-center">
                            <span class="px-2 py-0.5 rounded-full bg-success/20 text-success font-label-sm text-label-sm">Aktif</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- BAGIAN BAWAH: QUICK LINKS + INFO --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-bento-gap-desktop">

        {{-- Quick Links --}}
        <div class="bg-surface-container-low p-space-lg rounded-2xl shadow-md">
            <div class="flex items-center gap-space-xs mb-space-md">
                <span class="material-symbols-outlined text-primary text-headline-sm">link</span>
                <h3 class="font-headline-sm text-headline-sm">Akses Cepat</h3>
            </div>
            <div class="flex flex-col gap-space-sm">
                <a href="{{ route('courses.index') }}" 
                   class="flex items-center gap-space-sm px-space-md py-2 rounded-xl bg-surface-container hover:bg-surface-container-high transition-all">
                    <span class="material-symbols-outlined text-sm text-primary">menu_book</span>
                    <span class="font-body-md text-body-md">Daftar Mata Kuliah</span>
                    <span class="material-symbols-outlined text-sm text-outline ml-auto">chevron_right</span>
                </a>
                <a href="{{ route('tentang') }}" 
                   class="flex items-center gap-space-sm px-space-md py-2 rounded-xl bg-surface-container hover:bg-surface-container-high transition-all">
                    <span class="material-symbols-outlined text-sm text-tertiary">info</span>
                    <span class="font-body-md text-body-md">Tentang Aplikasi</span>
                    <span class="material-symbols-outlined text-sm text-outline ml-auto">chevron_right</span>
                </a>
            </div>
        </div>

        {{-- Informasi Sistem --}}
        <div class="bg-surface-container-low p-space-lg rounded-2xl shadow-md">
            <div class="flex items-center gap-space-xs mb-space-md">
                <span class="material-symbols-outlined text-primary text-headline-sm">info</span>
                <h3 class="font-headline-sm text-headline-sm">Informasi Sistem</h3>
            </div>
            <div class="flex flex-col gap-space-sm">
                <div class="flex items-center justify-between py-space-xs border-b border-surface-container">
                    <span class="font-body-sm text-body-sm text-on-surface-variant">Aplikasi</span>
                    <span class="font-label-md text-label-md">Akademia LMS</span>
                </div>
                <div class="flex items-center justify-between py-space-xs border-b border-surface-container">
                    <span class="font-body-sm text-body-sm text-on-surface-variant">Versi</span>
                    <span class="font-label-md text-label-md">1.0.0</span>
                </div>
                <div class="flex items-center justify-between py-space-xs border-b border-surface-container">
                    <span class="font-body-sm text-body-sm text-on-surface-variant">Framework</span>
                    <span class="font-label-md text-label-md">Laravel 12</span>
                </div>
                <div class="flex items-center justify-between py-space-xs">
                    <span class="font-body-sm text-body-sm text-on-surface-variant">Status</span>
                    <span class="flex items-center gap-space-2xs font-label-md text-label-md text-secondary">
                        <span class="w-2 h-2 rounded-full bg-secondary animate-pulse"></span>
                        Online
                    </span>
                </div>
                <div class="mt-space-sm p-space-sm rounded-xl bg-surface-container-high text-center">
                    <span class="font-label-sm text-label-sm text-outline">
                        📅 {{ date('l, d F Y') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

</x-layout>