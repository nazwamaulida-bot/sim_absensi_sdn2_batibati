<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\AbsensiGuru;
use App\Models\JadwalPelajaran;
use App\Models\PengajuanIzin;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard profil guru, status absensi hari ini, dan ringkasan tugas.
     */
    public function index(): View
    {
        $user = Auth::user();
        $guru = $user->guru;
        $today = Carbon::today()->format('Y-m-d');
        $namaHariIni = Carbon::now()->locale('id')->isoFormat('dddd');

        // Status absensi guru hari ini
        $absensiHariIni = $guru ? AbsensiGuru::where('guru_id', $guru->id)->where('tanggal', $today)->first() : null;

        // Jadwal mengajar hari ini
        $jadwalHariIni = $guru ? JadwalPelajaran::where('guru_id', $guru->id)
            ->where('hari', $namaHariIni)
            ->with('kelas')
            ->orderBy('jam_mulai', 'asc')
            ->get() : collect();

        // Riwayat pengajuan izin terakhir
        $izinTerakhir = $guru ? PengajuanIzin::where('guru_id', $guru->id)->orderBy('created_at', 'desc')->take(3)->get() : collect();

        // Data tugas tambahan Wali Kelas (jika ada)
        $infoWaliKelas = null;
        $siswaPeringatanRendah = collect();

        if ($user->isWaliKelas()) {
            $tahunAjaranAktif = TahunAjaran::where('status_aktif', true)->first();
            $penugasanWali = $guru->waliKelas()
                ->when($tahunAjaranAktif, fn($q) => $q->where('tahun_ajaran_id', $tahunAjaranAktif->id))
                ->with('kelas.siswa.absensiSiswa')
                ->first();

            if ($penugasanWali && $penugasanWali->kelas) {
                $kelas = $penugasanWali->kelas;
                $siswas = $kelas->siswa;

                // Hitung siswa dengan kehadiran < 75% (Aturan IF-THEN)
                $siswaPeringatanRendah = $siswas->map(function ($s) {
                    $total = $s->absensiSiswa->count();
                    $hadir = $s->absensiSiswa->where('status_kehadiran', 'hadir')->count();
                    $pct = $total > 0 ? round(($hadir / $total) * 100, 1) : 100.0;
                    $s->persentase_kehadiran = $pct;
                    $s->total_pertemuan = $total;
                    return $s;
                })->filter(function ($s) {
                    return $s->total_pertemuan > 0 && $s->persentase_kehadiran < config('school.ambang_kehadiran_rendah', 75.0);
                });

                $infoWaliKelas = [
                    'nama_kelas' => $kelas->nama_kelas,
                    'total_siswa' => $siswas->count(),
                    'total_peringatan' => $siswaPeringatanRendah->count(),
                ];
            }
        }

        return view('guru.dashboard', compact(
            'guru',
            'absensiHariIni',
            'jadwalHariIni',
            'izinTerakhir',
            'infoWaliKelas',
            'siswaPeringatanRendah'
        ));
    }

    /**
     * Menampilkan jadwal mengajar lengkap guru untuk seluruh hari.
     */
    public function jadwal(): View
    {
        $guru = Auth::user()->guru;

        $jadwals = $guru ? JadwalPelajaran::where('guru_id', $guru->id)
            ->with('kelas')
            ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')")
            ->orderBy('jam_mulai', 'asc')
            ->get() : collect();

        // Kelompokkan berdasarkan hari
        $jadwalPerHari = $jadwals->groupBy('hari');

        return view('guru.jadwal.index', compact('guru', 'jadwals', 'jadwalPerHari'));
    }
}
