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
    <style>
        @layer base {

            html,
            body {
                margin: 0;
                padding: 0;
            }

            body {
                overscroll-behavior: none;
            }
        }

        ::-webkit-scrollbar {
            display: none;
        }
    </style>
    <script src="https://cdn.tailwindcss.com"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "surface-container-highest": "#30362f",
                        "secondary-container": "#355116",
                        "secondary": "#afd188",
                        "surface-container-lowest": "#0a1009",
                        "on-surface": "#dee4d9",
                        "inverse-primary": "#486728",
                        "on-primary-fixed-variant": "#314f12",
                        "on-secondary-container": "#a1c37b",
                        "outline": "#8e9384",
                        "surface-container": "#1b211a",
                        "inverse-surface": "#dee4d9",
                        "surface-bright": "#353b33",
                        "primary": "#add286",
                        "surface-tint": "#add286",
                        "surface-variant": "#30362f",
                        "surface-dim": "#0f150e",
                        "on-primary-container": "#244104",
                        "on-error": "#690005",
                        "primary-fixed-dim": "#add286",
                        "on-secondary": "#1d3700",
                        "tertiary": "#d9c49b",
                        "on-tertiary-fixed-variant": "#534525",
                        "secondary-fixed-dim": "#afd188",
                        "surface": "#0f150e",
                        "on-surface-variant": "#c4c8b9",
                        "inverse-on-surface": "#2c322a",
                        "on-error-container": "#ffdad6",
                        "outline-variant": "#44483c",
                        "surface-container-low": "#171d16",
                        "background": "#0f150e",
                        "tertiary-fixed": "#f6e0b5",
                        "tertiary-container": "#b5a17a",
                        "error": "#ffb4ab",
                        "on-primary": "#1d3700",
                        "surface-container-high": "#252c24",
                        "on-primary-fixed": "#0e2000",
                        "on-tertiary-container": "#453819",
                        "secondary-fixed": "#caeea2",
                        "primary-fixed": "#c9ee9f",
                        "primary-container": "#8bae66",
                        "on-secondary-fixed-variant": "#324e14",
                        "on-tertiary": "#3b2f11",
                        "on-background": "#dee4d9",
                        "tertiary-fixed-dim": "#d9c49b",
                        "on-secondary-fixed": "#0f2000",
                        "on-tertiary-fixed": "#251a01",
                        "error-container": "#93000a"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                    "spacing": {
                        "space-md": "1rem",
                        "page-margin-desktop": "2rem",
                        "space-xs": "0.5rem",
                        "space-2xs": "0.25rem",
                        "page-margin-mobile": "1rem",
                        "bento-gap-desktop": "1.25rem",
                        "space-xl": "2rem",
                        "bento-gap-mobile": "0.75rem",
                        "space-2xl": "3rem",
                        "space-lg": "1.5rem",
                        "space-sm": "0.75rem"
                    },
                    "fontFamily": {
                        "headline-md": ["Plus Jakarta Sans"],
                        "label-md": ["Inter"],
                        "headline-lg": ["Plus Jakarta Sans"],
                        "headline-sm": ["Plus Jakarta Sans"],
                        "display-lg": ["Plus Jakarta Sans"],
                        "body-md": ["Plus Jakarta Sans"],
                        "label-lg": ["Inter"],
                        "label-sm": ["Inter"],
                        "body-sm": ["Plus Jakarta Sans"],
                        "body-lg": ["Plus Jakarta Sans"]
                    },
                    "fontSize": {
                        "headline-md": ["22px", {
                            "lineHeight": "30px",
                            "letterSpacing": "-0.01em",
                            "fontWeight": "600"
                        }],
                        "label-md": ["11px", {
                            "lineHeight": "14px",
                            "letterSpacing": "0.02em",
                            "fontWeight": "600"
                        }],
                        "headline-lg": ["28px", {
                            "lineHeight": "36px",
                            "letterSpacing": "-0.01em",
                            "fontWeight": "600"
                        }],
                        "headline-sm": ["18px", {
                            "lineHeight": "26px",
                            "fontWeight": "600"
                        }],
                        "display-lg": ["44px", {
                            "lineHeight": "52px",
                            "letterSpacing": "-0.02em",
                            "fontWeight": "700"
                        }],
                        "body-md": ["14px", {
                            "lineHeight": "20px",
                            "fontWeight": "400"
                        }],
                        "label-lg": ["13px", {
                            "lineHeight": "16px",
                            "letterSpacing": "0.01em",
                            "fontWeight": "600"
                        }],
                        "label-sm": ["10px", {
                            "lineHeight": "12px",
                            "letterSpacing": "0.03em",
                            "fontWeight": "600"
                        }],
                        "body-sm": ["12px", {
                            "lineHeight": "18px",
                            "fontWeight": "400"
                        }],
                        "body-lg": ["16px", {
                            "lineHeight": "24px",
                            "fontWeight": "400"
                        }]
                    }
                }
            }
        }
    </script>
