@extends('layouts.app')

@section('title', 'Manajemen Absensi Guru')
@section('page_title', 'Presensi Guru & Tenaga Pendidik')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div>
        <h3 class="text-lg font-bold text-slate-800">Rekapitulasi Kehadiran Guru</h3>
        <p class="text-xs text-slate-500">Pantau presensi guru berdasarkan jam kedatangan, radius GPS, dan verifikasi wajah.</p>
    </div>

    <!-- Filter Form -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('admin.absensi-guru.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div>
                <label class="block text-[11px] font-semibold text-slate-500 uppercase mb-1">Tanggal</label>
                <input type="date" name="tanggal" value="{{ $tanggal }}"
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-slate-500 uppercase mb-1">Pilih Guru</label>
                <select name="guru_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Guru</option>
                    @foreach ($gurus as $g)
                        <option value="{{ $g->id }}" {{ $guruId == $g->id ? 'selected' : '' }}>{{ $g->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-slate-500 uppercase mb-1">Status Kehadiran</label>
                <select name="status" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Status</option>
                    <option value="hadir" {{ $status == 'hadir' ? 'selected' : '' }}>Hadir Tepat Waktu</option>
                    <option value="terlambat" {{ $status == 'terlambat' ? 'selected' : '' }}>Terlambat (&gt;15m)</option>
                    <option value="izin" {{ $status == 'izin' ? 'selected' : '' }}>Izin</option>
                    <option value="sakit" {{ $status == 'sakit' ? 'selected' : '' }}>Sakit</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold transition">
                    Terapkan Filter
                </button>
                <a href="{{ route('admin.absensi-guru.index') }}" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-medium transition">Reset</a>
            </div>
        </form>
    </div>

    <!-- Tabel Data Absensi Guru -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="py-3 px-4">Tanggal</th>
                        <th class="py-3 px-4">Nama Guru</th>
                        <th class="py-3 px-4">Jam Masuk</th>
                        <th class="py-3 px-4">Jam Pulang</th>
                        <th class="py-3 px-4">Jarak GPS</th>
                        <th class="py-3 px-4">Verifikasi Wajah</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($absensis as $ab)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3 px-4 font-semibold text-slate-900">{{ date('d M Y', strtotime($ab->tanggal)) }}</td>
                            <td class="py-3 px-4 font-bold text-slate-800">{{ $ab->guru->nama ?? '-' }}</td>
                            <td class="py-3 px-4 font-mono">{{ $ab->jam_masuk ?? '-' }}</td>
                            <td class="py-3 px-4 font-mono">{{ $ab->jam_pulang ?? '-' }}</td>
                            <td class="py-3 px-4">
                                @if ($ab->jarak_meter)
                                    <span class="font-medium {{ $ab->jarak_meter <= 100 ? 'text-emerald-700' : 'text-rose-600' }}">
                                        {{ $ab->jarak_meter }} m
                                    </span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                @if ($ab->wajah_terverifikasi)
                                    <span class="inline-flex items-center gap-1 text-emerald-700 font-semibold text-[11px]">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Terverifikasi
                                    </span>
                                @else
                                    <span class="text-slate-400">Tidak ada foto</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold {{ $ab->status_kehadiran == 'hadir' ? 'bg-emerald-100 text-emerald-800' : ($ab->status_kehadiran == 'terlambat' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800') }}">
                                    {{ ucfirst($ab->status_kehadiran) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-slate-500">{{ $ab->keterangan ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-400">Tidak ada rekaman kehadiran guru yang sesuai filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($absensis->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $absensis->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
