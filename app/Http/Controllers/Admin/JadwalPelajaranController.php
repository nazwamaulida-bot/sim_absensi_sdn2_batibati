<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\Semester;
use App\Models\TahunAjaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JadwalPelajaranController extends Controller
{
    /**
     * Menampilkan daftar jadwal pelajaran dengan filter kelas dan hari.
     */
    public function index(Request $request): View
    {
        $kelasId = $request->query('kelas_id');
        $hari = $request->query('hari');

        $kelasList = Kelas::orderBy('tingkat', 'asc')->get();
        $gurus = Guru::orderBy('nama', 'asc')->get();
        $semesters = Semester::with('tahunAjaran')->get();

        $jadwals = JadwalPelajaran::query()
            ->when($kelasId, function ($q, $kelasId) {
                $q->where('kelas_id', $kelasId);
            })
            ->when($hari, function ($q, $hari) {
                $q->where('hari', $hari);
            })
            ->with(['guru', 'guruPengganti', 'kelas', 'semester.tahunAjaran'])
            ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')")
            ->orderBy('jam_mulai', 'asc')
            ->get();

        return view('admin.jadwal.index', compact('jadwals', 'kelasList', 'gurus', 'semesters', 'kelasId', 'hari'));
    }

    /**
     * Menyimpan jadwal pelajaran baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kelas_id' => ['required', 'exists:kelas,id'],
            'guru_id' => ['required', 'exists:guru,id'],
            'guru_pengganti_id' => ['nullable', 'exists:guru,id', 'different:guru_id'],
            'semester_id' => ['required', 'exists:semester,id'],
            'mata_pelajaran' => ['required', 'string', 'max:100'],
            'hari' => ['required', 'in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu'],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
        ], [
            'mata_pelajaran.required' => 'Mata pelajaran wajib diisi.',
            'guru_pengganti_id.different' => 'Guru pengganti tidak boleh sama dengan guru utama.',
            'jam_selesai.after' => 'Jam selesai harus lebih akhir dari jam mulai.',
        ]);

        JadwalPelajaran::create($validated);

        return redirect()->route('admin.jadwal.index')->with('success', 'Jadwal pelajaran berhasil ditambahkan.');
    }

    /**
     * Memperbarui jadwal pelajaran.
     */
    public function update(Request $request, JadwalPelajaran $jadwal): RedirectResponse
    {
        $validated = $request->validate([
            'kelas_id' => ['required', 'exists:kelas,id'],
            'guru_id' => ['required', 'exists:guru,id'],
            'guru_pengganti_id' => ['nullable', 'exists:guru,id', 'different:guru_id'],
            'semester_id' => ['required', 'exists:semester,id'],
            'mata_pelajaran' => ['required', 'string', 'max:100'],
            'hari' => ['required', 'in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu'],
            'jam_mulai' => ['required'],
            'jam_selesai' => ['required', 'after:jam_mulai'],
        ]);

        $jadwal->update($validated);

        return redirect()->route('admin.jadwal.index')->with('success', 'Jadwal pelajaran berhasil diperbarui.');
    }

    /**
     * Menghapus jadwal pelajaran.
     */
    public function destroy(JadwalPelajaran $jadwal): RedirectResponse
    {
        $jadwal->delete();

        return redirect()->route('admin.jadwal.index')->with('success', 'Jadwal pelajaran berhasil dihapus.');
    }
}
