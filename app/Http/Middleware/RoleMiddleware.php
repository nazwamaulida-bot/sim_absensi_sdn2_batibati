<?php

namespace App\Http\Middleware;

use App\Models\TahunAjaran;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Menangani request yang masuk dan memvalidasi hak akses (role) pengguna.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // 1. Pastikan pengguna sudah login
        if (! Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            return redirect()->guest('/login')->with('error', 'Silakan login terlebih dahulu untuk mengakses halaman ini.');
        }

        $user = Auth::user();

        // 2. Pastikan akun pengguna berstatus aktif
        if (! $user->isAktif()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Akun Anda telah dinonaktifkan.'], 403);
            }

            return redirect('/login')->with('error', 'Akun Anda tidak aktif. Silakan hubungi Administrator TU.');
        }

        // Jika tidak ada role yang dispesifikasikan, izinkan lewat
        if (empty($roles)) {
            return $next($request);
        }

        // Normalisasi daftar role yang diperbolehkan
        $allowedRoles = [];
        foreach ($roles as $roleItem) {
            foreach (explode(',', $roleItem) as $role) {
                $trimmed = trim($role);
                if ($trimmed !== '') {
                    $allowedRoles[] = $trimmed;
                }
            }
        }

        // 3. Periksa kecocokan role pengguna
        foreach ($allowedRoles as $role) {
            if ($role === 'admin_tu' && $user->isAdminTu()) {
                return $next($request);
            }

            if ($role === 'guru' && $user->isGuru()) {
                return $next($request);
            }

            if ($role === 'kepala_sekolah' && $user->isKepalaSekolah()) {
                return $next($request);
            }

            // Khusus wali_kelas: periksa penugasan pada tahun ajaran yang sedang aktif
            if ($role === 'wali_kelas') {
                $tahunAjaranAktifId = TahunAjaran::where('status_aktif', true)->value('id');

                if ($tahunAjaranAktifId && $user->isWaliKelas($tahunAjaranAktifId)) {
                    return $next($request);
                }
            }
        }

        // 4. Jika pengguna tidak memiliki hak akses yang sesuai, tolak akses (HTTP 403 Forbidden)
        abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman ini.');
    }
}
