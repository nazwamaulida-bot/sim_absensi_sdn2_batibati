<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class GuruController extends Controller
{
    /**
     * Menampilkan daftar guru dengan pencarian dan pagination.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');

        $gurus = Guru::query()
            ->when($search, function ($query, $search) {
                $query->where('nama', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->with('user')
            ->orderBy('nama', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.guru.index', compact('gurus', 'search'));
    }

    /**
     * Menampilkan form tambah guru baru.
     */
    public function create(): View
    {
        return view('admin.guru.create');
    }

    /**
     * Menyimpan data guru baru dan membuatkan akun login secara otomatis.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nip' => ['nullable', 'string', 'max:30', 'unique:guru,nip'],
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:guru,email', 'unique:users,email'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'string', 'min:6'],
        ], [
            'nama.required' => 'Nama lengkap guru wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar di sistem. Gunakan alamat email lain.',
            'nip.unique' => 'NIP sudah digunakan.',
        ]);

        DB::transaction(function () use ($validated) {
            // Simpan data master guru
            $guru = Guru::create([
                'nip' => $validated['nip'],
                'nama' => $validated['nama'],
                'email' => $validated['email'],
                'no_hp' => $validated['no_hp'],
                'data_wajah' => null,
            ]);

            // Buat akun login untuk guru bersangkutan
            User::create([
                'name' => $validated['nama'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password'] ?: 'password123'),
                'role' => 'guru',
                'status' => 'aktif',
                'guru_id' => $guru->id,
            ]);
        });

        return redirect()->route('admin.guru.index')->with('success', 'Data guru dan akun login berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail lengkap profil guru.
     */
    public function show(Guru $guru): View
    {
        $guru->load(['user', 'waliKelas.kelas', 'jadwalPelajaran.kelas', 'absensiGuru' => function ($q) {
            $q->orderBy('tanggal', 'desc')->take(10);
        }]);

        return view('admin.guru.show', compact('guru'));
    }

    /**
     * Menampilkan form edit guru.
     */
    public function edit(Guru $guru): View
    {
        return view('admin.guru.edit', compact('guru'));
    }

    /**
     * Memperbarui data guru.
     */
    public function update(Request $request, Guru $guru): RedirectResponse
    {
        $userId = $guru->user?->id;

        $validated = $request->validate([
            'nip' => ['nullable', 'string', 'max:30', 'unique:guru,nip,' . $guru->id],
            'nama' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:guru,email,' . $guru->id,
                'unique:users,email,' . ($userId ?: 'NULL') . ',id',
            ],
            'no_hp' => ['nullable', 'string', 'max:20'],
        ], [
            'nama.required' => 'Nama lengkap guru wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar di sistem (digunakan oleh akun lain, misal Admin TU atau Kepala Sekolah). Gunakan email yang berbeda.',
            'nip.unique' => 'NIP sudah digunakan oleh guru lain.',
        ]);

        DB::transaction(function () use ($guru, $validated) {
            $guru->update($validated);

            // Update nama dan email pada akun user yang terhubung
            if ($guru->user) {
                $guru->user->update([
                    'name' => $validated['nama'],
                    'email' => $validated['email'],
                ]);
            }
        });

        return redirect()->route('admin.guru.index')->with('success', 'Data guru berhasil diperbarui.');
    }

    /**
     * Menghapus data guru beserta akun pengguna terkait.
     */
    public function destroy(Guru $guru): RedirectResponse
    {
        // Hapus akun user terlebih dahulu jika ada
        if ($guru->user) {
            $guru->user->delete();
        }

        $guru->delete();

        return redirect()->route('admin.guru.index')->with('success', 'Data guru berhasil dihapus.');
    }
}
