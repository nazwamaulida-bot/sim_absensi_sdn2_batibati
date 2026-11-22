@extends('layouts.app')

@section('title', 'Persetujuan Izin Guru')
@section('page_title', 'Verifikasi & Persetujuan Permohonan Izin / Sakit Guru')

@section('content')
<div class="space-y-6">

    <!-- Header & Filter Tab Status -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <h3 class="text-base font-bold text-slate-800">Daftar Pengajuan Izin Tenaga Pendidik</h3>
            <p class="text-xs text-slate-500 mt-1">
                Persetujuan izin akan otomatis mencatat presensi guru yang bersangkutan pada tanggal izin terkait.
            </p>
        </div>

        <div class="flex items-center gap-1.5 bg-slate-100 p-1 rounded-xl text-xs font-semibold">
            <a href="{{ route('kepala_sekolah.izin.index') }}" class="px-3 py-1.5 rounded-lg transition {{ empty($status) ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                Semua
            </a>
            <a href="{{ route('kepala_sekolah.izin.index', ['status' => 'menunggu']) }}" class="px-3 py-1.5 rounded-lg transition {{ $status == 'menunggu' ? 'bg-white text-amber-700 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                Menunggu
            </a>
            <a href="{{ route('kepala_sekolah.izin.index', ['status' => 'disetujui']) }}" class="px-3 py-1.5 rounded-lg transition {{ $status == 'disetujui' ? 'bg-white text-emerald-700 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                Disetujui
            </a>
            <a href="{{ route('kepala_sekolah.izin.index', ['status' => 'ditolak']) }}" class="px-3 py-1.5 rounded-lg transition {{ $status == 'ditolak' ? 'bg-white text-rose-700 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                Ditolak
            </a>
        </div>
    </div>

    <!-- Tabel Permohonan Izin -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-slate-100 text-slate-400 uppercase tracking-wider text-[10px] bg-slate-50/50">
                        <th class="py-3.5 px-4">Guru Pemohon</th>
                        <th class="py-3.5 px-4">Jenis</th>
                        <th class="py-3.5 px-4">Rentang Tanggal</th>
                        <th class="py-3.5 px-4">Keterangan & Bukti</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-center">Tindakan / Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse ($pengajuans as $item)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-slate-900 block">{{ $item->guru->nama ?? '-' }}</span>
                                <span class="text-[11px] text-slate-400 font-mono">NIP: {{ $item->guru->nip ?? '-' }}</span>
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if ($item->jenis == 'izin')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-blue-100 text-blue-800">
                                        Izin
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-amber-100 text-amber-800">
                                        Sakit
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="font-medium text-slate-800">{{ Carbon\Carbon::parse($item->tanggal_mulai)->format('d/m/Y') }}</span>
                                <span class="text-slate-400">s/d</span>
                                <span class="font-medium text-slate-800">{{ Carbon\Carbon::parse($item->tanggal_selesai)->format('d/m/Y') }}</span>
                                <span class="block text-[10px] text-slate-400 mt-0.5">
                                    {{ Carbon\Carbon::parse($item->tanggal_mulai)->diffInDays(Carbon\Carbon::parse($item->tanggal_selesai)) + 1 }} Hari
                                </span>
                            </td>
                            <td class="py-3.5 px-4 max-w-xs">
                                <p class="text-slate-700 leading-snug">{{ $item->keterangan }}</p>
                                @if ($item->dokumen_pendukung)
                                    <a href="{{ asset('storage/' . $item->dokumen_pendukung) }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] text-indigo-600 hover:text-indigo-800 mt-1 font-semibold">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                        Buka Lampiran Bukti
                                    </a>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                @if ($item->status == 'menunggu')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                        Menunggu
                                    </span>
                                @elseif ($item->status == 'disetujui')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Disetujui
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        Ditolak
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                @if ($item->status == 'menunggu')
                                    <form action="{{ route('kepala_sekolah.izin.verify', $item->id) }}" method="POST" class="space-y-2">
                                        @csrf
                                        <input type="text" name="catatan_kepsek" placeholder="Catatan opsional..." class="w-full text-[11px] rounded-lg border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 py-1 px-2">
                                        <div class="flex items-center gap-2">
                                            <button type="submit" name="status" value="disetujui" class="flex-1 py-1 px-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-[11px] transition shadow-sm">
                                                Setujui
                                            </button>
                                            <button type="submit" name="status" value="ditolak" class="flex-1 py-1 px-2.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white font-semibold text-[11px] transition shadow-sm" onclick="return confirm('Yakin ingin menolak permohonan izin ini?')">
                                                Tolak
                                            </button>
                                        </div>
                                    </form>
                                @else
                                    <div class="text-[11px] text-slate-500">
                                        <p class="italic text-slate-600">"{{ $item->catatan_kepsek ?? 'Tanpa catatan' }}"</p>
                                        <p class="text-[10px] text-slate-400 mt-1">Oleh: {{ $item->verifikator->name ?? 'Kepala Sekolah' }}</p>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-slate-400">
                                Tidak ada data permohonan izin dengan filter status ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($pengajuans->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $pengajuans->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
