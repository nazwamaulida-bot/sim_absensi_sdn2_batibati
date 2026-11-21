<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\AbsensiSiswa;
use App\Models\JadwalPelajaran;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class WaliKelasController extends Controller
{
    /**
     * Memeriksa dan mengambil penugasan wali kelas aktif dari guru login.
     */
    protected function getWaliKelasAssignment()
    {
        $guru = Auth::user()->guru;
        if (! $guru) {
            abort(403, 'Profil guru tidak ditemukan.');
        }

        $tahunAjaranAktif = TahunAjaran::where('status_aktif', true)->first();

        return $guru->waliKelas()
            ->when($tahunAjaranAktif, fn($q) => $q->where('tahun_ajaran_id', $tahunAjaranAktif->id))
            ->with(['kelas.siswa' => fn($q) => $q->orderBy('nama', 'asc')])
            ->first();
    }

    /**
     * Menampilkan daftar siswa di kelas yang diampu oleh Wali Kelas.
     */
    public function siswa(): View
    {
        $penugasan = $this->getWaliKelasAssignment();
        if (! $penugasan || ! $penugasan->kelas) {
            abort(403, 'Anda tidak memiliki penugasan sebagai Wali Kelas pada tahun ajaran aktif.');
        }

        $kelas = $penugasan->kelas;
        $siswas = $kelas->siswa;

        return view('guru.wali_kelas.siswa', compact('kelas', 'siswas'));
    }

    /**
     * Form pencatatan absensi siswa untuk kelas yang diampu berdasarkan jadwal pelajaran.
     */
    public function absensi(Request $request): View
    {
        $penugasan = $this->getWaliKelasAssignment();
        if (! $penugasan || ! $penugasan->kelas) {
            abort(403, 'Akses khusus Wali Kelas.');
        }

        $kelas = $penugasan->kelas;
        $tanggal = $request->query('tanggal', Carbon::today()->format('Y-m-d'));
        $jadwalId = $request->query('jadwal_id');

        // Ambil jadwal pelajaran untuk kelas ini
        $jadwals = JadwalPelajaran::where('kelas_id', $kelas->id)->with('guru')->get();
        $selectedJadwal = $jadwalId ? JadwalPelajaran::find($jadwalId) : $jadwals->first();

        // Ambil data siswa dan status absensi yang sudah tersimpan untuk tanggal & jadwal ini
        $siswas = $kelas->siswa;
        $absensiMap = [];

        if ($selectedJadwal) {
            $absensiMap = AbsensiSiswa::where('jadwal_id', $selectedJadwal->id)
                ->where('tanggal', $tanggal)
                ->pluck('status_kehadiran', 'siswa_id')
                ->toArray();
        }

        return view('guru.wali_kelas.absensi', compact('kelas', 'jadwals', 'selectedJadwal', 'siswas', 'tanggal', 'absensiMap'));
    }

    /**
     * Menyimpan input absensi seluruh siswa di kelas.
     */
    public function simpanAbsensi(Request $request): RedirectResponse
    {
        $request->validate([
            'jadwal_id' => ['required', 'exists:jadwal_pelajaran,id'],
            'tanggal' => ['required', 'date'],
            'kehadiran' => ['required', 'array'],
            'kehadiran.*' => ['in:hadir,izin,sakit,alpha'],
        ]);

        $jadwalId = $request->jadwal_id;
        $tanggal = $request->tanggal;
        $userId = Auth::id();

        foreach ($request->kehadiran as $siswaId => $status) {
            AbsensiSiswa::updateOrCreate(
                [
                    'siswa_id' => $siswaId,
                    'jadwal_id' => $jadwalId,
                    'tanggal' => $tanggal,
                ],
                [
                    'dicatat_oleh' => $userId,
                    'status_kehadiran' => $status,
                ]
            );
        }

        return redirect()->route('guru.wali-kelas.absensi', ['jadwal_id' => $jadwalId, 'tanggal' => $tanggal])
            ->with('success', 'Presensi siswa berhasil disimpan.');
    }

    /**
     * Menampilkan rekapitulasi kehadiran siswa kelas dan Aturan IF-THEN (< 75%).
     */
    public function rekap(): View
    {
        $penugasan = $this->getWaliKelasAssignment();
        if (! $penugasan || ! $penugasan->kelas) {
            abort(403, 'Akses khusus Wali Kelas.');
        }

        $kelas = $penugasan->kelas;
        $siswas = $kelas->siswa()->with('absensiSiswa')->get();
        $ambangBatas = config('school.ambang_kehadiran_rendah', 75.0);

        // Hitung persentase kehadiran masing-masing siswa dan tandai yang < 75%
        $rekapSiswa = $siswas->map(function ($siswa) use ($ambangBatas) {
            $total = $siswa->absensiSiswa->count();
            $hadir = $siswa->absensiSiswa->where('status_kehadiran', 'hadir')->count();
            $izin = $siswa->absensiSiswa->where('status_kehadiran', 'izin')->count();
            $sakit = $siswa->absensiSiswa->where('status_kehadiran', 'sakit')->count();
            $alpha = $siswa->absensiSiswa->where('status_kehadiran', 'alpha')->count();

            $persen = $total > 0 ? round(($hadir / $total) * 100, 1) : 100.0;
            $peringatanRendah = $total > 0 && $persen < $ambangBatas;

            return [
                'siswa' => $siswa,
                'total' => $total,
                'hadir' => $hadir,
                'izin' => $izin,
                'sakit' => $sakit,
                'alpha' => $alpha,
                'persentase' => $persen,
                'peringatan_rendah' => $peringatanRendah,
            ];
        });

        $totalPeringatan = $rekapSiswa->where('peringatan_rendah', true)->count();

        return view('guru.wali_kelas.rekap', compact('kelas', 'rekapSiswa', 'totalPeringatan', 'ambangBatas'));
    }
}
