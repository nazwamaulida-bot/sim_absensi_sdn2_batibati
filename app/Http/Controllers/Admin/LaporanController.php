<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AbsensiGuru;
use App\Models\AbsensiSiswa;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Laporan;
use App\Models\Semester;
use App\Models\TahunAjaran;
use App\Services\GoogleDriveService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LaporanController extends Controller
{
    /**
     * Menampilkan halaman filter dan arsip laporan.
     */
    public function index(Request $request): View
    {
        $semesters = Semester::with('tahunAjaran')->get();
        $kelasList = Kelas::orderBy('tingkat', 'asc')->get();

        $laporans = Laporan::with(['pembuat', 'semester.tahunAjaran', 'kelas'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.laporan.index', compact('laporans', 'semesters', 'kelasList'));
    }

    /**
     * Menampilkan pratinjau (preview) cetak laporan yang ramah cetak (print/PDF).
     */
    public function cetak(Request $request, ?Laporan $laporan = null): View
    {
        if ($laporan && $laporan->exists) {
            $jenis = $laporan->jenis_laporan;
            $mulai = $laporan->periode_mulai;
            $selesai = $laporan->periode_selesai;
            $semester = $laporan->semester;
            $kelas = $laporan->kelas;
        } else {
            $request->validate([
                'jenis_laporan' => ['required', 'in:absensi_guru,absensi_siswa'],
                'periode_mulai' => ['required', 'date'],
                'periode_selesai' => ['required', 'date', 'after_or_equal:periode_mulai'],
                'semester_id' => ['required', 'exists:semester,id'],
                'kelas_id' => ['nullable', 'exists:kelas,id'],
            ]);

            $jenis = $request->jenis_laporan;
            $mulai = $request->periode_mulai;
            $selesai = $request->periode_selesai;
            $semester = Semester::with('tahunAjaran')->findOrFail($request->semester_id);
            $kelas = $request->kelas_id ? Kelas::find($request->kelas_id) : null;
        }

        if ($jenis === 'absensi_guru') {
            $data = AbsensiGuru::with('guru')
                ->whereBetween('tanggal', [$mulai, $selesai])
                ->orderBy('tanggal', 'asc')
                ->get();
        } else {
            $data = AbsensiSiswa::with(['siswa', 'jadwalPelajaran'])
                ->whereBetween('tanggal', [$mulai, $selesai])
                ->when($kelas, function ($q, $kelas) {
                    $q->whereHas('siswa', fn($sub) => $sub->where('kelas_id', $kelas->id));
                })
                ->orderBy('tanggal', 'asc')
                ->get();
        }

        return view('admin.laporan.cetak', compact('jenis', 'mulai', 'selesai', 'semester', 'kelas', 'data'));
    }

    /**
     * Mengarsipkan laporan dan mensinkronisasikannya ke Google Drive.
     */
    public function arsipkan(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'jenis_laporan' => ['required', 'string'],
            'periode_mulai' => ['required', 'date'],
            'periode_selesai' => ['required', 'date'],
            'semester_id' => ['required', 'exists:semester,id'],
            'kelas_id' => ['nullable', 'exists:kelas,id'],
        ]);

        $semester = Semester::with('tahunAjaran')->findOrFail($validated['semester_id']);

        // 1. Simpan metadata laporan ke database lokal
        $laporan = Laporan::create([
            'dibuat_oleh' => Auth::id(),
            'semester_id' => $validated['semester_id'],
            'kelas_id' => $validated['kelas_id'] ?? null,
            'jenis_laporan' => $validated['jenis_laporan'],
            'periode_mulai' => $validated['periode_mulai'],
            'periode_selesai' => $validated['periode_selesai'],
            'file_pdf' => null,
            'drive_file_id' => null,
            'drive_url' => null,
        ]);

        // 2. Siapkan file laporan sementara untuk diunggah
        $fileName = sprintf(
            'Laporan_%s_%s_%s.pdf',
            strtoupper($validated['jenis_laporan']),
            $validated['periode_mulai'],
            $validated['periode_selesai']
        );

        $tempPath = storage_path('app/temp_' . time() . '.pdf');
        file_put_contents($tempPath, "%PDF-1.4 Laporan Absensi UPTD SDN 2 Bati-Bati {$fileName}");

        // 3. Sinkronisasi ke Google Drive
        $uploadResult = GoogleDriveService::uploadReport($tempPath, $fileName, [
            'tahun_ajaran' => $semester->tahunAjaran->nama_tahun_ajaran ?? '',
            'semester' => $semester->nama_semester,
            'jenis_laporan' => $validated['jenis_laporan'],
        ]);

        if (file_exists($tempPath)) {
            @unlink($tempPath);
        }

        if ($uploadResult['success']) {
            $laporan->update([
                'drive_file_id' => $uploadResult['drive_file_id'],
                'drive_url' => $uploadResult['drive_url'],
            ]);

            return redirect()->route('admin.laporan.index')->with('success', $uploadResult['message']);
        }

        return redirect()->route('admin.laporan.index')->with('error', $uploadResult['message']);
    }
}
