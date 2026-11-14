<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SiswaController extends Controller
{
    /**
     * Menampilkan daftar siswa dengan pencarian dan filter kelas.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $kelasId = $request->query('kelas_id');

        $kelasList = Kelas::orderBy('tingkat', 'asc')->orderBy('nama_kelas', 'asc')->get();

        $siswas = Siswa::query()
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                        ->orWhere('nis', 'like', "%{$search}%");
                });
            })
            ->when($kelasId, function ($query, $kelasId) {
                $query->where('kelas_id', $kelasId);
            })
            ->with('kelas')
            ->orderBy('nama', 'asc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.siswa.index', compact('siswas', 'kelasList', 'search', 'kelasId'));
    }

    /**
     * Menampilkan form tambah siswa baru.
     */
    public function create(): View
    {
        $kelasList = Kelas::orderBy('tingkat', 'asc')->get();
        return view('admin.siswa.create', compact('kelasList'));
    }

    /**
     * Menyimpan data siswa baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nis' => ['required', 'string', 'max:30', 'unique:siswa,nis'],
            'nama' => ['required', 'string', 'max:255'],
            'nama_kelas' => ['nullable', 'string', 'max:50'],
            'kelas_id' => ['nullable'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'status' => ['required', 'string', 'in:aktif,lulus,pindah,nonaktif'],
        ], [
            'nis.required' => 'Nomor Induk Siswa (NIS) wajib diisi.',
            'nis.unique' => 'NIS ini sudah terdaftar.',
            'nama.required' => 'Nama siswa wajib diisi.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
        ]);

        $kelasId = $this->resolveKelasId($request);
        if (! $kelasId) {
            return back()->withErrors(['nama_kelas' => 'Kolom kelas wajib diisi.'])->withInput();
        }

        Siswa::create([
            'nis' => $validated['nis'],
            'nama' => $validated['nama'],
            'kelas_id' => $kelasId,
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'status' => $validated['status'],
        ]);

        return redirect()->route('admin.siswa.index')->with('success', 'Data siswa berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail profil dan riwayat absensi siswa.
     */
    public function show(Siswa $siswa): View
    {
        $siswa->load(['kelas', 'absensiSiswa.jadwalPelajaran']);

        // Hitung persentase kehadiran siswa
        $totalAbsensi = $siswa->absensiSiswa()->count();
        $totalHadir = $siswa->absensiSiswa()->where('status_kehadiran', 'hadir')->count();
        $persentase = $totalAbsensi > 0 ? round(($totalHadir / $totalAbsensi) * 100, 1) : 100.0;

        return view('admin.siswa.show', compact('siswa', 'totalAbsensi', 'totalHadir', 'persentase'));
    }

    /**
     * Menampilkan form edit siswa.
     */
    public function edit(Siswa $siswa): View
    {
        $kelasList = Kelas::orderBy('tingkat', 'asc')->get();
        return view('admin.siswa.edit', compact('siswa', 'kelasList'));
    }

    /**
     * Memperbarui data siswa.
     */
    public function update(Request $request, Siswa $siswa): RedirectResponse
    {
        $validated = $request->validate([
            'nis' => ['required', 'string', 'max:30', 'unique:siswa,nis,' . $siswa->id],
            'nama' => ['required', 'string', 'max:255'],
            'nama_kelas' => ['nullable', 'string', 'max:50'],
            'kelas_id' => ['nullable'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'status' => ['required', 'string', 'in:aktif,lulus,pindah,nonaktif'],
        ]);

        $kelasId = $this->resolveKelasId($request) ?? $siswa->kelas_id;

        $siswa->update([
            'nis' => $validated['nis'],
            'nama' => $validated['nama'],
            'kelas_id' => $kelasId,
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'status' => $validated['status'],
        ]);

        return redirect()->route('admin.siswa.index')->with('success', 'Data siswa berhasil diperbarui.');
    }

    /**
     * Menyelesaikan ID kelas dari input yang diketik (nama_kelas) atau dipilih (kelas_id).
     */
    protected function resolveKelasId(Request $request): ?int
    {
        if ($request->filled('kelas_id') && is_numeric($request->kelas_id)) {
            $existing = Kelas::find($request->kelas_id);
            if ($existing) {
                return $existing->id;
            }
        }

        $rawKelas = trim((string) $request->input('nama_kelas', ''));
        if ($rawKelas === '') {
            return null;
        }

        // Jika hanya angka 1-6 (misal user mengetik "1"), format otomatis menjadi "Kelas 1"
        if (is_numeric($rawKelas) && $rawKelas >= 1 && $rawKelas <= 6) {
            $namaKelas = 'Kelas ' . (int) $rawKelas;
            $tingkat = (int) $rawKelas;
        } else {
            $namaKelas = $rawKelas;
            preg_match('/[1-6]/', $namaKelas, $matches);
            $tingkat = !empty($matches[0]) ? (int) $matches[0] : 1;
        }

        $kelas = Kelas::firstOrCreate(
            ['nama_kelas' => $namaKelas],
            ['tingkat' => $tingkat]
        );

        return $kelas->id;
    }

    /**
     * Menghapus data siswa.
     */
    public function destroy(Siswa $siswa): RedirectResponse
    {
        $siswa->delete();

        return redirect()->route('admin.siswa.index')->with('success', 'Data siswa berhasil dihapus.');
    }
}
