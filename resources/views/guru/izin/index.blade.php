@extends('layouts.app')

@section('title', 'Pengajuan Izin / Sakit')
@section('page_title', 'Pengajuan Izin & Sakit Guru')

@section('content')
<div class="space-y-6">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Form Pengajuan Izin/Sakit Baru -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <div class="border-b border-slate-100 pb-4 mb-5">
                <h3 class="text-sm font-bold text-slate-800">Form Permohonan Izin / Sakit</h3>
                <p class="text-xs text-slate-500 mt-0.5">Permohonan akan diteruskan ke Kepala Sekolah untuk verifikasi dan persetujuan.</p>
            </div>

            <form action="{{ route('guru.izin.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <!-- Jenis Permohonan -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Jenis Permohonan <span class="text-rose-500">*</span></label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center gap-2 p-3 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 transition has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50/50">
                            <input type="radio" name="jenis" value="izin" class="text-blue-600 focus:ring-blue-500" {{ old('jenis', 'izin') == 'izin' ? 'checked' : '' }} required>
                            <div>
                                <span class="text-xs font-bold text-slate-800 block">Izin</span>
                                <span class="text-[10px] text-slate-500">Urusan kedinasan / keluarga</span>
                            </div>
                        </label>
                        <label class="flex items-center gap-2 p-3 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 transition has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50/50">
                            <input type="radio" name="jenis" value="sakit" class="text-blue-600 focus:ring-blue-500" {{ old('jenis') == 'sakit' ? 'checked' : '' }} required>
                            <div>
                                <span class="text-xs font-bold text-slate-800 block">Sakit</span>
                                <span class="text-[10px] text-slate-500">Dengan surat dokter</span>
                            </div>
                        </label>
                    </div>
                    @error('jenis')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Rentang Tanggal -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="tanggal_mulai" class="block text-xs font-semibold text-slate-700 mb-1">Mulai <span class="text-rose-500">*</span></label>
                        <input type="date" name="tanggal_mulai" id="tanggal_mulai" value="{{ old('tanggal_mulai', date('Y-m-d')) }}" required class="w-full text-xs rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                        @error('tanggal_mulai')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="tanggal_selesai" class="block text-xs font-semibold text-slate-700 mb-1">Selesai <span class="text-rose-500">*</span></label>
                        <input type="date" name="tanggal_selesai" id="tanggal_selesai" value="{{ old('tanggal_selesai', date('Y-m-d')) }}" required class="w-full text-xs rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                        @error('tanggal_selesai')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Keterangan / Alasan -->
                <div>
                    <label for="keterangan" class="block text-xs font-semibold text-slate-700 mb-1">Keterangan / Alasan Lengkap <span class="text-rose-500">*</span></label>
                    <textarea name="keterangan" id="keterangan" rows="3" required placeholder="Jelaskan alasan izin atau gejala sakit..." class="w-full text-xs rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">{{ old('keterangan') }}</textarea>
                    @error('keterangan')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Dokumen Pendukung -->
                <div>
                    <label for="dokumen_pendukung" class="block text-xs font-semibold text-slate-700 mb-1">Dokumen Pendukung (Opsional)</label>
                    <input type="file" name="dokumen_pendukung" id="dokumen_pendukung" accept=".pdf,.jpg,.jpeg,.png" class="w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-slate-200 rounded-xl">
                    <p class="text-[10px] text-slate-400 mt-1">Format: PDF, JPG, PNG (Maksimal 2MB). Contoh: Surat dokter, surat tugas.</p>
                    @error('dokumen_pendukung')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-sm transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    Kirim Permohonan Izin
                </button>
            </form>
        </div>

        <!-- Riwayat Pengajuan Izin -->
        <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-5">
                    <div>
                        <h3 class="text-sm font-bold text-slate-800">Riwayat Pengajuan Izin Saya</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Daftar seluruh permohonan izin dan status verifikasi dari Kepala Sekolah.</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-slate-100 text-slate-400 uppercase tracking-wider text-[10px] bg-slate-50/50">
                                <th class="py-3 px-3">Tgl Pengajuan</th>
                                <th class="py-3 px-3">Jenis</th>
                                <th class="py-3 px-3">Rentang Tanggal</th>
                                <th class="py-3 px-3">Alasan / Bukti</th>
                                <th class="py-3 px-3">Status Verifikasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse ($pengajuans as $item)
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="py-3 px-3 whitespace-nowrap text-slate-500">
                                        {{ $item->created_at->translatedFormat('d M Y') }}
                                        <span class="block text-[10px] text-slate-400">{{ $item->created_at->format('H:i') }} WITA</span>
                                    </td>
                                    <td class="py-3 px-3 whitespace-nowrap">
                                        @if ($item->jenis == 'izin')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-100 text-blue-800">
                                                Izin
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-100 text-amber-800">
                                                Sakit
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-3 whitespace-nowrap">
                                        <span class="font-medium text-slate-800">{{ Carbon\Carbon::parse($item->tanggal_mulai)->format('d/m/Y') }}</span>
                                        <span class="text-slate-400">s/d</span>
                                        <span class="font-medium text-slate-800">{{ Carbon\Carbon::parse($item->tanggal_selesai)->format('d/m/Y') }}</span>
                                    </td>
                                    <td class="py-3 px-3 max-w-xs">
                                        <p class="truncate" title="{{ $item->keterangan }}">{{ $item->keterangan }}</p>
                                        @if ($item->dokumen_pendukung)
                                            <a href="{{ asset('storage/' . $item->dokumen_pendukung) }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] text-blue-600 hover:text-blue-800 mt-1 font-medium">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                                Lihat Dokumen
                                            </a>
                                        @endif
                                    </td>
                                    <td class="py-3 px-3 whitespace-nowrap">
                                        @if ($item->status == 'menunggu')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-amber-100 text-amber-800">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                Menunggu
                                            </span>
                                        @elseif ($item->status == 'disetujui')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                Disetujui
                                            </span>
                                            @if ($item->catatan_kepsek)
                                                <p class="text-[10px] text-slate-500 mt-1 italic">"{{ $item->catatan_kepsek }}"</p>
                                            @endif
                                        @elseif ($item->status == 'ditolak')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-rose-100 text-rose-800">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                Ditolak
                                            </span>
                                            @if ($item->catatan_kepsek)
                                                <p class="text-[10px] text-rose-500 mt-1 italic">"{{ $item->catatan_kepsek }}"</p>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-slate-400">
                                        Belum ada riwayat permohonan izin atau sakit.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($pengajuans instanceof \Illuminate\Pagination\LengthAwarePaginator && $pengajuans->hasPages())
                <div class="mt-4 pt-4 border-t border-slate-100">
                    {{ $pengajuans->links() }}
                </div>
            @endif
        </div>

    </div>

</div>
@endsection
