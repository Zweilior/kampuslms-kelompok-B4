<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Menampilkan halaman form login
     */
    public function showLoginForm()
    {
        return view('auth.login'); // Sesuaikan dengan lokasi file login.blade.php kamu
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
}