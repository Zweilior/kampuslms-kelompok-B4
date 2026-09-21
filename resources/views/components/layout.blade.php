<!DOCTYPE html>
<html class="dark" lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $title ?? 'KampusLMS' }}</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Plus+Jakarta+Sans:wght@100..900&family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&display=swap" rel="stylesheet" />

    {{-- Tailwind CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('js/tailwind-config.js') }}"></script>

    {{-- Alpine.js CDN --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Custom CSS --}}
    @vite('resources/css/app.css')
    
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}">

    <style>
        /* Memastikan konten utama tidak tertutup navbar */
        .main-content {
            padding-top: 80px; /* Sesuaikan dengan tinggi navbar Anda */
            min-height: 100vh;
            background-color: #0f0f0f; /* Warna background gelap */
        }
    </style>
</head>

<body class="bg-surface font-body-md text-body-md text-on-surface antialiased selection:bg-primary-container selection:text-on-primary-container">

    {{-- NAVBAR --}}
    @include('components.navbar')

    {{-- KONTEN UTAMA --}}
    <div class="main-content">

        {{-- FLASH MESSAGE --}}
        @if (session('success'))
            <div class="mb-4 rounded-lg border border-green-500/30 bg-green-500/10 px-4 py-3 text-green-400">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 rounded-lg border border-red-500/30 bg-red-500/10 px-4 py-3 text-red-400">
                {{ session('error') }}
            </div>
        @endif

        {{ $slot }}

    </div>

</body>

</html>