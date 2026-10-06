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

    <style>
        .auth-shell {
            display: grid;
            min-height: 100vh;
            min-height: 100svh;
            grid-template-columns: minmax(0, 0.9fr) minmax(0, 1.1fr);
            overflow: hidden;
            background: #10150f;
        }

        .auth-pattern {
            background-image:
                radial-gradient(rgba(255, 255, 255, 0.12) 0.7px, transparent 0.7px),
                linear-gradient(135deg, #233e19 0%, #3d6827 52%, #759c54 100%);
            background-size: 22px 22px, 180% 180%;
            background-position: 0 0, 0% 50%;
            animation: auth-gradient-shift 18s ease-in-out infinite;
        }

        .auth-brand-panel {
            isolation: isolate;
            border-right: 1px solid rgba(255, 255, 255, 0.1);
            animation: auth-brand-in 900ms cubic-bezier(0.2, 0.75, 0.25, 1) both;
        }

        .auth-form-panel {
            min-width: 0;
            padding: clamp(1.25rem, 3vh, 2.5rem) clamp(1.5rem, 4vw, 4rem);
            animation: auth-form-in 800ms 100ms cubic-bezier(0.2, 0.75, 0.25, 1) both;
        }

        .auth-form-content {
            padding: clamp(1.25rem, 2.2vw, 1.8rem);
            border: 1px solid rgba(210, 224, 197, 0.12);
            border-radius: 1.25rem;
            background: linear-gradient(145deg, rgba(255, 255, 255, 0.045), rgba(255, 255, 255, 0.015));
            box-shadow: 0 24px 70px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(14px);
        }

        .auth-form-content h2,
        .auth-brand-panel h1 {
            font-family: 'Poppins', 'Inter', sans-serif;
        }

        .auth-form-content input {
            min-height: 3.25rem;
            transition: border-color 220ms ease, box-shadow 220ms ease, background-color 220ms ease;
        }

        .auth-field-icon,
        .auth-field-action {
            position: absolute;
            top: 50%;
            display: inline-flex;
            width: 1.5rem;
            height: 1.5rem;
            align-items: center;
            justify-content: center;
            line-height: 1;
            transform: translateY(-50%);
        }

        .auth-field-icon {
            left: 1rem;
            pointer-events: none;
        }

        .auth-field-action {
            right: 0.75rem;
        }

        .auth-form-content input:focus {
            background-color: rgba(255, 255, 255, 0.06);
        }

        .auth-form-content button[type='submit'] {
            min-height: 3.5rem;
            transition: transform 220ms ease, box-shadow 220ms ease, background-color 220ms ease;
        }

        .auth-mobile-brand {
            animation: auth-form-in 700ms 80ms cubic-bezier(0.2, 0.75, 0.25, 1) both;
        }

        @keyframes auth-gradient-shift {
            0%, 100% { background-position: 0 0, 0% 50%; }
            50% { background-position: 0 0, 100% 50%; }
        }

        @keyframes auth-brand-in {
            from { opacity: 0; transform: translateX(-18px); }
            to { opacity: 1; transform: translateX(0); }
        }

        @keyframes auth-form-in {
            from { opacity: 0; transform: translateY(14px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 1023px) {
            .auth-shell {
                grid-template-columns: minmax(0, 0.82fr) minmax(0, 1.18fr);
            }

            .auth-brand-panel {
                padding: 1.5rem;
            }

            .auth-brand-panel h1 {
                font-size: 2.25rem;
            }

            .auth-form-panel {
                padding: 1.5rem;
            }
        }

        @media (max-width: 767px) {
            .auth-shell {
                display: flex;
                flex-direction: column;
                overflow: visible;
                background: radial-gradient(ellipse at top, rgba(89, 132, 57, 0.14), transparent 55%), #10150f;
            }

            .auth-form-panel {
                flex: 1;
                justify-content: flex-start;
                padding: max(1.25rem, env(safe-area-inset-top)) 1rem max(1.5rem, env(safe-area-inset-bottom));
                transition: padding 320ms cubic-bezier(0.2, 0.75, 0.25, 1);
            }

            .auth-mobile-brand {
                width: 100%;
                max-width: 28rem;
                margin: 0 auto 1.5rem;
                transition: margin 320ms cubic-bezier(0.2, 0.75, 0.25, 1);
            }

            .auth-mobile-brand > div:first-child {
                width: 3.5rem;
                height: 3.5rem;
                border: 1px solid rgba(255, 255, 255, 0.2);
                background: rgba(255, 255, 255, 0.12);
                backdrop-filter: blur(10px);
            }

            .auth-form-content {
                width: 100%;
                max-width: 28rem;
                margin-inline: auto;
                padding: 1.4rem;
                border-radius: 1rem;
                animation: auth-form-in 700ms 140ms cubic-bezier(0.2, 0.75, 0.25, 1) both;
                transition: width 320ms cubic-bezier(0.2, 0.75, 0.25, 1), padding 320ms cubic-bezier(0.2, 0.75, 0.25, 1), border-radius 320ms ease;
            }

            .auth-form-content h2 {
                font-size: 1.75rem;
            }

            .auth-form-content input {
                font-size: 16px;
            }

            .auth-form-content .auth-bottom-links {
                flex-wrap: wrap;
                row-gap: 0.75rem;
            }
        }

        @media (max-width: 380px) {
            .auth-form-panel {
                padding-inline: 0.75rem;
            }

            .auth-form-content {
                padding: 1.15rem;
            }

            .auth-mobile-brand {
                margin-bottom: 1.1rem;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                scroll-behavior: auto !important;
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
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
                @if ($errors->any() && !$errors->has('identity') && !$errors->has('password'))
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
                    <a href="#" class="hover:text-primary transition-colors">Bantuan</a>
                    <span class="w-1 h-1 rounded-full bg-outline-variant"></span>
                    <a href="#" class="hover:text-primary transition-colors">Kontak</a>
                </div>

            </div>
        </main>

    </div>

</body>
</html>