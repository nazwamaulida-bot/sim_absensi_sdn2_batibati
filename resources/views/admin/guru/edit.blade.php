@extends('layouts.app')

@section('title', 'Edit Data Guru')
@section('page_title', 'Edit Data Guru')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-base font-bold text-slate-900">Perbarui Profil Guru</h3>
            <p class="text-xs text-slate-500">Ubah informasi identitas dan nomor kontak guru.</p>
        </div>
        <a href="{{ route('admin.guru.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900">&larr; Batal & Kembali</a>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <form method="POST" action="{{ route('admin.guru.update', $guru->id) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="nama" class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap & Gelar *</label>
                <input type="text" id="nama" name="nama" value="{{ old('nama', $guru->nama) }}" required
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label for="nip" class="block text-xs font-semibold text-slate-700 mb-1">NIP (Nomor Induk Pegawai)</label>
                <input type="text" id="nip" name="nip" value="{{ old('nip', $guru->nip) }}"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">Alamat Email *</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $guru->email) }}" required
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label for="no_hp" class="block text-xs font-semibold text-slate-700 mb-1">No. WhatsApp / HP</label>
                    <input type="text" id="no_hp" name="no_hp" value="{{ old('no_hp', $guru->no_hp) }}"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="pt-4 flex justify-end gap-2 border-t border-slate-100">
                <a href="{{ route('admin.guru.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-medium text-slate-600 hover:bg-slate-50 transition">Batal</a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-sm transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
