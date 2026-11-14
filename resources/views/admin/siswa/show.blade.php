@extends('layouts.app')

@section('title', 'Detail Siswa - ' . $siswa->nama)
@section('page_title', 'Detail Siswa')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-base font-bold text-slate-900">{{ $siswa->nama }}</h3>
            <p class="text-xs text-slate-500">NIS: {{ $siswa->nis }} • Kelas: {{ $siswa->kelas->nama_kelas ?? '-' }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.siswa.edit', $siswa->id) }}" class="px-3.5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold shadow-sm transition">Edit Siswa</a>
            <a href="{{ route('admin.siswa.index') }}" class="px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">&larr; Kembali</a>
        </div>
    </div>

    <!-- Statistik Kehadiran Siswa & Aturan Peringatan < 75% -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <span class="text-xs text-slate-500 block font-medium">Persentase Kehadiran</span>
            <span class="text-2xl font-bold mt-1 block {{ $persentase < 75 ? 'text-rose-600' : 'text-emerald-600' }}">
                {{ $persentase }}%
            </span>
            @if ($persentase < 75 && $totalAbsensi > 0)
                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-rose-100 text-rose-800 mt-1">
                    Peringatan: Di Bawah 75%
                </span>
            @else
                <span class="text-xs text-slate-400">Tingkat kehadiran normal</span>
            @endif
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <span class="text-xs text-slate-500 block font-medium">Total Hadir</span>
            <span class="text-2xl font-bold text-slate-800 mt-1 block">{{ $totalHadir }}</span>
            <span class="text-xs text-slate-400">Dari {{ $totalAbsensi }} pertemuan</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <span class="text-xs text-slate-500 block font-medium">Jenis Kelamin</span>
            <span class="text-sm font-bold text-slate-800 mt-1 block">{{ $siswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
            <span class="text-xs text-slate-400">Peserta didik</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <span class="text-xs text-slate-500 block font-medium">Status Siswa</span>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 mt-1 uppercase">
                {{ $siswa->status }}
            </span>
        </div>
    </div>

    <!-- Riwayat Kehadiran Siswa -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 font-bold text-xs uppercase tracking-wider text-slate-600">
            Riwayat Presensi Pelajaran
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-600 font-semibold">
                    <tr>
                        <th class="p-3">Tanggal</th>
                        <th class="p-3">Mata Pelajaran</th>
                        <th class="p-3">Guru Pengajar</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($siswa->absensiSiswa as $ab)
                        <tr>
                            <td class="p-3 font-semibold">{{ $ab->tanggal }}</td>
                            <td class="p-3">{{ $ab->jadwalPelajaran->mata_pelajaran ?? '-' }}</td>
                            <td class="p-3">{{ $ab->jadwalPelajaran->guru->nama ?? '-' }}</td>
                            <td class="p-3">
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold {{ $ab->status_kehadiran == 'hadir' ? 'bg-emerald-100 text-emerald-800' : ($ab->status_kehadiran == 'alpha' ? 'bg-rose-100 text-rose-800' : 'bg-blue-100 text-blue-800') }}">
                                    {{ ucfirst($ab->status_kehadiran) }}
                                </span>
                            </td>
                            <td class="p-3 text-slate-500">{{ $ab->keterangan ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-6 text-center text-slate-400">Belum ada data presensi untuk siswa ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
