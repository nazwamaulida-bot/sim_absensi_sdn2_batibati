@extends('layouts.app')

@section('title', 'Edit Data Siswa')
@section('page_title', 'Edit Data Siswa')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-base font-bold text-slate-900">Perbarui Data Siswa</h3>
            <p class="text-xs text-slate-500">Ubah identitas dan penempatan kelas siswa.</p>
        </div>
        <a href="{{ route('admin.siswa.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900">&larr; Batal & Kembali</a>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <form method="POST" action="{{ route('admin.siswa.update', $siswa->id) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="nis" class="block text-xs font-semibold text-slate-700 mb-1">Nomor Induk Siswa (NIS) *</label>
                    <input type="text" id="nis" name="nis" value="{{ old('nis', $siswa->nis) }}" required
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label for="nama_kelas" class="block text-xs font-semibold text-slate-700 mb-1">Kelas *</label>
                    <input type="text" id="nama_kelas" name="nama_kelas" list="kelasList" value="{{ old('nama_kelas', $siswa->kelas->nama_kelas ?? '') }}" required placeholder="Ketik kelas (Contoh: Kelas 1 atau 1)"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <datalist id="kelasList">
                        @foreach ($kelasList as $k)
                            <option value="{{ $k->nama_kelas }}">
                        @endforeach
                    </datalist>
                    <p class="text-[10px] text-slate-400 mt-1">Bisa diketik bebas (contoh: <strong>Kelas 1</strong> atau cukup ketik angka <strong>1</strong> sampai <strong>6</strong>).</p>
                    @error('nama_kelas')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="nama" class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap Siswa *</label>
                <input type="text" id="nama" name="nama" value="{{ old('nama', $siswa->nama) }}" required
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="jenis_kelamin" class="block text-xs font-semibold text-slate-700 mb-1">Jenis Kelamin *</label>
                    <select id="jenis_kelamin" name="jenis_kelamin" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="L" {{ old('jenis_kelamin', $siswa->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki (L)</option>
                        <option value="P" {{ old('jenis_kelamin', $siswa->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan (P)</option>
                    </select>
                </div>
                <div>
                    <label for="status" class="block text-xs font-semibold text-slate-700 mb-1">Status Keaktifan *</label>
                    <select id="status" name="status" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="aktif" {{ old('status', $siswa->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="lulus" {{ old('status', $siswa->status) == 'lulus' ? 'selected' : '' }}>Lulus</option>
                        <option value="pindah" {{ old('status', $siswa->status) == 'pindah' ? 'selected' : '' }}>Pindah / Mutasi</option>
                        <option value="nonaktif" {{ old('status', $siswa->status) == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
            </div>

            <div class="pt-4 flex justify-end gap-2 border-t border-slate-100">
                <a href="{{ route('admin.siswa.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-medium text-slate-600 hover:bg-slate-50 transition">Batal</a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-sm transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
