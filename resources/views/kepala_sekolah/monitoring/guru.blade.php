@extends('layouts.app')

@section('title', 'Monitoring Keterlambatan Guru')
@section('page_title', 'Monitoring Kedisiplinan & Keterlambatan Guru')

@section('content')
<div class="space-y-6">

    <!-- Header & Filter Bulan -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                <h3 class="text-base font-bold text-slate-800">Evaluasi Aturan IF-THEN: Keterlambatan Masuk Guru</h3>
            </div>
            <p class="text-xs text-slate-500 mt-1">
                Aturan Kedisiplinan: Guru yang melakukan presensi masuk melebihi batas toleransi <strong>15 menit (setelah 07:45 WITA)</strong> secara otomatis tercatat berstatus <strong>Terlambat</strong>.
            </p>
        </div>

        <form method="GET" action="{{ route('kepala_sekolah.monitoring.guru') }}" class="flex items-center gap-2">
            <label for="bulan" class="text-xs font-semibold text-slate-600">Pilih Periode:</label>
            <input type="month" name="bulan" id="bulan" value="{{ $bulan }}" class="text-xs rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500">
            <button type="submit" class="px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold shadow-sm transition">
                Filter
            </button>
        </form>
    </div>

    <!-- Statistik Ringkas Bulan Terpilih -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Periode Aktif</span>
                <p class="text-lg font-bold text-slate-800 mt-0.5">
                    {{ Carbon\Carbon::parse($bulan . '-01')->translatedFormat('F Y') }}
                </p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Keterlambatan</span>
                <p class="text-2xl font-bold text-amber-600 mt-0.5">{{ $totalTerlambatBulanIni }} Kasus</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Jadwal Masuk Baku</span>
                <p class="text-lg font-bold text-slate-800 mt-0.5">07:30 WITA</p>
                <span class="text-[10px] text-slate-400">Toleransi s/d 07:45 WITA</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
    </div>

    <!-- Tabel Daftar Keterlambatan -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h4 class="text-sm font-bold text-slate-800">Daftar Guru Terlambat Periode {{ Carbon\Carbon::parse($bulan . '-01')->translatedFormat('F Y') }}</h4>
            <button onclick="window.print()" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                Cetak Lembar Monitoring
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-slate-100 text-slate-400 uppercase tracking-wider text-[10px] bg-slate-50/50">
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Nama Guru</th>
                        <th class="py-3 px-4">NIP</th>
                        <th class="py-3 px-4">Tanggal</th>
                        <th class="py-3 px-4">Jam Masuk</th>
                        <th class="py-3 px-4">Durasi Keterlambatan</th>
                        <th class="py-3 px-4">Radius GPS</th>
                        <th class="py-3 px-4 text-center">Verifikasi Wajah</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse ($absensiTerlambat as $idx => $item)
                        @php
                            $jamMasuk = Carbon\Carbon::parse($item->jam_masuk);
                            $jamBaku = Carbon\Carbon::parse('07:30:00');
                            $selisih = $jamMasuk->diffInMinutes($jamBaku);
                        @endphp
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-3.5 px-4 text-center text-slate-400 font-medium">{{ $absensiTerlambat->firstItem() + $idx }}</td>
                            <td class="py-3.5 px-4 font-bold text-slate-900">{{ $item->guru->nama ?? '-' }}</td>
                            <td class="py-3.5 px-4 font-mono text-slate-500">{{ $item->guru->nip ?? '-' }}</td>
                            <td class="py-3.5 px-4 whitespace-nowrap">{{ Carbon\Carbon::parse($item->tanggal)->translatedFormat('d M Y') }}</td>
                            <td class="py-3.5 px-4 font-bold text-amber-700">{{ $item->jam_masuk }} WITA</td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                    +{{ $selisih }} Menit
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-500">{{ $item->jarak_meter ?? 0 }} m</td>
                            <td class="py-3.5 px-4 text-center">
                                @if ($item->foto_masuk)
                                    <a href="{{ asset('storage/' . $item->foto_masuk) }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] text-indigo-600 hover:text-indigo-800 font-semibold">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        Foto
                                    </a>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-10 text-center text-slate-400">
                                Tidak ada catatan keterlambatan guru pada periode {{ Carbon\Carbon::parse($bulan . '-01')->translatedFormat('F Y') }}.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($absensiTerlambat->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $absensiTerlambat->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
