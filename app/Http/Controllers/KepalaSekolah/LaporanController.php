<?php

namespace App\Http\Controllers\KepalaSekolah;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use Illuminate\View\View;

class LaporanController extends Controller
{
    /**
     * Menampilkan daftar arsip laporan dan tautan Google Drive untuk Kepala Sekolah.
     */
    public function index(): View
    {
        $laporans = Laporan::with(['pembuat', 'semester.tahunAjaran', 'kelas'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('kepala_sekolah.laporan.index', compact('laporans'));
    }
}
