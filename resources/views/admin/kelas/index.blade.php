@extends('layouts.app')

@section('title', 'Manajemen Kelas & Wali Kelas')
@section('page_title', 'Manajemen Kelas & Wali Kelas')

@section('content')
<div class="space-y-6">

    <!-- Header & Info -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h3 class="text-lg font-bold text-slate-800">Daftar Rombongan Belajar (Kelas) & Wali Kelas</h3>
            <p class="text-xs text-slate-500">
                Tahun Ajaran Aktif: 
                <span class="font-bold text-blue-600">{{ $tahunAjaranAktif->nama_tahun_ajaran ?? 'Belum ada TA aktif' }}</span>
            </p>
        </div>
    </div>

    <!-- Layout Grid: Tambah Kelas & Daftar Kelas -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Form Tambah Kelas -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm h-fit">
            <h4 class="text-sm font-bold text-slate-900 mb-3 flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Kelas Baru
            </h4>
            <form method="POST" action="{{ route('admin.kelas.store') }}" class="space-y-3">
                @csrf
                <div>
                    <label for="nama_kelas" class="block text-xs font-semibold text-slate-700 mb-1">Nama Rombel / Kelas *</label>
                    <input type="text" id="nama_kelas" name="nama_kelas" required placeholder="Contoh: Kelas 1, Kelas 2"
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label for="tingkat" class="block text-xs font-semibold text-slate-700 mb-1">Tingkat Kelas (1-6) *</label>
                    <select id="tingkat" name="tingkat" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @for ($i = 1; $i <= 6; $i++)
                            <option value="{{ $i }}">Tingkat {{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <button type="submit" class="w-full py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-sm transition">
                    Simpan Kelas
                </button>
            </form>
        </div>

        <!-- Tabel Daftar Kelas & Pengaturan Wali -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-100 font-bold text-xs uppercase tracking-wider text-slate-600">
                Data Kelas & Penugasan Wali Kelas
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="py-3 px-4">Tingkat</th>
                            <th class="py-3 px-4">Nama Kelas</th>
                            <th class="py-3 px-4">Total Siswa</th>
                            <th class="py-3 px-4">Wali Kelas Aktif</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($kelasList as $kelas)
                            @php
                                $waliAktif = $kelas->waliKelas->first();
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-4 font-bold text-slate-900">Kelas {{ $kelas->tingkat }}</td>
                                <td class="py-3.5 px-4 font-semibold text-blue-700">{{ $kelas->nama_kelas }}</td>
                                <td class="py-3.5 px-4">{{ $kelas->siswa_count }} Siswa</td>
                                <td class="py-3.5 px-4">
                                    @if ($waliAktif && $waliAktif->guru)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 font-medium">
                                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            {{ $waliAktif->guru->nama }}
                                        </span>
                                    @else
                                        <span class="text-xs text-rose-500 italic">Belum ditentukan</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <!-- Modal/Form Tetapkan Wali -->
                                    @if ($tahunAjaranAktif)
                                        <form method="POST" action="{{ route('admin.kelas.tetapkan-wali', $kelas->id) }}" class="inline-flex items-center gap-1">
                                            @csrf
                                            <input type="hidden" name="tahun_ajaran_id" value="{{ $tahunAjaranAktif->id }}">
                                            <select name="guru_id" class="px-2 py-1 bg-slate-50 border border-slate-200 rounded-lg text-[11px] focus:outline-none focus:ring-1 focus:ring-blue-500">
                                                <option value="">-- Tetapkan Wali --</option>
                                                @foreach ($gurus as $g)
                                                    <option value="{{ $g->id }}" {{ ($waliAktif && $waliAktif->guru_id == $g->id) ? 'selected' : '' }}>
                                                        {{ $g->nama }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="p-1 rounded bg-blue-50 text-blue-600 hover:bg-blue-100 transition" title="Simpan Wali">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Hapus Kelas -->
                                    <form method="POST" action="{{ route('admin.kelas.destroy', $kelas->id) }}" onsubmit="return confirm('Hapus kelas ini?');" class="inline ml-1">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 rounded text-rose-600 hover:bg-rose-50 transition" title="Hapus Kelas">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-400">Belum ada kelas yang dibuat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>
@endsection
