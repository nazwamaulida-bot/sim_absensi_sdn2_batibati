@extends('layouts.app')

@section('title', 'Laporan & Google Drive')
@section('page_title', 'Arsip Laporan Presensi & Sinkronisasi Google Drive')

@section('content')
<div class="space-y-6">

    <!-- Header & Info -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span>
                <h3 class="text-base font-bold text-slate-800">Arsip Laporan Resmi Presensi</h3>
            </div>
            <p class="text-xs text-slate-500 mt-1">
                Laporan presensi yang di-generate oleh Admin TU tersimpan secara lokal dan otomatis di-backup ke Google Drive Sekolah.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-indigo-50 text-indigo-700 text-xs font-semibold">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 00-9.791 2.292A3.999 3.999 0 003 15z"/></svg>
                Google Drive Terintegrasi
            </span>
        </div>
    </div>

    <!-- Tabel Arsip Laporan -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h4 class="text-sm font-bold text-slate-800">Daftar Dokumen Laporan Tersedia</h4>
            <span class="text-xs text-slate-400">Total Arsip: {{ $laporans->total() }} Dokumen</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-slate-100 text-slate-400 uppercase tracking-wider text-[10px] bg-slate-50/50">
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Nama Laporan</th>
                        <th class="py-3 px-4">Jenis & Target</th>
                        <th class="py-3 px-4">Periode</th>
                        <th class="py-3 px-4">Dibuat Oleh</th>
                        <th class="py-3 px-4 text-center">Status Google Drive</th>
                        <th class="py-3 px-4 text-center">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse ($laporans as $idx => $item)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-3.5 px-4 text-center text-slate-400 font-medium">{{ $laporans->firstItem() + $idx }}</td>
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-slate-900 block">{{ $item->judul ?? 'Laporan Presensi' }}</span>
                                <span class="text-[10px] text-slate-400">{{ $item->created_at->translatedFormat('d M Y, H:i') }} WITA</span>
                            </td>
                            <td class="py-3.5 px-4">
                                @if ($item->jenis == 'guru')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-indigo-100 text-indigo-800">
                                        Guru & Tenaga Kependidikan
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-800">
                                        Siswa • Kelas {{ $item->kelas->nama_kelas ?? '-' }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap text-slate-600">
                                {{ Carbon\Carbon::parse($item->tanggal_mulai)->format('d/m/Y') }} - {{ Carbon\Carbon::parse($item->tanggal_selesai)->format('d/m/Y') }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-600">
                                {{ $item->pembuat->name ?? 'Admin TU' }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if ($item->google_drive_file_id)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">
                                        <svg class="w-3 h-3 text-blue-600" fill="currentColor" viewBox="0 0 24 24"><path d="M19.35 10.04C18.67 6.59 15.64 4 12 4 9.11 4 6.6 5.64 5.35 8.04 2.34 8.36 0 10.91 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96zM17 13l-5 5-5-5h3V9h4v4h3z"/></svg>
                                        Tersinkron
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-medium bg-slate-100 text-slate-600">
                                        Penyimpanan Lokal
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('admin.laporan.cetak', $item->id) }}" target="_blank" class="px-2.5 py-1 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-semibold transition">
                                        Buka Lembar Cetak
                                    </a>
                                    @if ($item->google_drive_link)
                                        <a href="{{ $item->google_drive_link }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 text-xs font-semibold transition flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/></svg>
                                            Buka Drive
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-slate-400">
                                Belum ada berkas laporan yang diarsipkan oleh Admin TU.
                            </td>
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
