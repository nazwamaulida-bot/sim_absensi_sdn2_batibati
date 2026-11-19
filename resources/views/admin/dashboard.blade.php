@extends('layouts.app')

@section('title', 'Dashboard Administrator TU')
@section('page_title', 'Dashboard Administrator TU')

@section('content')
<div class="space-y-6">

    <!-- Kartu Statistik Utama -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card Total Guru -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Guru</p>
                <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ $totalGuru }}</h3>
                <p class="text-xs text-blue-600 font-medium mt-1">Tenaga Pendidik Aktif</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
        </div>

        <!-- Card Total Siswa -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Siswa</p>
                <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ $totalSiswa }}</h3>
                <p class="text-xs text-emerald-600 font-medium mt-1">Peserta Didik Aktif</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
        </div>

        <!-- Card Kehadiran Guru Hari Ini -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Guru Hadir Hari Ini</p>
                <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ $guruHadir + $guruTerlambat }} <span class="text-sm font-normal text-slate-500">/ {{ $totalGuru }}</span></h3>
                <p class="text-xs text-slate-500 mt-1">
                    <span class="text-emerald-600 font-medium">{{ $guruHadir }} tepat</span> • 
                    <span class="text-amber-600 font-medium">{{ $guruTerlambat }} terlambat</span>
                </p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        <!-- Card Kehadiran Siswa Hari Ini -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Siswa Hadir Hari Ini</p>
                <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ $siswaHadir }}</h3>
                <p class="text-xs text-slate-500 mt-1">
                    <span class="text-blue-600">{{ $siswaIzin }} izin</span> • 
                    <span class="text-amber-600">{{ $siswaSakit }} sakit</span> • 
                    <span class="text-rose-600">{{ $siswaAlpha }} alpa</span>
                </p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
        </div>
    </div>

    <!-- Grafik Tren & Ringkasan Hari Ini -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Grafik Tren 7 Hari Terakhir -->
        <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <h3 class="text-sm font-bold text-slate-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
                Tren Kehadiran (7 Hari Terakhir)
            </h3>
            <div class="h-64">
                <canvas id="trenChart"></canvas>
            </div>
        </div>

        <!-- Status Kehadiran Guru Hari Ini -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-900 mb-4">Presensi Guru Hari Ini</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 rounded-xl bg-emerald-50 text-emerald-900 text-sm">
                        <span class="flex items-center gap-2 font-medium">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Hadir Tepat Waktu
                        </span>
                        <span class="font-bold">{{ $guruHadir }} Guru</span>
                    </div>
                    <div class="flex items-center justify-between p-3 rounded-xl bg-amber-50 text-amber-900 text-sm">
                        <span class="flex items-center gap-2 font-medium">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> Terlambat (&gt;15m)
                        </span>
                        <span class="font-bold">{{ $guruTerlambat }} Guru</span>
                    </div>
                    <div class="flex items-center justify-between p-3 rounded-xl bg-blue-50 text-blue-900 text-sm">
                        <span class="flex items-center gap-2 font-medium">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span> Izin / Sakit
                        </span>
                        <span class="font-bold">{{ $guruIzin + $guruSakit }} Guru</span>
                    </div>
                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 text-slate-700 text-sm">
                        <span class="flex items-center gap-2 font-medium">
                            <span class="w-2.5 h-2.5 rounded-full bg-slate-400"></span> Belum Absen
                        </span>
                        <span class="font-bold">{{ $guruBelumAbsen }} Guru</span>
                    </div>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-slate-100 text-xs text-slate-500 text-center">
                Jadwal Masuk: <strong>07:30 WITA</strong> • Toleransi: <strong>15 Menit</strong>
            </div>
        </div>
    </div>

    <!-- Tabel Presensi Terbaru -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Absensi Guru Terbaru -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-900">Aktivitas Absensi Guru Terbaru</h3>
                <a href="{{ route('admin.absensi-guru.index') }}" class="text-xs text-blue-600 hover:underline font-medium">Lihat Semua &rarr;</a>
            </div>
            <div class="divide-y divide-slate-100 overflow-x-auto">
                @forelse ($absensiGuruTerbaru as $absen)
                    <div class="p-4 flex items-center justify-between text-sm">
                        <div>
                            <p class="font-semibold text-slate-800">{{ $absen->guru->nama ?? 'Guru' }}</p>
                            <p class="text-xs text-slate-500">Pukul: {{ $absen->jam_masuk ?? '-' }} • Radius: {{ $absen->jarak_meter ?? 0 }} m</p>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $absen->status_kehadiran == 'hadir' ? 'bg-emerald-100 text-emerald-800' : ($absen->status_kehadiran == 'terlambat' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800') }}">
                            {{ ucfirst($absen->status_kehadiran) }}
                        </span>
                    </div>
                @empty
                    <p class="p-6 text-xs text-center text-slate-400">Belum ada aktivitas absensi guru hari ini.</p>
                @endforelse
            </div>
        </div>

        <!-- Absensi Siswa Terbaru -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-900">Aktivitas Presensi Siswa Terbaru</h3>
                <a href="{{ route('admin.absensi-siswa.index') }}" class="text-xs text-blue-600 hover:underline font-medium">Lihat Semua &rarr;</a>
            </div>
            <div class="divide-y divide-slate-100 overflow-x-auto">
                @forelse ($absensiSiswaTerbaru as $absen)
                    <div class="p-4 flex items-center justify-between text-sm">
                        <div>
                            <p class="font-semibold text-slate-800">{{ $absen->siswa->nama ?? 'Siswa' }}</p>
                            <p class="text-xs text-slate-500">{{ $absen->siswa->kelas->nama_kelas ?? 'Kelas' }} • {{ $absen->jadwalPelajaran->mata_pelajaran ?? '-' }}</p>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $absen->status_kehadiran == 'hadir' ? 'bg-emerald-100 text-emerald-800' : ($absen->status_kehadiran == 'alpha' ? 'bg-rose-100 text-rose-800' : 'bg-blue-100 text-blue-800') }}">
                            {{ ucfirst($absen->status_kehadiran) }}
                        </span>
                    </div>
                @empty
                    <p class="p-6 text-xs text-center text-slate-400">Belum ada catatan presensi siswa hari ini.</p>
                @endforelse
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('trenChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartDates) !!},
                datasets: [
                    {
                        label: 'Guru Hadir',
                        data: {!! json_encode($chartGuruHadir) !!},
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37, 99, 235, 0.1)',
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Siswa Hadir',
                        data: {!! json_encode($chartSiswaHadir) !!},
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.3,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection
