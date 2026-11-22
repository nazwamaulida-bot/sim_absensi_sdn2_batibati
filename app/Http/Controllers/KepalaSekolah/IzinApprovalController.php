<?php

namespace App\Http\Controllers\KepalaSekolah;

use App\Http\Controllers\Controller;
use App\Models\AbsensiGuru;
use App\Models\PengajuanIzin;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class IzinApprovalController extends Controller
{
    /**
     * Menampilkan daftar seluruh pengajuan izin guru (menunggu, disetujui, ditolak).
     */
    public function index(Request $request): View
    {
        $status = $request->query('status');

        $pengajuans = PengajuanIzin::query()
            ->when($status, fn($q, $status) => $q->where('status', $status))
            ->with(['guru', 'verifikator'])
            ->orderByRaw("FIELD(status, 'menunggu', 'disetujui', 'ditolak')")
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('kepala_sekolah.izin.index', compact('pengajuans', 'status'));
    }

    /**
     * Memproses persetujuan (approve) atau penolakan (reject) izin guru.
     */
    public function verify(Request $request, PengajuanIzin $pengajuanIzin): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:disetujui,ditolak'],
            'catatan_kepsek' => ['nullable', 'string', 'max:500'],
        ]);

        $pengajuanIzin->update([
            'status' => $validated['status'],
            'catatan_kepsek' => $validated['catatan_kepsek'],
            'diverifikasi_oleh' => Auth::id(),
        ]);

        // Jika disetujui, otomatis sinkronisasikan ke tabel absensi_guru untuk rentang tanggal tersebut
        if ($validated['status'] === 'disetujui') {
            $period = CarbonPeriod::create($pengajuanIzin->tanggal_mulai, $pengajuanIzin->tanggal_selesai);

            foreach ($period as $date) {
                AbsensiGuru::updateOrCreate(
                    [
                        'guru_id' => $pengajuanIzin->guru_id,
                        'tanggal' => $date->format('Y-m-d'),
                    ],
                    [
                        'status_kehadiran' => $pengajuanIzin->jenis, // 'izin' atau 'sakit'
                        'keterangan' => 'Disetujui Kepala Sekolah: ' . $pengajuanIzin->keterangan,
                        'wajah_terverifikasi' => false,
                    ]
                );
            }
        }

        $pesan = $validated['status'] === 'disetujui' ? 'Permohonan izin guru berhasil disetujui.' : 'Permohonan izin guru telah ditolak.';

        return redirect()->route('kepala_sekolah.izin.index')->with('success', $pesan);
    }
}
