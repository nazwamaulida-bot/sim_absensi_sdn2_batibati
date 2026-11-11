<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman formulir login.
     */
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    /**
     * Memproses percobaan login pengguna.
     */
    public function login(Request $request): RedirectResponse
    {
        // 1. Validasi input email dan password
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $remember = $request->boolean('remember');

        // 2. Coba autentikasi menggunakan Auth::attempt()
        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            // 3. Pastikan hanya user dengan status 'aktif' yang dapat login
            if (! $user->isAktif()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Akun Anda dinonaktifkan. Silakan hubungi Administrator TU.',
                ])->onlyInput('email');
            }

            // 4. Periksa apakah role pengguna dikenali oleh sistem
            $validRoles = ['admin_tu', 'guru', 'kepala_sekolah'];
            if (! in_array($user->role, $validRoles, true)) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect('/login')->withErrors([
                    'email' => 'Peran (role) akun Anda tidak dikenali oleh sistem. Hubungi Administrator.',
                ])->onlyInput('email');
            }

            // 5. Regenerasi session ID untuk keamanan (mencegah session fixation)
            $request->session()->regenerate();

            // 6. Arahkan pengguna ke dashboard sesuai role masing-masing
            if ($user->isAdminTu()) {
                return redirect('/admin/dashboard');
            }

            if ($user->isGuru()) {
                return redirect('/guru/dashboard');
            }

            if ($user->isKepalaSekolah()) {
                return redirect('/kepala-sekolah/dashboard');
            }

            // Fallback pengamanan jika kondisi di atas tidak terpenuhi
            return redirect('/login');
        }

        // 7. Jika login gagal, kembalikan pesan kesalahan umum tanpa membocorkan detail sensitif
        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    /**
     * Memproses logout pengguna.
     */
    public function logout(Request $request): RedirectResponse
    {
        // 1. Keluarkan pengguna dari autentikasi
        Auth::logout();

        // 2. Hapus data session
        $request->session()->invalidate();

        // 3. Buat ulang token CSRF
        $request->session()->regenerateToken();

        // 4. Arahkan kembali ke halaman login
        return redirect('/login')->with('success', 'Anda telah berhasil keluar dari sistem.');
    }
}
