<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AbsensiSiswa;
use App\Models\Kelas;
use App\Models\Semester;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AbsensiSiswaController extends Controller
{
    /**
     * Menampilkan rekapitulasi data kehadiran siswa.
     */
    public function index(Request $request): View
    {
        $tanggal = $request->query('tanggal', Carbon::today()->format('Y-m-d'));
        $kelasId = $request->query('kelas_id');
        $status = $request->query('status');

        $kelasList = Kelas::orderBy('tingkat', 'asc')->get();

        $absensis = AbsensiSiswa::query()
            ->when($tanggal, function ($q, $tanggal) {
                $q->where('tanggal', $tanggal);
            })
            ->when($kelasId, function ($q, $kelasId) {
                $q->whereHas('siswa', function ($query) use ($kelasId) {
                    $query->where('kelas_id', $kelasId);
                });
            })
            ->when($status, function ($q, $status) {
                $q->where('status_kehadiran', $status);
            })
            ->with(['siswa.kelas', 'jadwalPelajaran.guru', 'pencatat'])
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.absensi_siswa.index', compact('absensis', 'kelasList', 'tanggal', 'kelasId', 'status'));
    }
}
