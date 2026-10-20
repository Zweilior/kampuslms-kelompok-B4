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
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body class="font-body-md text-body-md text-on-surface antialiased selection:bg-primary-container selection:text-on-primary-container">

    @auth
        @include('components.navbar')
    @endauth

    {{-- KONTEN UTAMA --}}
    <div class="{{ auth()->check() ? 'main-content main-content--with-navbar' : 'main-content' }}">
        <div class="layout-feedback max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="my-space-sm flex items-center gap-space-xs rounded-xl border border-primary/20 bg-primary/10 px-space-md py-space-sm text-primary" role="status">
                    <span class="material-symbols-outlined shrink-0 text-xl" aria-hidden="true">check_circle</span>
                    <span class="font-body-md text-sm sm:text-base">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="my-space-sm flex items-center gap-space-xs rounded-xl border border-error/20 bg-error/10 px-space-md py-space-sm text-error" role="alert">
                    <span class="material-symbols-outlined shrink-0 text-xl" aria-hidden="true">error</span>
                    <span class="font-body-md text-sm sm:text-base">{{ session('error') }}</span>
                </div>
            @endif

            @if ($errors->has('file'))
                <div class="my-space-sm flex items-center gap-space-xs rounded-xl border border-error/20 bg-error/10 px-space-md py-space-sm text-error" role="alert">
                    <span class="material-symbols-outlined shrink-0 text-xl" aria-hidden="true">warning</span>
                    <span class="font-body-md text-sm sm:text-base">{{ $errors->first('file') }}</span>
                </div>
            @endif
        </div>

        {{ $slot ?? '' }}
        @yield('content')
    </div>

    @auth
        @include('components.footer')
    @endauth

</body>

</html>