</head>

<body
    class="bg-surface font-body-md text-body-md text-on-surface antialiased selection:bg-primary-container selection:text-on-primary-container">

    {{-- SIDEBAR --}}
    <aside
        class="fixed left-0 top-0 h-full w-64 bg-surface-container-low z-50 flex flex-col justify-between py-space-lg px-space-md shadow-[0_1px_8px_rgba(0,0,0,0.25)]">
        <div class="flex flex-col gap-space-xl">
            <div class="flex items-center gap-space-sm px-space-sm">
                <span class="material-symbols-outlined text-primary text-headline-lg">school</span>
                <div class="flex flex-col">
                    <span class="font-headline-sm text-headline-sm tracking-tight leading-none">EduSpace</span>
                    <span
                        class="font-label-sm text-label-sm text-primary tracking-wider uppercase mt-space-2xs">Learning Management</span>
                </div>
            </div>

            <nav class="flex flex-col gap-space-2xs"
                data-active-classes="bg-secondary-container text-on-secondary-container font-semibold shadow-[0_0_16px_rgba(175,209,136,0.15)]">
                @php
                    $navItems = [
                        ['path' => 'dashboard', 'icon' => 'grid_view', 'label' => 'Dashboard', 'route' => null],
                        [
                            'path' => 'courses',
                            'icon' => 'menu_book',
                            'label' => 'Mata Kuliah',
                            'route' => 'courses.index',
                        ],
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
                        class="flex items-center gap-space-sm px-space-md py-space-sm rounded-xl transition-all duration-200
                              {{ $isActive
                                  ? 'bg-secondary-container text-on-secondary-container font-semibold shadow-[0_0_16px_rgba(175,209,136,0.15)]'
                                  : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}">
                        <span class="material-symbols-outlined text-headline-sm">{{ $item['icon'] }}</span>
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </nav>
        </div>

        <div class="px-space-xs">
            <div
                class="bg-surface-container p-space-md rounded-xl flex items-center justify-between shadow-[0_1px_8px_rgba(0,0,0,0.12)]">
                <div class="flex flex-col">
                    <span class="font-label-sm text-label-sm text-on-surface-variant">Sistem Status</span>
                    <span
                        class="font-label-lg text-label-lg text-secondary flex items-center gap-space-2xs mt-space-2xs">
                        <span class="w-2 h-2 rounded-full bg-secondary inline-block animate-pulse"></span>Kampus Online
                    </span>
                </div>
                <span class="material-symbols-outlined text-on-surface-variant text-headline-sm">dns</span>
            </div>
        </div>
    </aside>

    {{-- KONTEN UTAMA --}}
    <div class="pl-64">
        <main class="w-full min-h-screen px-page-margin-desktop py-space-lg bg-surface">
            {{ $slot }}
        </main>
    </div>

</body>

</html>
