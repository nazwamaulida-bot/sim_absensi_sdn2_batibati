@extends('layouts.app')

@section('title', 'Manajemen Tahun Ajaran & Semester')
@section('page_title', 'Tahun Ajaran & Semester')

@section('content')
<div class="space-y-6">

    <!-- Header & Info -->
    <div>
        <h3 class="text-lg font-bold text-slate-800">Pengaturan Kalender Akademik</h3>
        <p class="text-xs text-slate-500">Tentukan tahun ajaran aktif dan kelola rentang tanggal semester.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Form Tambah Tahun Ajaran -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm h-fit">
            <h4 class="text-sm font-bold text-slate-900 mb-3 flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Tahun Ajaran Baru
            </h4>
            <form method="POST" action="{{ route('admin.tahun-ajaran.store') }}" class="space-y-3">
                @csrf
                <div>
                    <label for="nama_tahun_ajaran" class="block text-xs font-semibold text-slate-700 mb-1">Nama Tahun Ajaran *</label>
                    <input type="text" id="nama_tahun_ajaran" name="nama_tahun_ajaran" required placeholder="Contoh: 2024/2025"
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label for="tanggal_mulai" class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Mulai TA *</label>
                    <input type="date" id="tanggal_mulai" name="tanggal_mulai" required
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label for="tanggal_selesai" class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Selesai TA *</label>
                    <input type="date" id="tanggal_selesai" name="tanggal_selesai" required
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex items-center pt-1">
                    <input type="checkbox" id="status_aktif" name="status_aktif" value="1" class="h-4 w-4 text-blue-600 rounded">
                    <label for="status_aktif" class="ml-2 text-xs text-slate-700 select-none cursor-pointer">
                        Jadikan Tahun Ajaran Aktif
                    </label>
                </div>
                <button type="submit" class="w-full py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-sm transition">
                    Simpan Kalender Akademik
                </button>
            </form>
        </div>

        <!-- Tabel Tahun Ajaran & Semester -->
        <div class="lg:col-span-2 space-y-4">
            @forelse ($tahunAjarans as $ta)
                <div class="bg-white p-5 rounded-2xl border {{ $ta->status_aktif ? 'border-blue-400 ring-2 ring-blue-100' : 'border-slate-200' }} shadow-sm">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-slate-100 pb-3 mb-3">
                        <div class="flex items-center gap-3">
                            <span class="text-base font-bold text-slate-900">{{ $ta->nama_tahun_ajaran }}</span>
                            @if ($ta->status_aktif)
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 uppercase tracking-wider">
                                    Sedang Aktif
                                </span>
                            @else
                                <form method="POST" action="{{ route('admin.tahun-ajaran.set-aktif', $ta->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-xs font-semibold text-blue-600 hover:underline">Aktifkan TA Ini</button>
                                </form>
                            @endif
                        </div>
                        <span class="text-xs text-slate-500">
                            Periode: {{ date('d M Y', strtotime($ta->tanggal_mulai)) }} s/d {{ date('d M Y', strtotime($ta->tanggal_selesai)) }}
                        </span>
                    </div>

                    <!-- Semester di Tahun Ajaran Ini -->
                    <div>
                        <span class="text-xs font-semibold text-slate-700 block mb-2">Semester Terdaftar:</span>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach ($ta->semester as $sem)
                                <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between text-xs">
                                    <div>
                                        <span class="font-bold text-slate-800 block">Semester {{ $sem->nama_semester }}</span>
                                        <span class="text-[11px] text-slate-500">{{ date('d M Y', strtotime($sem->tanggal_mulai)) }} - {{ date('d M Y', strtotime($sem->tanggal_selesai)) }}</span>
                                    </div>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-medium bg-blue-100 text-blue-800">
                                        {{ $sem->nama_semester }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white p-8 rounded-2xl border border-slate-200 text-center text-slate-400 text-xs">
                    Belum ada data tahun ajaran. Silakan tambahkan pada form di samping.
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection
