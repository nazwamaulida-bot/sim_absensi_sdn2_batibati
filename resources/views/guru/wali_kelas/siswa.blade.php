@extends('layouts.app')

@section('title', 'Daftar Siswa Kelas ' . ($kelas->nama_kelas ?? ''))
@section('page_title', 'Data Siswa Binaan Wali Kelas')

@section('content')
<div class="space-y-6">

    <!-- Header Kelas & Quick Stats -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-1 rounded-lg bg-amber-100 text-amber-800 font-bold text-xs uppercase tracking-wider">Wali Kelas</span>
                <h3 class="text-base font-bold text-slate-800">{{ $kelas->nama_kelas }}</h3>
            </div>
            <p class="text-xs text-slate-500 mt-1">Daftar siswa yang berada di bawah bimbingan Anda sebagai wali kelas pada tahun ajaran aktif.</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('guru.wali-kelas.absensi') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                Input Absensi Kelas
            </a>
            <a href="{{ route('guru.wali-kelas.rekap') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-semibold transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                Rekap Kehadiran
            </a>
        </div>
    </div>

    <!-- Ringkasan Gender Siswa -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase">Total Siswa</span>
                <p class="text-xl font-bold text-slate-900 mt-0.5">{{ $siswas->count() }} Orang</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase">Laki-Laki</span>
                <p class="text-xl font-bold text-slate-900 mt-0.5">{{ $siswas->where('jenis_kelamin', 'L')->count() }} Siswa</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center font-bold">
                L
            </div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase">Perempuan</span>
                <p class="text-xl font-bold text-slate-900 mt-0.5">{{ $siswas->where('jenis_kelamin', 'P')->count() }} Siswi</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold">
                P
            </div>
        </div>
    </div>

    <!-- Tabel Daftar Siswa -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h4 class="text-sm font-bold text-slate-800">Daftar Anggota Kelas</h4>
            <span class="text-xs text-slate-400">Urut berdasarkan nama abjad (A-Z)</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-slate-100 text-slate-400 uppercase tracking-wider text-[10px] bg-slate-50/50">
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Nama Siswa</th>
                        <th class="py-3 px-4">NIS / NISN</th>
                        <th class="py-3 px-4">L/P</th>
                        <th class="py-3 px-4">Tempat, Tanggal Lahir</th>
                        <th class="py-3 px-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse ($siswas as $index => $item)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-3 px-4 text-center font-medium text-slate-400">{{ $index + 1 }}</td>
                            <td class="py-3 px-4">
                                <span class="font-bold text-slate-900 block">{{ $item->nama }}</span>
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap text-slate-500">
                                <span class="block font-mono text-[11px]">{{ $item->nis ?? '-' }}</span>
                                <span class="block text-[10px] text-slate-400 font-mono">{{ $item->nisn ?? '-' }}</span>
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                @if ($item->jenis_kelamin == 'L')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-sky-100 text-sky-800">Laki-Laki</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-rose-100 text-rose-800">Perempuan</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-slate-500">
                                {{ $item->tempat_lahir ?? '-' }}, {{ $item->tanggal_lahir ? Carbon\Carbon::parse($item->tanggal_lahir)->translatedFormat('d F Y') : '-' }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-800 capitalize">
                                    {{ $item->status ?? 'Aktif' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">
                                Belum ada data siswa yang didaftarkan pada kelas ini. Silakan hubungi Admin TU.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
