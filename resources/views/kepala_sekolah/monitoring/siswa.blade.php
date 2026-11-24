@extends('layouts.app')

@section('title', 'Monitoring Siswa < 75% Kehadiran')
@section('page_title', 'Sistem Peringatan Dini Kehadiran Rendah Siswa')

@section('content')
<div class="space-y-6">

    <!-- Header & Filter Kelas -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                <h3 class="text-base font-bold text-slate-800">Evaluasi Aturan IF-THEN: Kehadiran Siswa &lt; {{ $ambangBatas }}%</h3>
            </div>
            <p class="text-xs text-slate-500 mt-1">
                Sistem otomatis memfilter siswa yang tingkat kehadirannya di bawah ambang batas minimal {{ $ambangBatas }}% untuk evaluasi akademik dan kedisiplinan.
            </p>
        </div>

        <form method="GET" action="{{ route('kepala_sekolah.monitoring.siswa') }}" class="flex items-center gap-2">
            <label for="kelas_id" class="text-xs font-semibold text-slate-600">Filter Kelas:</label>
            <select name="kelas_id" id="kelas_id" class="text-xs rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">Semua Kelas</option>
                @foreach ($kelasList as $kelas)
                    <option value="{{ $kelas->id }}" {{ $kelasId == $kelas->id ? 'selected' : '' }}>
                        {{ $kelas->nama_kelas }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold shadow-sm transition">
                Filter
            </button>
        </form>
    </div>

    <!-- Alert Box: Ringkasan Hasil Evaluasi Aturan IF-THEN -->
    @if ($siswaList->count() > 0)
        <div class="bg-rose-50 border-l-4 border-rose-500 p-5 rounded-2xl shadow-sm flex items-start gap-4">
            <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div class="flex-1">
                <h4 class="text-sm font-bold text-rose-900">
                    Ditemukan {{ $siswaList->count() }} Siswa Membutuhkan Tindak Lanjut Khusus
                </h4>
                <p class="text-xs text-rose-700 mt-1 leading-relaxed">
                    Siswa-siswa di bawah ini memiliki rekapitulasi kehadiran di bawah {{ $ambangBatas }}%. Kepala Sekolah dapat menginstruksikan Wali Kelas dan Guru Bimbingan untuk melayangkan surat panggilan orang tua atau kunjungan rumah (home visit).
                </p>
            </div>
        </div>
    @else
        <div class="bg-emerald-50 border border-emerald-200 p-4 rounded-2xl flex items-center gap-3 text-emerald-800">
            <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <p class="text-xs font-semibold">
                Kondisi Ideal: Tidak ada siswa dengan persentase kehadiran di bawah {{ $ambangBatas }}% pada filter yang dipilih.
            </p>
        </div>
    @endif

    <!-- Tabel Monitoring Siswa -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h4 class="text-sm font-bold text-slate-800">Daftar Siswa di Bawah Standar Kehadiran</h4>
            <button onclick="window.print()" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                Cetak Laporan Khusus
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-slate-100 text-slate-400 uppercase tracking-wider text-[10px] bg-slate-50/50">
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Nama Siswa</th>
                        <th class="py-3 px-4">NIS</th>
                        <th class="py-3 px-4">Kelas</th>
                        <th class="py-3 px-3 text-center">Total Sesi</th>
                        <th class="py-3 px-3 text-center text-emerald-600">Hadir</th>
                        <th class="py-3 px-3 text-center text-blue-600">Izin</th>
                        <th class="py-3 px-3 text-center text-amber-600">Sakit</th>
                        <th class="py-3 px-3 text-center text-rose-600">Alpha</th>
                        <th class="py-3 px-4 text-center">Persentase</th>
                        <th class="py-3 px-4 text-center">Rekomendasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse ($siswaList as $idx => $s)
                        <tr class="hover:bg-slate-50/50 transition bg-rose-50/30">
                            <td class="py-3.5 px-4 text-center text-slate-400 font-medium">{{ $idx + 1 }}</td>
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-slate-900 block">{{ $s->nama }}</span>
                                <span class="text-[10px] text-slate-400">{{ $s->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                            </td>
                            <td class="py-3.5 px-4 font-mono text-slate-500">{{ $s->nis ?? '-' }}</td>
                            <td class="py-3.5 px-4 font-medium text-slate-700">Kelas {{ $s->kelas->nama_kelas ?? '-' }}</td>
                            <td class="py-3.5 px-3 text-center font-bold text-slate-700">{{ $s->total_absen }}</td>
                            <td class="py-3.5 px-3 text-center font-bold text-emerald-600">{{ $s->total_hadir }}</td>
                            <td class="py-3.5 px-3 text-center font-bold text-blue-600">{{ $s->total_izin }}</td>
                            <td class="py-3.5 px-3 text-center font-bold text-amber-600">{{ $s->total_sakit }}</td>
                            <td class="py-3.5 px-3 text-center font-bold text-rose-600">{{ $s->total_alpha }}</td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-700">
                                    {{ $s->persentase }}%
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if ($s->total_alpha >= 3)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-600 text-white">
                                        Surat Panggilan Orang Tua
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-100 text-amber-800">
                                        Bimbingan Wali Kelas
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="py-10 text-center text-slate-400">
                                Tidak ada siswa yang memerlukan perhatian khusus (&lt; {{ $ambangBatas }}%).
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
