<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AbsensiGuru;
use App\Models\AbsensiSiswa;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Siswa;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard utama Administrator TU dengan statistik lengkap.
     */
    public function index(): View
    {
        $today = Carbon::today()->format('Y-m-d');

        // 1. Statistik Total Master Data
        $totalGuru = Guru::count();
        $totalSiswa = Siswa::where('status', 'aktif')->count();
        $totalKelas = Kelas::count();

        // 2. Statistik Kehadiran Guru Hari Ini
        $absensiGuruHariIni = AbsensiGuru::where('tanggal', $today)->get();
        $guruHadir = $absensiGuruHariIni->where('status_kehadiran', 'hadir')->count();
        $guruTerlambat = $absensiGuruHariIni->where('status_kehadiran', 'terlambat')->count();
        $guruIzin = $absensiGuruHariIni->where('status_kehadiran', 'izin')->count();
        $guruSakit = $absensiGuruHariIni->where('status_kehadiran', 'sakit')->count();
        $guruBelumAbsen = max(0, $totalGuru - $absensiGuruHariIni->count());

        // 3. Statistik Kehadiran Siswa Hari Ini
        $absensiSiswaHariIni = AbsensiSiswa::where('tanggal', $today)->get();
        $siswaHadir = $absensiSiswaHariIni->where('status_kehadiran', 'hadir')->count();
        $siswaIzin = $absensiSiswaHariIni->where('status_kehadiran', 'izin')->count();
        $siswaSakit = $absensiSiswaHariIni->where('status_kehadiran', 'sakit')->count();
        $siswaAlpha = $absensiSiswaHariIni->where('status_kehadiran', 'alpha')->count();

        // 4. Data Absensi Terbaru
        $absensiGuruTerbaru = AbsensiGuru::with('guru')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $absensiSiswaTerbaru = AbsensiSiswa::with(['siswa', 'jadwalPelajaran.kelas'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // 5. Data Tren Kehadiran 7 Hari Terakhir untuk Grafik Chart.js
        $chartDates = [];
        $chartGuruHadir = [];
        $chartSiswaHadir = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i)->format('Y-m-d');
            $chartDates[] = Carbon::today()->subDays($i)->translatedFormat('d M');
            $chartGuruHadir[] = AbsensiGuru::where('tanggal', $date)->whereIn('status_kehadiran', ['hadir', 'terlambat'])->count();
            $chartSiswaHadir[] = AbsensiSiswa::where('tanggal', $date)->where('status_kehadiran', 'hadir')->count();
        }

        return view('admin.dashboard', compact(
            'totalGuru',
            'totalSiswa',
            'totalKelas',
            'guruHadir',
            'guruTerlambat',
            'guruIzin',
            'guruSakit',
            'guruBelumAbsen',
            'siswaHadir',
            'siswaIzin',
            'siswaSakit',
            'siswaAlpha',
            'absensiGuruTerbaru',
            'absensiSiswaTerbaru',
            'chartDates',
            'chartGuruHadir',
            'chartSiswaHadir'
        ));
    }
}
