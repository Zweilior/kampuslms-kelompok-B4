<x-layout title="Masuk ke EduSpace">
    <div class="relative flex min-h-[calc(100vh-var(--navbar-height)-4rem)] w-full flex-col items-center justify-center py-8">
        
        {{-- Background Glow Effects dari Stitch --}}
        <div class="pointer-events-none absolute inset-0 flex items-center justify-center overflow-hidden">
            <div class="absolute -top-32 -left-20 h-[640px] w-[640px] rounded-full bg-primary/5 blur-[120px]"></div>
            <div class="absolute bottom-0 right-0 h-[520px] w-[520px] rounded-full bg-secondary-container/20 blur-[140px]"></div>
            <div class="absolute top-1/3 right-1/4 h-[380px] w-[380px] rounded-full bg-tertiary-container/10 blur-[100px]"></div>
        </div>

        {{-- Card Form Login --}}
        <div class="relative z-10 my-auto w-full max-w-md">
            <div class="rounded-xl border border-outline-variant/30 bg-surface-container p-6 shadow-2xl backdrop-blur-xl md:p-8">
                
                {{-- Header & Logo --}}
                <div class="mb-6 flex flex-col items-center text-center">
                    <div class="mb-3 flex h-14 w-14 items-center justify-center rounded-xl border border-primary/20 bg-primary/10 text-primary">
                        <span class="material-symbols-outlined text-3xl">shield</span>
                    </div>
                    <h1 class="text-2xl font-semibold tracking-tight text-on-surface">Masuk ke EduSpace</h1>
                    <p class="mt-1 text-sm text-on-surface-variant">Masukkan kredensial akun kampus Anda</p>
                </div>

                {{-- Form Login --}}
                <form action="{{ route('login') }}" method="POST" class="space-y-4">
                    @csrf

                    {{-- Input NIM / Email --}}
                    <div class="space-y-1.5">
                        <label for="identity" class="block text-xs font-semibold text-on-surface-variant">
                            Nomor Induk Mahasiswa (NIM) atau Email Kampus
                        </label>
                        <div class="relative flex items-center">
                            <span class="material-symbols-outlined absolute left-3 text-[20px] text-outline">badge</span>
                            <input type="text" id="identity" name="identity" value="{{ old('identity') }}"
                                placeholder="cth: 1202210045 atau nama@univ.ac.id" required autofocus
                                class="w-full rounded-lg bg-surface-container-lowest py-2.5 pr-4 pl-10 text-sm text-on-surface shadow-inner outline-none transition-all placeholder:text-outline-variant focus:ring-2 focus:ring-primary/60">
                        </div>
                        @error('identity')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Input Password dengan Toggle Alpine.js --}}
                    <div class="space-y-1.5" x-data="{ showPassword: false }">
                        <div class="flex items-center justify-between">
                            <label for="password" class="block text-xs font-semibold text-on-surface-variant">Kata Sandi</label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-xs font-medium text-primary transition-colors hover:text-secondary">
                                    Lupa Kata Sandi?
                                </a>
                            @endif
                        </div>
                        <div class="relative flex items-center">
                            <span class="material-symbols-outlined absolute left-3 text-[20px] text-outline">key</span>
                            <input :type="showPassword ? 'text' : 'password'" id="password" name="password"
                                placeholder="••••••••••••" required
                                class="w-full rounded-lg bg-surface-container-lowest py-2.5 pr-10 pl-10 text-sm text-on-surface shadow-inner outline-none transition-all placeholder:text-outline-variant focus:ring-2 focus:ring-primary/60">
                            
                            <button type="button" @click="showPassword = !showPassword" tabindex="-1"
                                class="absolute right-3 flex items-center justify-center text-outline transition-colors hover:text-on-surface">
                                <span class="material-symbols-outlined text-[18px]" x-text="showPassword ? 'visibility_off' : 'visibility'">
                                    visibility
                                </span>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Remember Me --}}
                    <div class="flex items-center justify-between pt-0.5">
                        <label class="flex cursor-pointer select-none items-center gap-2">
                            <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}
                                class="h-4 w-4 rounded bg-surface-container-lowest text-primary accent-primary focus:ring-0">
                            <span class="text-xs text-on-surface-variant">Ingat saya</span>
                        </label>
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit"
                        class="group flex w-full items-center justify-center gap-2 rounded-lg bg-primary py-3 px-4 font-bold text-on-primary shadow-lg shadow-primary/20 transition-all duration-200 hover:bg-primary-container hover:shadow-primary/30 active:scale-[0.99]">
                        <span>Masuk ke Akun</span>
                        <span class="material-symbols-outlined text-[20px] transition-transform group-hover:translate-x-1">arrow_forward</span>
                    </button>
                </form>

                {{-- Footer Register --}}
                <div class="pt-4 text-center">
                    <p class="text-xs text-on-surface-variant">
                        Belum punya akun?
                        <a href="#" class="ml-1 font-semibold text-primary hover:underline">Daftar</a>
                    </p>
                </div>

            </div>
        </div>
    </div>
</x-layout>