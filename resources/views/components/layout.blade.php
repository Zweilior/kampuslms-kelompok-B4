<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $title ?? 'KampusLMS' }}</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Poppins:wght@100..900&family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&display=swap"
        rel="stylesheet" />

    {{-- Tailwind CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('js/tailwind-config.js') }}"></script>

    {{-- Alpine.js CDN --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Custom CSS --}}
    @vite('resources/css/app.css')

</head>

<body class="font-body-md text-body-md text-on-surface antialiased selection:bg-primary-container selection:text-on-primary-container">

    @auth
        @include('components.navbar')
    @endauth

    {{-- KONTEN UTAMA --}}
    <div class="{{ auth()->check() ? 'main-content main-content--with-navbar' : 'main-content' }}">
        {{ $slot ?? '' }}
        @yield('content')
    </div>

    @auth
        @include('components.footer')
    @endauth

</body>

</html>
