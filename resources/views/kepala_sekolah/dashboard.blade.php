@extends('layouts.app')

@section('title', 'Dashboard Kepala Sekolah')
@section('page_title', 'Dashboard Eksekutif & Monitoring Presensi')

@section('content')
<div class="space-y-6">

    <!-- Ringkasan Statistik Eksekutif Hari Ini -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Kehadiran Guru -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Guru Hadir Hari Ini</span>
                <div class="flex items-baseline gap-2 mt-1">
                    <span class="text-2xl font-bold text-slate-900">{{ $guruHadir }}</span>
                    <span class="text-xs text-slate-500">/ {{ $totalGuru }} Guru</span>
                </div>
                <div class="flex items-center gap-2 mt-2 text-[11px]">
                    <span class="text-amber-600 font-semibold">{{ $guruTerlambat }} Terlambat</span>
                    <span class="text-slate-300">•</span>
                    <span class="text-blue-600 font-semibold">{{ $guruIzin + $guruSakit }} Izin/Sakit</span>
                </div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
        </div>

        <!-- Kehadiran Siswa -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Siswa Hadir Hari Ini</span>
                <div class="flex items-baseline gap-2 mt-1">
                    <span class="text-2xl font-bold text-slate-900">{{ $siswaHadir }}</span>
                    <span class="text-xs text-slate-500">/ {{ $totalSiswa }} Siswa</span>
                </div>
                <div class="flex items-center gap-2 mt-2 text-[11px]">
                    <span class="text-rose-600 font-semibold">{{ $siswaAlpha }} Alpha</span>
                    <span class="text-slate-300">•</span>
                    <span class="text-blue-600 font-semibold">{{ $siswaIzin + $siswaSakit }} Izin/Sakit</span>
                </div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
        </div>

        <!-- Izin Menunggu Verifikasi -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Izin Menunggu Persetujuan</span>
                <div class="flex items-baseline gap-2 mt-1">
                    <span class="text-2xl font-bold text-amber-600">{{ $izinMenunggu->count() }}</span>
                    <span class="text-xs text-slate-500">Permohonan</span>
                </div>
                <div class="mt-2">
                    <a href="{{ route('kepala_sekolah.izin.index') }}" class="text-[11px] text-blue-600 hover:text-blue-800 font-semibold">
                        Lihat & Verifikasi &rarr;
                    </a>
                </div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        <!-- Siswa Peringatan Rendah (< 75%) -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Peringatan Siswa (&lt; 75%)</span>
                <div class="flex items-baseline gap-2 mt-1">
                    <span class="text-2xl font-bold {{ $siswaPeringatan->count() > 0 ? 'text-rose-600' : 'text-slate-900' }}">
                        {{ $siswaPeringatan->count() }}
                    </span>
                    <span class="text-xs text-slate-500">Siswa Rendah</span>
                </div>
                <div class="mt-2">
                    <a href="{{ route('kepala_sekolah.monitoring.siswa') }}" class="text-[11px] text-rose-600 hover:text-rose-800 font-semibold">
                        Detail Monitoring &rarr;
                    </a>
                </div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
        </div>
    </div>

    <!-- Grafik Tren Kehadiran 7 Hari Terakhir -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-sm font-bold text-slate-800">Tren Kehadiran Guru & Siswa (7 Hari Terakhir)</h3>
                <p class="text-xs text-slate-500 mt-0.5">Visualisasi jumlah kehadiran harian di UPTD SDN 2 Bati-Bati</p>
            </div>
        </div>
        <div class="h-64">
            <canvas id="attendanceTrendChart"></canvas>
        </div>
    </div>

    <!-- Dua Kolom Evaluasi Aturan IF-THEN -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- ATURAN IF-THEN 2: Keterlambatan Guru Hari Ini (> 15 Menit) -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col justify-between">
            <div>
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h4 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                            Guru Terlambat Masuk Hari Ini (> 15 Menit)
                        </h4>
                        <p class="text-xs text-slate-500 mt-0.5">Batas toleransi maksimal pukul 07:45 WITA (Jadwal: 07:30 WITA)</p>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                        {{ $daftarGuruTerlambat->count() }} Guru
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-slate-100 text-slate-400 uppercase tracking-wider text-[10px] bg-slate-50/50">
                                <th class="py-3 px-4">Nama Guru</th>
                                <th class="py-3 px-4">NIP</th>
                                <th class="py-3 px-4">Jam Masuk</th>
                                <th class="py-3 px-4">Keterlambatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse ($daftarGuruTerlambat as $absen)
                                @php
                                    $jamMasuk = Carbon\Carbon::parse($absen->jam_masuk);
                                    $jamJadwal = Carbon\Carbon::parse('07:30:00');
                                    $selisihMenit = $jamMasuk->diffInMinutes($jamJadwal);
                                @endphp
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="py-3 px-4 font-bold text-slate-900">{{ $absen->guru->nama ?? '-' }}</td>
                                    <td class="py-3 px-4 text-slate-500 font-mono">{{ $absen->guru->nip ?? '-' }}</td>
                                    <td class="py-3 px-4 font-bold text-amber-700">{{ $absen->jam_masuk }} WITA</td>
                                    <td class="py-3 px-4">
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-100 text-amber-800">
                                            +{{ $selisihMenit }} Menit
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-slate-400">
                                        Tidak ada guru yang terlambat pada hari ini. Seluruh guru hadir tepat waktu.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="p-4 border-t border-slate-100 bg-slate-50/50 text-right">
                <a href="{{ route('kepala_sekolah.monitoring.guru') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800">
                    Lihat Seluruh Riwayat Keterlambatan Bulanan &rarr;
                </a>
            </div>
        </div>

        <!-- ATURAN IF-THEN 1: Siswa Kehadiran Rendah (< 75%) -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col justify-between">
            <div>
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h4 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                            Siswa dengan Kehadiran Rendah (&lt; 75%)
                        </h4>
                        <p class="text-xs text-slate-500 mt-0.5">Memerlukan pemanggilan orang tua atau pembinaan wali kelas</p>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800">
                        {{ $siswaPeringatan->count() }} Siswa
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-slate-100 text-slate-400 uppercase tracking-wider text-[10px] bg-slate-50/50">
                                <th class="py-3 px-4">Nama Siswa</th>
                                <th class="py-3 px-4">Kelas</th>
                                <th class="py-3 px-4 text-center">Kehadiran</th>
                                <th class="py-3 px-4 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse ($siswaPeringatan->take(5) as $s)
                                <tr class="hover:bg-slate-50/50 transition bg-rose-50/20">
                                    <td class="py-3 px-4 font-bold text-slate-900">{{ $s->nama }}</td>
                                    <td class="py-3 px-4 font-medium text-slate-600">Kelas {{ $s->kelas->nama_kelas ?? '-' }}</td>
                                    <td class="py-3 px-4 text-center font-bold text-rose-600">{{ $s->persentase }}%</td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">
                                            Perlu Perhatian
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-slate-400">
                                        Seluruh siswa memiliki tingkat kehadiran yang memenuhi standar (&ge; 75%).
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="p-4 border-t border-slate-100 bg-slate-50/50 text-right">
                <a href="{{ route('kepala_sekolah.monitoring.siswa') }}" class="text-xs font-semibold text-rose-600 hover:text-rose-800">
                    Buka Monitoring Lengkap Siswa &rarr;
                </a>
            </div>
        </div>

    </div>

</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('attendanceTrendChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($chartDates) !!},
                    datasets: [
                        {
                            label: 'Guru Hadir',
                            data: {!! json_encode($chartGuruHadir) !!},
                            borderColor: '#4f46e5',
                            backgroundColor: 'rgba(79, 70, 229, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.3
                        },
                        {
                            label: 'Siswa Hadir',
                            data: {!! json_encode($chartSiswaHadir) !!},
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.3
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top' }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0 }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush
@endsection
