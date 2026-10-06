<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Masuk - EduSpace</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Poppins:wght@100..900&family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&display=swap" rel="stylesheet" />

    {{-- Tailwind & Config --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('js/tailwind-config.js') }}"></script>

    {{-- Alpine --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Vite --}}
    @vite('resources/css/app.css')
    @vite('resources/css/auth/login.css')

</head>
<body class="font-sans antialiased bg-surface text-on-surface">

    <div class="auth-shell min-h-screen">

        {{-- ============================================
             KIRI: BRANDING PANEL
             ============================================ --}}
        <aside class="auth-brand-panel relative hidden md:flex flex-col justify-between p-8 xl:p-10 overflow-hidden auth-pattern">

            {{-- Dot grid overlay --}}
            <div class="absolute inset-0 auth-dot-grid opacity-40"></div>

            {{-- Logo + Brand --}}
            <div class="relative z-10 flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-white/15 backdrop-blur border border-white/20 flex items-center justify-center">
                    <span class="material-symbols-outlined text-white text-2xl">school</span>
                </div>
                <div class="flex flex-col leading-tight">
                    <span class="text-white font-bold text-lg tracking-tight">EduSpace</span>
                    <span class="text-white/70 text-[10px] tracking-[0.15em] uppercase font-semibold">Learning Management Systems</span>
                </div>
            </div>

            {{-- Headline --}}
            <div class="relative z-10 space-y-6">
                <h1 class="text-white text-4xl xl:text-5xl font-bold leading-tight tracking-tight">
                    Belajar tanpa batas,<br/>
                    <span class="text-primary-fixed">tumbuh bersama.</span>
                </h1>
                <p class="text-white/80 text-base leading-relaxed max-w-md">
                    Platform pembelajaran terpadu untuk mahasiswa, dosen, dan staf akademik kampus.
                    Kelola materi, tugas, dan nilai dalam satu tempat.
                </p>

                {{-- Feature bullets --}}
                <ul class="space-y-3 pt-2">
                    <li class="flex items-center gap-3 text-white/90 text-sm">
                        <span class="w-6 h-6 rounded-full bg-white/15 border border-white/20 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[14px] text-white">check</span>
                        </span>
                        Akses materi kuliah kapan saja
                    </li>
                    <li class="flex items-center gap-3 text-white/90 text-sm">
                        <span class="w-6 h-6 rounded-full bg-white/15 border border-white/20 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[14px] text-white">check</span>
                        </span>
                        Pengumpulan tugas secara digital
                    </li>
                    <li class="flex items-center gap-3 text-white/90 text-sm">
                        <span class="w-6 h-6 rounded-full bg-white/15 border border-white/20 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[14px] text-white">check</span>
                        </span>
                        Monitoring nilai real-time
                    </li>
                </ul>
            </div>

            {{-- Footer --}}
            <div class="relative z-10 mb-4 text-white/60 text-xs">
                © 2026 EduSpace • Institut Teknologi Kalimantan
            </div>

        </aside>

        {{-- ============================================
             KANAN: FORM LOGIN
             ============================================ --}}
        <main class="auth-form-panel flex flex-col items-center justify-center bg-surface">

            {{-- Brand untuk mobile --}}
            <div class="auth-mobile-brand md:hidden flex items-center gap-3 mb-8">
                <div class="w-10 h-10 rounded-xl bg-primary flex items-center justify-center">
                    <span class="material-symbols-outlined text-white text-xl">school</span>
                </div>
                <div class="flex flex-col leading-tight">
                    <span class="font-bold text-on-surface">EduSpace</span>
                    <span class="text-on-surface-variant text-[10px] tracking-[0.15em] uppercase">Learning Management System</span>
                </div>
            </div>

            <div class="auth-form-content w-full max-w-lg">

                {{-- Badge semester --}}
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-secondary-container text-on-secondary-container text-xs font-semibold mb-4">
                    <span class="w-1.5 h-1.5 rounded-full bg-primary animate-pulse"></span>
                    Portal Akademik • Semester Ganjil 2026/2027
                </div>

                {{-- Headline --}}
                <div class="mb-5">
                    <h2 class="text-3xl font-bold text-on-surface tracking-tight mb-2">
                        Selamat Datang 👋
                    </h2>
                    <p class="text-on-surface-variant text-sm">
                        Silakan masuk untuk melanjutkan ke dashboard Anda.
                    </p>
                </div>

                {{-- Error umum --}}
                @if ($errors->any() && !$errors->has('identity') && !$errors->has('password') && !$errors->has('auth'))
                    <div class="mb-5 px-4 py-3 rounded-xl bg-error/10 border border-error/30 text-error text-sm">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST" class="space-y-4">
                    @csrf

                    {{-- Identity --}}
                    <div>
                        <label for="identity" class="block text-sm font-semibold text-on-surface mb-2">
                            NIM atau Email Kampus
                        </label>
                        <div class="relative">
                            <span class="auth-field-icon material-symbols-outlined text-outline text-xl">
                                badge
                            </span>
                            <input
                                type="text"
                                id="identity"
                                name="identity"
                                value="{{ old('identity') }}"
                                placeholder="10241000 atau nama@kampuslms.test"
                                required autofocus autocomplete="username"
                                class="w-full rounded-xl border border-outline-variant bg-surface-container-lowest py-3 pl-12 pr-4 text-sm text-on-surface outline-none transition-all placeholder:text-outline focus:border-primary focus:ring-2 focus:ring-primary/20" />
                        </div>
                        @error('identity')
                            <p class="mt-2 text-xs text-error flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">error</span>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div x-data="{ show: false }">
                        <div class="flex items-center justify-between mb-2">
                            <label for="password" class="block text-sm font-semibold text-on-surface">
                                Kata Sandi
                            </label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-xs font-medium text-primary hover:underline">
                                    Lupa kata sandi?
                                </a>
                            @endif
                        </div>
                        <div class="relative">
                            <span class="auth-field-icon material-symbols-outlined text-outline text-xl">
                                lock
                            </span>
                            <input
                                :type="show ? 'text' : 'password'"
                                type="password"
                                id="password"
                                name="password"
                                placeholder="Masukkan kata sandi"
                                required autocomplete="current-password"
                                class="w-full rounded-xl border border-outline-variant bg-surface-container-lowest py-3 pl-12 pr-12 text-sm text-on-surface outline-none transition-all placeholder:text-outline focus:border-primary focus:ring-2 focus:ring-primary/20" />
                            <button
                                type="button"
                                @click="show = !show"
                                tabindex="-1"
                                class="auth-field-action text-outline hover:text-on-surface rounded-lg transition-colors">
                                <span class="material-symbols-outlined text-xl" x-text="show ? 'visibility_off' : 'visibility'">visibility</span>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-2 text-xs text-error flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">error</span>
                                {{ $message }}
                            </p>
                        @enderror
                        @error('auth')
                            <p class="mt-2 text-sm font-medium text-error flex items-center gap-1" role="alert">
                                <span class="material-symbols-outlined text-base">error</span>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Remember --}}
                    <label class="flex items-center gap-2.5 cursor-pointer select-none">
                        <input
                            type="checkbox"
                            name="remember"
                            {{ old('remember') ? 'checked' : '' }}
                            class="w-4 h-4 rounded border-outline-variant text-primary accent-primary focus:ring-primary/30" />
                        <span class="text-sm text-on-surface-variant">Ingat saya di perangkat ini</span>
                    </label>

                    {{-- Submit --}}
                    <button
                        type="submit"
                        class="group w-full flex items-center justify-center gap-2 rounded-xl bg-primary hover:bg-primary-dark text-white font-semibold py-3.5 px-4 transition-all shadow-lg shadow-primary/20 hover:shadow-primary/30 active:scale-[0.99]">
                        Masuk ke Akun
                        <span class="material-symbols-outlined text-xl transition-transform group-hover:translate-x-1">arrow_forward</span>
                    </button>

                </form>

                {{-- Footer --}}
                <div class="mt-5 text-center text-sm text-on-surface-variant">
                    Belum punya akun?
                    <a href="#" class="font-semibold text-primary hover:underline ml-1">Hubungi Admin</a>
                </div>

                {{-- Bottom links --}}
                <div class="auth-bottom-links mt-6 flex items-center justify-center gap-4 text-xs text-outline">
                    <a href="#" class="hover:text-primary transition-colors">Kebijakan Privasi</a>
                    <span class="w-1 h-1 rounded-full bg-outline-variant"></span>
                    <a href="{{ route('tentang') }}" class="hover:text-primary transition-colors">Tentang Kami</a>
                </div>

            </div>
        </main>

    </div>

</body>
</html>