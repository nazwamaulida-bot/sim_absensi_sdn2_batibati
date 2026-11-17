@extends('layouts.app')

@section('title', 'Dashboard Guru')
@section('page_title', 'Dashboard Guru & Tenaga Pendidik')

@section('content')
<div class="space-y-6">

    <!-- Status Kehadiran Hari Ini & Profil Singkat -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Kartu Absensi Hari Ini -->
        <div class="md:col-span-2 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Presensi Saya Hari Ini</span>
                    <span class="text-xs text-slate-400 font-medium">{{ Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
                </div>

                @if ($absensiHariIni)
                    <div class="flex items-center gap-4 p-4 rounded-xl {{ $absensiHariIni->status_kehadiran == 'hadir' ? 'bg-emerald-50 border border-emerald-200' : 'bg-amber-50 border border-amber-200' }}">
                        <div class="w-12 h-12 rounded-xl {{ $absensiHariIni->status_kehadiran == 'hadir' ? 'bg-emerald-600 text-white' : 'bg-amber-600 text-white' }} flex items-center justify-center font-bold">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-900 text-base">
                                Status: {{ strtoupper($absensiHariIni->status_kehadiran) }}
                            </h4>
                            <p class="text-xs text-slate-600 mt-0.5">
                                Masuk: <strong>{{ $absensiHariIni->jam_masuk ?? '-' }}</strong> • 
                                Pulang: <strong>{{ $absensiHariIni->jam_pulang ?? 'Belum Pulang' }}</strong> • 
                                Radius: {{ $absensiHariIni->jarak_meter ?? 0 }} m
                            </p>
                            @if ($absensiHariIni->keterangan)
                                <p class="text-[11px] text-slate-500 mt-1 italic">{{ $absensiHariIni->keterangan }}</p>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-slate-200 text-slate-500 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-800 text-sm">Anda Belum Melakukan Absensi Masuk Hari Ini</h4>
                            <p class="text-xs text-slate-500 mt-0.5">Batas toleransi masuk: <strong>07:45 WITA</strong> (Maksimal 15 menit dari jam 07:30).</p>
                        </div>
                    </div>
                @endif
            </div>

            <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between">
                <span class="text-xs text-slate-500">Radius Sekolah: <strong>100 Meter</strong></span>
                <a href="{{ route('guru.absensi.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                    Buka Halaman Absensi GPS / Kamera
                </a>
            </div>
        </div>

        <!-- Kartu Identitas Guru -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-3">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block border-b border-slate-100 pb-2">Profil Pendidik</span>
            <div>
                <p class="text-sm font-bold text-slate-900">{{ $guru->nama ?? Auth::user()->name }}</p>
                <p class="text-xs text-slate-500">NIP: {{ $guru->nip ?? '-' }}</p>
            </div>
            <div>
                <span class="text-[11px] text-slate-400 block">Email Terdaftar</span>
                <span class="text-xs font-medium text-slate-700">{{ Auth::user()->email }}</span>
            </div>
            <div>
                <span class="text-[11px] text-slate-400 block">Tugas Wali Kelas</span>
                @if (Auth::user()->isWaliKelas())
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 mt-1">
                        Wali Kelas Aktif
                    </span>
                @else
                    <span class="text-xs text-slate-400">Guru Mata Pelajaran</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Peringatan Khusus Wali Kelas: Siswa < 75% Kehadiran (Aturan IF-THEN) -->
    @if ($infoWaliKelas)
        <div class="bg-white p-6 rounded-2xl border border-amber-200 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <span class="p-2 rounded-xl bg-amber-100 text-amber-800 font-bold text-xs">Wali Kelas</span>
                    <div>
                        <h4 class="font-bold text-sm text-slate-900">{{ $infoWaliKelas['nama_kelas'] }}</h4>
                        <p class="text-xs text-slate-500">Total siswa terdaftar: {{ $infoWaliKelas['total_siswa'] }} siswa</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('guru.wali-kelas.absensi') }}" class="px-3 py-1.5 rounded-lg bg-blue-600 text-white text-xs font-semibold hover:bg-blue-700 transition">Absenkan Siswa</a>
                    <a href="{{ route('guru.wali-kelas.rekap') }}" class="px-3 py-1.5 rounded-lg border border-slate-200 text-slate-700 text-xs font-semibold hover:bg-slate-50 transition">Lihat Rekap</a>
                </div>
            </div>

            <!-- Warning Alert jika ada siswa di bawah 75% -->
            @if ($infoWaliKelas['total_peringatan'] > 0)
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-900 text-xs flex items-start gap-3">
                    <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <div>
                        <strong class="font-semibold block text-sm">Peringatan Tingkat Kehadiran Rendah (&lt; 75%):</strong>
                        Terdapat <strong>{{ $infoWaliKelas['total_peringatan'] }} siswa</strong> di kelas Anda yang memiliki tingkat kehadiran di bawah 75%. Mohon segera dilakukan penelusuran atau koordinasi wali murid.
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach ($siswaPeringatanRendah as $sw)
                                <span class="px-2 py-0.5 rounded bg-rose-100 text-rose-800 font-bold text-[11px]">
                                    {{ $sw->nama }} ({{ $sw->persentase_kehadiran }}%)
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- Jadwal Mengajar Hari Ini -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h4 class="font-bold text-sm text-slate-900">Jadwal Mengajar Saya Hari Ini</h4>
            <a href="{{ route('guru.jadwal.index') }}" class="text-xs text-blue-600 font-semibold hover:underline">Lihat Jadwal Lengkap &rarr;</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-600 font-semibold uppercase text-[11px]">
                    <tr>
                        <th class="p-3.5 px-4">Jam Pelajaran</th>
                        <th class="p-3.5 px-4">Kelas</th>
                        <th class="p-3.5 px-4">Mata Pelajaran</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($jadwalHariIni as $j)
                        <tr>
                            <td class="p-3.5 px-4 font-mono font-semibold">{{ substr($j->jam_mulai, 0, 5) }} - {{ substr($j->jam_selesai, 0, 5) }} WITA</td>
                            <td class="p-3.5 px-4 font-bold text-blue-700">{{ $j->kelas->nama_kelas ?? '-' }}</td>
                            <td class="p-3.5 px-4 font-medium text-slate-800">{{ $j->mata_pelajaran }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="p-6 text-center text-slate-400">Tidak ada jadwal mengajar pada hari ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
