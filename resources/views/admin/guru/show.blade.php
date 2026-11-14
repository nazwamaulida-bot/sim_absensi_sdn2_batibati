@extends('layouts.app')

@section('title', 'Detail Guru - ' . $guru->nama)
@section('page_title', 'Detail Guru')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-base font-bold text-slate-900">{{ $guru->nama }}</h3>
            <p class="text-xs text-slate-500">NIP: {{ $guru->nip ?? 'Belum ada NIP' }} • Email: {{ $guru->email }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.guru.edit', $guru->id) }}" class="px-3.5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold shadow-sm transition">Edit Guru</a>
            <a href="{{ route('admin.guru.index') }}" class="px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">&larr; Kembali</a>
        </div>
    </div>

    <!-- Informasi Profil & Tugas -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Profil Card -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-4">
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 border-b border-slate-100 pb-2">Informasi Akun</h4>
            <div>
                <span class="text-xs text-slate-400 block">Nama Lengkap</span>
                <span class="text-sm font-semibold text-slate-800">{{ $guru->nama }}</span>
            </div>
            <div>
                <span class="text-xs text-slate-400 block">Nomor Induk Pegawai</span>
                <span class="text-sm font-semibold text-slate-800">{{ $guru->nip ?? '-' }}</span>
            </div>
            <div>
                <span class="text-xs text-slate-400 block">No. Telepon / WhatsApp</span>
                <span class="text-sm font-semibold text-slate-800">{{ $guru->no_hp ?? '-' }}</span>
            </div>
            <div>
                <span class="text-xs text-slate-400 block">Status Akun Login</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 mt-1">
                    {{ $guru->user->status ?? 'Tidak Terhubung' }}
                </span>
            </div>
        </div>

        <!-- Penugasan Wali Kelas & Jadwal -->
        <div class="md:col-span-2 bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-4">
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 border-b border-slate-100 pb-2">Penugasan Wali Kelas & Jadwal Mengajar</h4>
            
            <div>
                <span class="text-xs font-semibold text-slate-700 block mb-1">Riwayat Wali Kelas:</span>
                <div class="flex flex-wrap gap-2">
                    @forelse ($guru->waliKelas as $wali)
                        <span class="px-3 py-1 rounded-lg bg-blue-50 border border-blue-200 text-blue-800 text-xs font-semibold">
                            {{ $wali->kelas->nama_kelas ?? 'Kelas' }} (TA: {{ $wali->tahunAjaran->nama_tahun_ajaran ?? '-' }})
                        </span>
                    @empty
                        <span class="text-xs text-slate-400">Tidak ada riwayat penugasan wali kelas.</span>
                    @endforelse
                </div>
            </div>

            <div class="pt-2">
                <span class="text-xs font-semibold text-slate-700 block mb-2">Jadwal Mengajar Aktif:</span>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-600 font-medium">
                            <tr>
                                <th class="p-2">Hari</th>
                                <th class="p-2">Kelas</th>
                                <th class="p-2">Mata Pelajaran</th>
                                <th class="p-2">Jam</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($guru->jadwalPelajaran as $j)
                                <tr>
                                    <td class="p-2 font-semibold text-slate-800">{{ $j->hari }}</td>
                                    <td class="p-2">{{ $j->kelas->nama_kelas ?? '-' }}</td>
                                    <td class="p-2">{{ $j->mata_pelajaran }}</td>
                                    <td class="p-2">{{ substr($j->jam_mulai, 0, 5) }} - {{ substr($j->jam_selesai, 0, 5) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-4 text-center text-slate-400">Belum ada jadwal mengajar yang diatur.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Riwayat Absensi Terakhir -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 font-bold text-xs uppercase tracking-wider text-slate-600">
            10 Catatan Kehadiran Terakhir
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-600 font-semibold">
                    <tr>
                        <th class="p-3">Tanggal</th>
                        <th class="p-3">Jam Masuk</th>
                        <th class="p-3">Jam Pulang</th>
                        <th class="p-3">Jarak (m)</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($guru->absensiGuru as $ab)
                        <tr>
                            <td class="p-3 font-semibold">{{ $ab->tanggal }}</td>
                            <td class="p-3">{{ $ab->jam_masuk ?? '-' }}</td>
                            <td class="p-3">{{ $ab->jam_pulang ?? '-' }}</td>
                            <td class="p-3">{{ $ab->jarak_meter ?? 0 }} m</td>
                            <td class="p-3">
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $ab->status_kehadiran == 'hadir' ? 'bg-emerald-100 text-emerald-800' : ($ab->status_kehadiran == 'terlambat' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800') }}">
                                    {{ ucfirst($ab->status_kehadiran) }}
                                </span>
                            </td>
                            <td class="p-3 text-slate-500">{{ $ab->keterangan ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-6 text-center text-slate-400">Belum ada riwayat absensi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
