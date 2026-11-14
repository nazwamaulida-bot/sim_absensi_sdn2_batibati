<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\WaliKelas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KelasController extends Controller
{
    /**
     * Menampilkan daftar kelas beserta wali kelasnya pada tahun ajaran aktif.
     */
    public function index(): View
    {
        $tahunAjaranAktif = TahunAjaran::where('status_aktif', true)->first();

        $kelasList = Kelas::with(['siswa', 'waliKelas' => function ($q) use ($tahunAjaranAktif) {
            if ($tahunAjaranAktif) {
                $q->where('tahun_ajaran_id', $tahunAjaranAktif->id)->with('guru');
            }
        }])
        ->withCount('siswa')
        ->orderBy('tingkat', 'asc')
        ->orderBy('nama_kelas', 'asc')
        ->get();

        $gurus = Guru::orderBy('nama', 'asc')->get();

        return view('admin.kelas.index', compact('kelasList', 'gurus', 'tahunAjaranAktif'));
    }

    /**
     * Menyimpan kelas baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_kelas' => ['required', 'string', 'max:50'],
            'tingkat' => ['required', 'integer', 'between:1,6'],
        ], [
            'nama_kelas.required' => 'Nama kelas wajib diisi.',
            'tingkat.required' => 'Tingkat kelas wajib dipilih (1-6).',
        ]);

        Kelas::create($validated);

        return redirect()->route('admin.kelas.index')->with('success', 'Kelas baru berhasil ditambahkan.');
    }

    /**
     * Memperbarui informasi kelas.
     */
    public function update(Request $request, Kelas $kelas): RedirectResponse
    {
        $validated = $request->validate([
            'nama_kelas' => ['required', 'string', 'max:50'],
            'tingkat' => ['required', 'integer', 'between:1,6'],
        ]);

        $kelas->update($validated);

        return redirect()->route('admin.kelas.index')->with('success', 'Informasi kelas berhasil diperbarui.');
    }

    /**
     * Menghapus kelas jika belum memiliki data siswa terkait.
     */
    public function destroy(Kelas $kelas): RedirectResponse
    {
        if ($kelas->siswa()->count() > 0) {
            return back()->with('error', 'Kelas tidak dapat dihapus karena masih memiliki data siswa terdaftar.');
        }

        $kelas->delete();

        return redirect()->route('admin.kelas.index')->with('success', 'Kelas berhasil dihapus.');
    }

    /**
     * Menetapkan atau memperbarui Wali Kelas untuk kelas tertentu pada tahun ajaran aktif.
     */
    public function tetapkanWali(Request $request, Kelas $kelas): RedirectResponse
    {
        $validated = $request->validate([
            'guru_id' => ['required', 'exists:guru,id'],
            'tahun_ajaran_id' => ['required', 'exists:tahun_ajaran,id'],
        ]);

        WaliKelas::updateOrCreate(
            [
                'kelas_id' => $kelas->id,
                'tahun_ajaran_id' => $validated['tahun_ajaran_id'],
            ],
            [
                'guru_id' => $validated['guru_id'],
            ]
        );

        return redirect()->route('admin.kelas.index')->with('success', 'Wali kelas berhasil ditetapkan.');
    }
}
