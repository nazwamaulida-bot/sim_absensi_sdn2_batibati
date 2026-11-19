@extends('layouts.app')

@section('title', 'Manajemen Absensi Siswa')
@section('page_title', 'Presensi Peserta Didik')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div>
        <h3 class="text-lg font-bold text-slate-800">Catatan Kehadiran Siswa per Jadwal Pelajaran</h3>
        <p class="text-xs text-slate-500">Rekapitulasi absensi harian yang dicatat oleh guru/wali kelas di setiap jam tatap muka.</p>
    </div>

    <!-- Filter Form -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('admin.absensi-siswa.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div>
                <label class="block text-[11px] font-semibold text-slate-500 uppercase mb-1">Tanggal</label>
                <input type="date" name="tanggal" value="{{ $tanggal }}"
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-slate-500 uppercase mb-1">Pilih Kelas</label>
                <select name="kelas_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Kelas</option>
                    @foreach ($kelasList as $k)
                        <option value="{{ $k->id }}" {{ $kelasId == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-slate-500 uppercase mb-1">Status Kehadiran</label>
                <select name="status" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Status</option>
                    <option value="hadir" {{ $status == 'hadir' ? 'selected' : '' }}>Hadir</option>
                    <option value="izin" {{ $status == 'izin' ? 'selected' : '' }}>Izin</option>
                    <option value="sakit" {{ $status == 'sakit' ? 'selected' : '' }}>Sakit</option>
                    <option value="alpha" {{ $status == 'alpha' ? 'selected' : '' }}>Alpha (Tanpa Keterangan)</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold transition">
                    Terapkan Filter
                </button>
                <a href="{{ route('admin.absensi-siswa.index') }}" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-medium transition">Reset</a>
            </div>
        </form>
    </div>

    <!-- Tabel Data Absensi Siswa -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="py-3 px-4">Tanggal</th>
                        <th class="py-3 px-4">NIS</th>
                        <th class="py-3 px-4">Nama Siswa</th>
                        <th class="py-3 px-4">Kelas</th>
                        <th class="py-3 px-4">Mata Pelajaran</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Dicatat Oleh</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($absensis as $ab)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-4 font-semibold text-slate-900">{{ date('d M Y', strtotime($ab->tanggal)) }}</td>
                            <td class="py-3.5 px-4 font-mono font-medium">{{ $ab->siswa->nis ?? '-' }}</td>
                            <td class="py-3.5 px-4 font-bold text-slate-800">{{ $ab->siswa->nama ?? '-' }}</td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded bg-blue-50 text-blue-800 font-medium">
                                    {{ $ab->siswa->kelas->nama_kelas ?? '-' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-700 font-medium">{{ $ab->jadwalPelajaran->mata_pelajaran ?? '-' }}</td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold {{ $ab->status_kehadiran == 'hadir' ? 'bg-emerald-100 text-emerald-800' : ($ab->status_kehadiran == 'alpha' ? 'bg-rose-100 text-rose-800' : 'bg-blue-100 text-blue-800') }}">
                                    {{ ucfirst($ab->status_kehadiran) }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-500">{{ $ab->pencatat->name ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">Tidak ada data kehadiran siswa yang sesuai dengan filter.</td>
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
