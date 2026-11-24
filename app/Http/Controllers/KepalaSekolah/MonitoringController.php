<?php

namespace App\Http\Controllers\KepalaSekolah;

use App\Http\Controllers\Controller;
use App\Models\AbsensiGuru;
use App\Models\Kelas;
use App\Models\Siswa;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MonitoringController extends Controller
{
    /**
     * Monitoring khusus keterlambatan guru (> 15 menit dari jadwal masuk).
     */
    public function guru(Request $request): View
    {
        $bulan = $request->query('bulan', Carbon::today()->format('Y-m'));

        $absensiTerlambat = AbsensiGuru::where('status_kehadiran', 'terlambat')
            ->where('tanggal', 'like', "{$bulan}%")
            ->with('guru')
            ->orderBy('tanggal', 'desc')
            ->paginate(15)
            ->withQueryString();

        $totalTerlambatBulanIni = AbsensiGuru::where('status_kehadiran', 'terlambat')
            ->where('tanggal', 'like', "{$bulan}%")
            ->count();

        return view('kepala_sekolah.monitoring.guru', compact('absensiTerlambat', 'bulan', 'totalTerlambatBulanIni'));
    }

    /**
     * Monitoring khusus siswa dengan persentase kehadiran rendah (< 75%).
     */
    public function siswa(Request $request): View
    {
        $kelasId = $request->query('kelas_id');
        $ambangBatas = config('school.ambang_kehadiran_rendah', 75.0);

        $kelasList = Kelas::orderBy('tingkat', 'asc')->get();

        $siswaList = Siswa::query()
            ->where('status', 'aktif')
            ->when($kelasId, fn($q, $kelasId) => $q->where('kelas_id', $kelasId))
            ->with(['kelas', 'absensiSiswa'])
            ->get()
            ->map(function ($s) {
                $total = $s->absensiSiswa->count();
                $hadir = $s->absensiSiswa->where('status_kehadiran', 'hadir')->count();
                $izin = $s->absensiSiswa->where('status_kehadiran', 'izin')->count();
                $sakit = $s->absensiSiswa->where('status_kehadiran', 'sakit')->count();
                $alpha = $s->absensiSiswa->where('status_kehadiran', 'alpha')->count();

                $s->total_absen = $total;
                $s->total_hadir = $hadir;
                $s->total_izin = $izin;
                $s->total_sakit = $sakit;
                $s->total_alpha = $alpha;
                $s->persentase = $total > 0 ? round(($hadir / $total) * 100, 1) : 100.0;
                return $s;
            })
            ->filter(fn($s) => $s->total_absen > 0 && $s->persentase < $ambangBatas);

        return view('kepala_sekolah.monitoring.siswa', compact('siswaList', 'kelasList', 'kelasId', 'ambangBatas'));
    }
}
