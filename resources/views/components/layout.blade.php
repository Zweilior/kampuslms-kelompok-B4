<!DOCTYPE html>
<html class="dark" lang="id">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>{{ $title ?? 'Akademia LMS' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Plus+Jakarta+Sans:wght@100..900&family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&display=swap"
        rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}">

    <script src="https://cdn.tailwindcss.com"></script>
    <script id="tailwind-config">
    </script>

</head>

<body class="bg-surface font-body-md text-body-md text-on-surface antialiased selection:bg-primary-container selection:text-on-primary-container">

    <aside class="app-sidebar">
        <div class="flex flex-col gap-space-xl">
            <div class="flex items-center gap-space-sm px-space-sm">
                <span class="material-symbols-outlined text-primary text-headline-lg">school</span>
                <div class="flex flex-col">
                    <span class="font-headline-sm text-headline-sm tracking-tight leading-none">EduSpace</span>
                    <span class="font-label-sm text-label-sm text-primary tracking-wider uppercase mt-space-2xs">Learning Management</span>
                </div>
            </div>

            <nav class="flex flex-col gap-space-2xs">
                @php
                    $navItems = [
                        ['path' => 'dashboard', 'icon' => 'grid_view', 'label' => 'Dashboard', 'route' => null],
                        ['path' => 'courses', 'icon' => 'menu_book', 'label' => 'Mata Kuliah', 'route' => 'courses.index'],
                        ['path' => 'jadwal', 'icon' => 'calendar_today', 'label' => 'Jadwal Kuliah', 'route' => null],
                        ['path' => 'tugas', 'icon' => 'assignment', 'label' => 'Tugas & Kuis', 'route' => null],
                        ['path' => 'nilai', 'icon' => 'grade', 'label' => 'Nilai & KHS', 'route' => null],
                        ['path' => 'pengumuman', 'icon' => 'campaign', 'label' => 'Pengumuman', 'route' => null],
                    ];
                    $activeNav = $activeNav ?? 'courses';
                @endphp

                @foreach ($navItems as $item)
                    @php $isActive = $activeNav === $item['path']; @endphp
                    <a href="{{ $item['route'] ? route($item['route']) : '#' }}"
                       class="nav-link {{ $isActive ? 'active' : '' }}">
                        <span class="material-symbols-outlined text-headline-sm">{{ $item['icon'] }}</span>
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </nav>
        </div>

        <div class="px-space-xs">
            <div class="system-status">
                <div class="flex flex-col">
                    <span class="font-label-sm text-label-sm text-on-surface-variant">Sistem Status</span>
                    <span class="font-label-lg text-label-lg text-secondary flex items-center gap-space-2xs mt-space-2xs">
                        <span class="w-2 h-2 rounded-full bg-secondary inline-block animate-pulse"></span>Kampus Online
                    </span>
                </div>
                <span class="material-symbols-outlined text-on-surface-variant text-headline-sm">dns</span>
            </div>
        </div>
    </aside>

    {{-- KONTEN UTAMA --}}
    <div class="app-main-wrapper">
        <main class="app-main">
            {{ $slot }}
        </main>
    </div>

</body>

</html>