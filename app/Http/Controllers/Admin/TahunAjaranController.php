<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Semester;
use App\Models\TahunAjaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TahunAjaranController extends Controller
{
    /**
     * Menampilkan daftar tahun ajaran beserta semesternya.
     */
    public function index(): View
    {
        $tahunAjarans = TahunAjaran::with('semester')->orderBy('created_at', 'desc')->get();

        return view('admin.tahun_ajaran.index', compact('tahunAjarans'));
    }

    /**
     * Menyimpan data tahun ajaran baru dan membuat otomatis Semester Ganjil & Genap.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_tahun_ajaran' => ['required', 'string', 'max:20', 'unique:tahun_ajaran,nama_tahun_ajaran'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after:tanggal_mulai'],
            'status_aktif' => ['boolean'],
        ], [
            'nama_tahun_ajaran.required' => 'Nama tahun ajaran wajib diisi (contoh: 2024/2025).',
            'nama_tahun_ajaran.unique' => 'Nama tahun ajaran ini sudah ada.',
            'tanggal_selesai.after' => 'Tanggal selesai harus setelah tanggal mulai.',
        ]);

        $statusAktif = $request->boolean('status_aktif');

        // Jika disetel aktif, nonaktifkan tahun ajaran lainnya
        if ($statusAktif) {
            TahunAjaran::query()->update(['status_aktif' => false]);
        }

        $ta = TahunAjaran::create([
            'nama_tahun_ajaran' => $validated['nama_tahun_ajaran'],
            'tanggal_mulai' => $validated['tanggal_mulai'],
            'tanggal_selesai' => $validated['tanggal_selesai'],
            'status_aktif' => $statusAktif,
        ]);

        // Buat otomatis Semester Ganjil dan Genap
        Semester::create([
            'tahun_ajaran_id' => $ta->id,
            'nama_semester' => 'Ganjil',
            'tanggal_mulai' => $validated['tanggal_mulai'],
            'tanggal_selesai' => date('Y-12-31', strtotime($validated['tanggal_mulai'])),
        ]);

        Semester::create([
            'tahun_ajaran_id' => $ta->id,
            'nama_semester' => 'Genap',
            'tanggal_mulai' => date('Y-01-01', strtotime($validated['tanggal_selesai'])),
            'tanggal_selesai' => $validated['tanggal_selesai'],
        ]);

        return redirect()->route('admin.tahun-ajaran.index')->with('success', 'Tahun ajaran dan semester berhasil ditambahkan.');
    }

    /**
     * Menetapkan tahun ajaran tertentu sebagai tahun ajaran aktif.
     */
    public function setAktif(TahunAjaran $tahunAjaran): RedirectResponse
    {
        TahunAjaran::query()->update(['status_aktif' => false]);
        $tahunAjaran->update(['status_aktif' => true]);

        return redirect()->route('admin.tahun-ajaran.index')->with('success', "Tahun ajaran {$tahunAjaran->nama_tahun_ajaran} berhasil diaktifkan.");
    }

    /**
     * Memperbarui tahun ajaran.
     */
    public function update(Request $request, TahunAjaran $tahunAjaran): RedirectResponse
    {
        $validated = $request->validate([
            'nama_tahun_ajaran' => ['required', 'string', 'max:20', 'unique:tahun_ajaran,nama_tahun_ajaran,' . $tahunAjaran->id],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after:tanggal_mulai'],
        ]);

        $tahunAjaran->update($validated);

        return redirect()->route('admin.tahun-ajaran.index')->with('success', 'Data tahun ajaran berhasil diperbarui.');
    }

    /**
     * Menghapus tahun ajaran jika tidak terikat data krusial.
     */
    public function destroy(TahunAjaran $tahunAjaran): RedirectResponse
    {
        if ($tahunAjaran->status_aktif) {
            return back()->with('error', 'Tahun ajaran yang sedang aktif tidak dapat dihapus.');
        }

        $tahunAjaran->delete();

        return redirect()->route('admin.tahun-ajaran.index')->with('success', 'Tahun ajaran berhasil dihapus.');
    }
}
