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

        $fieldType = filter_var($credentials['identity'], FILTER_VALIDATE_EMAIL) ? 'email' : 'nim';

        if (Auth::attempt([$fieldType => $credentials['identity'], 'password' => $credentials['password']], $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('courses.index'));
        }

        return back()->withErrors([
            'identity' => 'Kredensial yang Anda masukkan tidak cocok.',
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