<x-layout title="Masuk ke Akun">
    {{-- CSS Terpisah khusus halaman Login --}}
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    @endpush

    <div class="login-wrapper">
        <div class="login-card">

            {{-- Header & Shield Icon --}}
            <div class="login-header">
                <div class="shield-icon">
                    <span class="material-symbols-outlined">shield</span>
                </div>
                <h1 class="login-title">Masuk ke EduSpace</h1>
                <p class="login-subtitle">Masukkan kredensial akun kampus Anda</p>
            </div>

            {{-- Form Login --}}
            <form action="{{ route('login') }}" method="POST" class="login-form">
                @csrf

                {{-- Input NIM / Email --}}
                <div class="form-group">
                    <label for="identity" class="form-label">
                        Nomor Induk Mahasiswa (NIM) atau Email Kampus
                    </label>
                    <div class="input-wrapper">
                        <span class="material-symbols-outlined input-icon">badge</span>
                        <input type="text" id="identity" name="identity" value="{{ old('identity') }}"
                            placeholder="cth: 1202210045 atau nama@univ.ac.id" required autofocus class="form-input">
                    </div>
                    @error('identity')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Input Password --}}
                <div class="form-group" x-data="{ showPassword: false }">
                    <div class="label-row">
                        <label for="password" class="form-label">Kata Sandi</label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="forgot-link">
                                Lupa Kata Sandi?
                            </a>
                        @endif
                    </div>
                    <div class="input-wrapper">
                        <span class="material-symbols-outlined input-icon">key</span>
                        <input :type="showPassword ? 'text' : 'password'" id="password" name="password"
                            placeholder="••••••••••••" required class="form-input">
                        <button type="button" @click="showPassword = !showPassword" class="toggle-password"
                            tabindex="-1">
                            <span class="material-symbols-outlined"
                                x-text="showPassword ? 'visibility_off' : 'visibility'">
                                visibility
                            </span>
                        </button>
                    </div>
                    @error('password')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Remember Me Checkbox --}}
                <div class="remember-group">
                    <label class="checkbox-container">
                        <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <span class="checkmark"></span>
                        <span class="remember-label">Ingat saya</span>
                    </label>
                </div>

                {{-- Submit Button --}}
                <button type="submit" class="btn-submit">
                    <span>Masuk ke Akun</span>
                    <span class="material-symbols-outlined btn-icon">arrow_forward</span>
                </button>

                {{-- Register Link --}}
                <div class="login-footer">
                    <span>Belum punya akun?</span>
                    <a href="{{ route('register') }}" class="register-link">Daftar</a>
                </div>

            </form>

        </div>
    </div>
</x-layout>
