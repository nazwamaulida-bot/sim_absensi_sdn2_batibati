@extends('layouts.app')

@section('title', 'Laporan & Integrasi Google Drive')
@section('page_title', 'Laporan & Arsip Google Drive')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h3 class="text-lg font-bold text-slate-800">Cetak Laporan & Sinkronisasi Google Drive</h3>
            <p class="text-xs text-slate-500">Cetak rekapitulasi presensi berkala atau arsipkan otomatis ke Google Drive sekolah.</p>
        </div>
    </div>

    <!-- Panel Filter & Generator Laporan -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Form Cetak Laporan (Pratinjau / Print) -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <h4 class="text-sm font-bold text-slate-900 mb-1 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                1. Cetak Laporan Resmi (PDF / Print)
            </h4>
            <p class="text-xs text-slate-500 mb-4">Membuka tampilan cetak bersih dengan kop surat UPTD SDN 2 Bati-Bati.</p>

            <form method="GET" action="{{ route('admin.laporan.cetak') }}" target="_blank" class="space-y-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Jenis Laporan *</label>
                    <select name="jenis_laporan" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="absensi_guru">Laporan Rekapitulasi Presensi Guru</option>
                        <option value="absensi_siswa">Laporan Rekapitulasi Presensi Siswa</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Periode Mulai *</label>
                        <input type="date" name="periode_mulai" required value="{{ date('Y-m-01') }}"
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Periode Selesai *</label>
                        <input type="date" name="periode_selesai" required value="{{ date('Y-m-d') }}"
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Semester *</label>
                        <select name="semester_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @foreach ($semesters as $s)
                                <option value="{{ $s->id }}">{{ $s->nama_semester }} ({{ $s->tahunAjaran->nama_tahun_ajaran ?? '' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Kelas (Khusus Siswa)</label>
                        <select name="kelas_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua Kelas</option>
                            @foreach ($kelasList as $k)
                                <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <button type="submit" class="w-full py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-sm transition mt-2">
                    Buka Pratinjau & Cetak PDF
                </button>
            </form>
        </div>

        <!-- Form Arsip ke Google Drive -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <h4 class="text-sm font-bold text-slate-900 mb-1 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                2. Arsipkan ke Google Drive
            </h4>
            <p class="text-xs text-slate-500 mb-4">Mengunggah laporan terenkripsi ke folder Google Drive resmi sekolah.</p>

            <form method="POST" action="{{ route('admin.laporan.arsipkan') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Jenis Laporan *</label>
                    <select name="jenis_laporan" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="absensi_guru">Laporan Rekapitulasi Presensi Guru</option>
                        <option value="absensi_siswa">Laporan Rekapitulasi Presensi Siswa</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Periode Mulai *</label>
                        <input type="date" name="periode_mulai" required value="{{ date('Y-m-01') }}"
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Periode Selesai *</label>
                        <input type="date" name="periode_selesai" required value="{{ date('Y-m-d') }}"
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Semester *</label>
                        <select name="semester_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @foreach ($semesters as $s)
                                <option value="{{ $s->id }}">{{ $s->nama_semester }} ({{ $s->tahunAjaran->nama_tahun_ajaran ?? '' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Kelas (Khusus Siswa)</label>
                        <select name="kelas_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua Kelas</option>
                            @foreach ($kelasList as $k)
                                <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <button type="submit" class="w-full py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-sm transition mt-2">
                    Generate & Upload ke Google Drive
                </button>
            </form>
        </div>

    </div>

    <!-- Riwayat Arsip Laporan -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 font-bold text-xs uppercase tracking-wider text-slate-600">
            Daftar Arsip Laporan Terintegrasi Google Drive
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="py-3 px-4">Tanggal Arsip</th>
                        <th class="py-3 px-4">Jenis Laporan</th>
                        <th class="py-3 px-4">Periode</th>
                        <th class="py-3 px-4">Semester</th>
                        <th class="py-3 px-4">Dibuat Oleh</th>
                        <th class="py-3 px-4 text-center">Status Google Drive</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($laporans as $lap)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-4 font-semibold text-slate-900">{{ date('d M Y H:i', strtotime($lap->created_at)) }}</td>
                            <td class="py-3.5 px-4 font-bold text-slate-800">
                                {{ $lap->jenis_laporan == 'absensi_guru' ? 'Rekap Presensi Guru' : 'Rekap Presensi Siswa' }}
                                @if ($lap->kelas)
                                    <span class="text-blue-600 block text-[11px]">({{ $lap->kelas->nama_kelas }})</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">{{ date('d M Y', strtotime($lap->periode_mulai)) }} s/d {{ date('d M Y', strtotime($lap->periode_selesai)) }}</td>
                            <td class="py-3.5 px-4">{{ $lap->semester->nama_semester ?? '-' }}</td>
                            <td class="py-3.5 px-4 text-slate-600">{{ $lap->pembuat->name ?? 'Admin' }}</td>
                            <td class="py-3.5 px-4 text-center">
                                @if ($lap->drive_url || $lap->drive_file_id)
                                    <a href="{{ $lap->drive_url ?: 'https://drive.google.com/file/d/' . $lap->drive_file_id . '/view' }}" target="_blank"
                                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-emerald-50 text-emerald-800 hover:bg-emerald-100 font-semibold transition">
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14h2v2h-2v-2zm0-10h2v8h-2V6z"/></svg>
                                        Buka di Google Drive
                                    </a>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-medium bg-amber-50 text-amber-700">Tersimpan Lokal</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">Belum ada arsip laporan yang digenerate.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($laporans->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $laporans->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
