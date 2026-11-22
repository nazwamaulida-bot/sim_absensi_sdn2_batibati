<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\PengajuanIzin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class IzinController extends Controller
{
    /**
     * Menampilkan daftar pengajuan izin/sakit guru dan form pengajuan baru.
     */
    public function index(): View
    {
        $guru = Auth::user()->guru;

        $pengajuans = $guru ? PengajuanIzin::where('guru_id', $guru->id)
            ->with('verifikator')
            ->orderBy('created_at', 'desc')
            ->paginate(10) : collect();

        return view('guru.izin.index', compact('pengajuans', 'guru'));
    }

    /**
     * Menyimpan permohonan izin/sakit baru ke database.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'jenis' => ['required', 'in:izin,sakit'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'keterangan' => ['required', 'string', 'max:1000'],
            'dokumen_pendukung' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
        ], [
            'jenis.required' => 'Pilih jenis permohonan (Izin atau Sakit).',
            'tanggal_mulai.required' => 'Tanggal mulai izin wajib diisi.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
            'keterangan.required' => 'Keterangan atau alasan izin wajib diisi.',
            'dokumen_pendukung.max' => 'Ukuran dokumen bukti maksimal 2MB.',
        ]);

        $guru = Auth::user()->guru;
        if (! $guru) {
            return back()->with('error', 'Akun Anda belum terhubung ke data Guru.');
        }

        $dokumenPath = null;
        if ($request->hasFile('dokumen_pendukung')) {
            $dokumenPath = $request->file('dokumen_pendukung')->store('dokumen_izin', 'public');
        }

        PengajuanIzin::create([
            'guru_id' => $guru->id,
            'jenis' => $validated['jenis'],
            'tanggal_mulai' => $validated['tanggal_mulai'],
            'tanggal_selesai' => $validated['tanggal_selesai'],
            'keterangan' => $validated['keterangan'],
            'dokumen_pendukung' => $dokumenPath,
            'status' => 'menunggu',
        ]);

        return redirect()->route('guru.izin.index')->with('success', 'Permohonan izin berhasil diajukan dan sedang menunggu persetujuan Kepala Sekolah.');
    }
}
