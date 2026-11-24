<?php

namespace App\Http\Controllers\KepalaSekolah;

use App\Http\Controllers\Controller;
use App\Models\AbsensiGuru;
use App\Models\AbsensiSiswa;
use App\Models\Guru;
use App\Models\PengajuanIzin;
use App\Models\Siswa;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard monitoring eksekutif Kepala Sekolah.
     */
    public function index(): View
    {
        $today = Carbon::today()->format('Y-m-d');

        $totalGuru = Guru::count();
        $totalSiswa = Siswa::where('status', 'aktif')->count();

        // Kehadiran Guru Hari Ini
        $absensiGuruHariIni = AbsensiGuru::where('tanggal', $today)->get();
        $guruHadir = $absensiGuruHariIni->where('status_kehadiran', 'hadir')->count();
        $guruTerlambat = $absensiGuruHariIni->where('status_kehadiran', 'terlambat')->count();
        $guruIzin = $absensiGuruHariIni->where('status_kehadiran', 'izin')->count();
        $guruSakit = $absensiGuruHariIni->where('status_kehadiran', 'sakit')->count();

        // Kehadiran Siswa Hari Ini
        $absensiSiswaHariIni = AbsensiSiswa::where('tanggal', $today)->get();
        $siswaHadir = $absensiSiswaHariIni->where('status_kehadiran', 'hadir')->count();
        $siswaIzin = $absensiSiswaHariIni->where('status_kehadiran', 'izin')->count();
        $siswaSakit = $absensiSiswaHariIni->where('status_kehadiran', 'sakit')->count();
        $siswaAlpha = $absensiSiswaHariIni->where('status_kehadiran', 'alpha')->count();

        // 1. ATURAN IF-THEN: Guru Terlambat (> 15 Menit) Hari Ini
        $daftarGuruTerlambat = AbsensiGuru::where('tanggal', $today)
            ->where('status_kehadiran', 'terlambat')
            ->with('guru')
            ->get();

        // 2. ATURAN IF-THEN: Siswa dengan Tingkat Kehadiran Rendah (< 75%)
        $ambangBatas = config('school.ambang_kehadiran_rendah', 75.0);
        $siswaPeringatan = Siswa::with('kelas', 'absensiSiswa')
            ->where('status', 'aktif')
            ->get()
            ->map(function ($s) {
                $total = $s->absensiSiswa->count();
                $hadir = $s->absensiSiswa->where('status_kehadiran', 'hadir')->count();
                $s->persentase = $total > 0 ? round(($hadir / $total) * 100, 1) : 100.0;
                $s->total_absen = $total;
                return $s;
            })
            ->filter(fn($s) => $s->total_absen > 0 && $s->persentase < $ambangBatas);

        // 3. Pengajuan Izin Guru yang Menunggu Persetujuan
        $izinMenunggu = PengajuanIzin::where('status', 'menunggu')->with('guru')->get();

        // 4. Data Tren untuk Grafik 7 Hari Terakhir
        $chartDates = [];
        $chartGuruHadir = [];
        $chartSiswaHadir = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i)->format('Y-m-d');
            $chartDates[] = Carbon::today()->subDays($i)->translatedFormat('d M');
            $chartGuruHadir[] = AbsensiGuru::where('tanggal', $date)->whereIn('status_kehadiran', ['hadir', 'terlambat'])->count();
            $chartSiswaHadir[] = AbsensiSiswa::where('tanggal', $date)->where('status_kehadiran', 'hadir')->count();
        }

        return view('kepala_sekolah.dashboard', compact(
            'totalGuru',
            'totalSiswa',
            'guruHadir',
            'guruTerlambat',
            'guruIzin',
            'guruSakit',
            'siswaHadir',
            'siswaIzin',
            'siswaSakit',
            'siswaAlpha',
            'daftarGuruTerlambat',
            'siswaPeringatan',
            'izinMenunggu',
            'chartDates',
            'chartGuruHadir',
            'chartSiswaHadir'
        ));
    }
}
