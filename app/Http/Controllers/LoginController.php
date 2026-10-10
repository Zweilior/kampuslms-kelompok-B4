<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules\Password as PasswordRule;

class LoginController extends Controller
{
    /**
     * Menampilkan halaman form login
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Memproses submit login
     */
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'identity' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $fieldType = filter_var($credentials['identity'], FILTER_VALIDATE_EMAIL) ? 'email' : 'nim_nip';

        if (Auth::attempt([$fieldType => $credentials['identity'], 'password' => $credentials['password']], $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(match (Auth::user()->role) {
                'admin' => route('admin.dashboard'),
                'dosen' => route('dosen.dashboard'),
                default => route('mahasiswa.dashboard'),
            });
        }

        return back()->withErrors([
            'auth' => 'NIM/Email atau kata sandi salah. Silakan periksa kembali.',
        ])->onlyInput('identity');
    }

    /**
     * Memproses logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /*
    |--------------------------------------------------------------------------
    | FITUR RESET PASSWORD (LUPA KATA SANDI)
    |--------------------------------------------------------------------------
    */

    /**
     * Menampilkan form "Lupa Password"
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Mengirim link reset password ke email user
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // Password::sendResetLink() akan mencari user berdasarkan email
        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    /**
     * Menampilkan form "Buat Password Baru" (diklik dari link di email)
     */
    public function showResetForm(Request $request, $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    /**
     * Memproses penyimpanan password baru
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }
}