@extends('layouts.app')

@section('title', 'Rekap Presensi - Kelas ' . ($kelas->nama_kelas ?? ''))
@section('page_title', 'Rekapitulasi Kehadiran Siswa & Sistem Peringatan Dini')

@section('content')
<div class="space-y-6">

    <!-- Header Rekap -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-1 rounded-lg bg-amber-100 text-amber-800 font-bold text-xs uppercase tracking-wider">Wali Kelas</span>
                <h3 class="text-base font-bold text-slate-800">Rekapitulasi Kehadiran {{ $kelas->nama_kelas }}</h3>
            </div>
            <p class="text-xs text-slate-500 mt-1">
                Aturan Evaluasi: Siswa dengan persentase kehadiran <strong>&lt; {{ $ambangBatas }}%</strong> secara otomatis ditandai untuk pembinaan wali kelas.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('guru.wali-kelas.absensi') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Input Presensi Baru
            </a>
            <button onclick="window.print()" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-semibold transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak Rekap
            </button>
        </div>
    </div>

    <!-- Banner Aturan IF-THEN: Peringatan Kehadiran Rendah (< 75%) -->
    @if ($totalPeringatan > 0)
        <div class="bg-rose-50 border-l-4 border-rose-500 p-5 rounded-2xl shadow-sm flex items-start gap-4">
            <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div class="flex-1">
                <h4 class="text-sm font-bold text-rose-900">
                    Peringatan Dini: Terdapat {{ $totalPeringatan }} Siswa Memiliki Kehadiran &lt; {{ $ambangBatas }}%
                </h4>
                <p class="text-xs text-rose-700 mt-1 leading-relaxed">
                    Berdasarkan Aturan Sistem, siswa dengan persentase kehadiran di bawah {{ $ambangBatas }}% memerlukan perhatian khusus dari Wali Kelas dan Guru BP/BK, serta pemanggilan orang tua/wali untuk evaluasi kedisiplinan.
                </p>
            </div>
        </div>
    @else
        <div class="bg-emerald-50 border border-emerald-200 p-4 rounded-2xl flex items-center gap-3 text-emerald-800">
            <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <p class="text-xs font-semibold">
                Kondisi Baik: Seluruh siswa di {{ $kelas->nama_kelas }} saat ini memenuhi ambang batas minimal kehadiran (&ge; {{ $ambangBatas }}%).
            </p>
        </div>
    @endif

    <!-- Tabel Rekapitulasi Kehadiran Siswa -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h4 class="text-sm font-bold text-slate-800">Statistik Kehadiran Seluruh Siswa</h4>
            <span class="text-xs text-slate-400">Total: {{ $rekapSiswa->count() }} Siswa</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-slate-100 text-slate-400 uppercase tracking-wider text-[10px] bg-slate-50/50">
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Nama Siswa</th>
                        <th class="py-3 px-4">NIS</th>
                        <th class="py-3 px-3 text-center">Total Sesi</th>
                        <th class="py-3 px-3 text-center text-emerald-600">Hadir</th>
                        <th class="py-3 px-3 text-center text-blue-600">Izin</th>
                        <th class="py-3 px-3 text-center text-amber-600">Sakit</th>
                        <th class="py-3 px-3 text-center text-rose-600">Alpha</th>
                        <th class="py-3 px-4 text-center">Persentase</th>
                        <th class="py-3 px-4 text-center">Status Evaluasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse ($rekapSiswa as $idx => $item)
                        <tr class="hover:bg-slate-50/50 transition {{ $item['peringatan_rendah'] ? 'bg-rose-50/30' : '' }}">
                            <td class="py-3 px-4 text-center text-slate-400 font-medium">{{ $idx + 1 }}</td>
                            <td class="py-3 px-4">
                                <span class="font-bold text-slate-900 block">{{ $item['siswa']->nama }}</span>
                                <span class="text-[10px] text-slate-400">{{ $item['siswa']->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                            </td>
                            <td class="py-3 px-4 font-mono text-slate-500">{{ $item['siswa']->nis ?? '-' }}</td>
                            <td class="py-3 px-3 text-center font-bold text-slate-700">{{ $item['total'] }}</td>
                            <td class="py-3 px-3 text-center font-bold text-emerald-600">{{ $item['hadir'] }}</td>
                            <td class="py-3 px-3 text-center font-bold text-blue-600">{{ $item['izin'] }}</td>
                            <td class="py-3 px-3 text-center font-bold text-amber-600">{{ $item['sakit'] }}</td>
                            <td class="py-3 px-3 text-center font-bold text-rose-600">{{ $item['alpha'] }}</td>
                            <td class="py-3 px-4 text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    <span class="font-bold {{ $item['peringatan_rendah'] ? 'text-rose-600' : 'text-slate-800' }}">
                                        {{ $item['persentase'] }}%
                                    </span>
                                </div>
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if ($item['peringatan_rendah'])
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 animate-pulse">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                        PERINGATAN &lt; {{ $ambangBatas }}%
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-800">
                                        Memenuhi Syarat
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="py-8 text-center text-slate-400">
                                Belum ada data presensi yang tercatat untuk kelas ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
