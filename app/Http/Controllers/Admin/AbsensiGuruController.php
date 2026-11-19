<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AbsensiGuru;
use App\Models\Guru;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AbsensiGuruController extends Controller
{
    /**
     * Menampilkan daftar kehadiran guru lengkap dengan geolokasi dan status keterlambatan.
     */
    public function index(Request $request): View
    {
        $tanggal = $request->query('tanggal', Carbon::today()->format('Y-m-d'));
        $guruId = $request->query('guru_id');
        $status = $request->query('status');

        $gurus = Guru::orderBy('nama', 'asc')->get();

        $absensis = AbsensiGuru::query()
            ->when($tanggal, function ($q, $tanggal) {
                $q->where('tanggal', $tanggal);
            })
            ->when($guruId, function ($q, $guruId) {
                $q->where('guru_id', $guruId);
            })
            ->when($status, function ($q, $status) {
                $q->where('status_kehadiran', $status);
            })
            ->with('guru')
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.absensi_guru.index', compact('absensis', 'gurus', 'tanggal', 'guruId', 'status'));
    }
}
