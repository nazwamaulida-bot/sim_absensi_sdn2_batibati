<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') - UPTD SDN 2 Bati-Bati</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles: Vite & Tailwind Fallback -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Chart.js for Stats Visualizations -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
    @stack('styles')
</head>
<body class="h-full text-slate-800 antialiased flex flex-col md:flex-row bg-slate-100">

    <!-- Mobile Header -->
    <div class="md:hidden bg-slate-900 text-white flex items-center justify-between px-4 py-3 border-b border-slate-800">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center font-bold text-white text-sm">
                SD
            </div>
            <span class="font-bold text-sm tracking-tight">SDN 2 Bati-Bati</span>
        </div>
        <button id="mobileMenuBtn" class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </div>

    <!-- Sidebar Navigasi -->
    <aside id="sidebar" class="hidden md:flex flex-col w-64 bg-slate-900 text-slate-300 min-h-screen shrink-0 border-r border-slate-800 transition-all duration-200">
        <!-- Logo & Identitas -->
        <div class="p-5 border-b border-slate-800 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold text-lg shadow-md shadow-blue-500/20">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
            <div>
                <h1 class="font-bold text-white text-sm leading-tight">SIM Absensi</h1>
                <p class="text-xs text-blue-400 font-medium">UPTD SDN 2 Bati-Bati</p>
            </div>
        </div>

        <!-- Menu Navigasi Berdasarkan Role -->
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto text-sm">
            @php
                $user = Auth::user();
                $currentRoute = Route::currentRouteName();
            @endphp

            @if ($user->isAdminTu())
                <!-- MENU ADMIN TU -->
                <div class="px-3 pb-2 text-[11px] font-semibold tracking-wider text-slate-500 uppercase">Menu Utama</div>
                
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition {{ $currentRoute == 'admin.dashboard' ? 'bg-blue-600 text-white shadow-sm' : 'hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Dashboard Admin
                </a>

                <div class="pt-4 px-3 pb-2 text-[11px] font-semibold tracking-wider text-slate-500 uppercase">Master Data</div>

                <a href="{{ route('admin.guru.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition {{ str_starts_with($currentRoute, 'admin.guru') ? 'bg-blue-600 text-white' : 'hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    Data Guru
                </a>

                <a href="{{ route('admin.siswa.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition {{ str_starts_with($currentRoute, 'admin.siswa') ? 'bg-blue-600 text-white' : 'hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    Data Siswa
                </a>

                <a href="{{ route('admin.kelas.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition {{ str_starts_with($currentRoute, 'admin.kelas') || str_starts_with($currentRoute, 'admin.wali-kelas') ? 'bg-blue-600 text-white' : 'hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    Kelas & Wali Kelas
                </a>

                <div class="pt-4 px-3 pb-2 text-[11px] font-semibold tracking-wider text-slate-500 uppercase">Akademik</div>

                <a href="{{ route('admin.tahun-ajaran.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition {{ str_starts_with($currentRoute, 'admin.tahun-ajaran') || str_starts_with($currentRoute, 'admin.semester') ? 'bg-blue-600 text-white' : 'hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Tahun Ajaran & Semester
                </a>

                <a href="{{ route('admin.jadwal.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition {{ str_starts_with($currentRoute, 'admin.jadwal') ? 'bg-blue-600 text-white' : 'hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Jadwal Pelajaran
                </a>

                <div class="pt-4 px-3 pb-2 text-[11px] font-semibold tracking-wider text-slate-500 uppercase">Presensi & Rekap</div>

                <a href="{{ route('admin.absensi-guru.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition {{ str_starts_with($currentRoute, 'admin.absensi-guru') ? 'bg-blue-600 text-white' : 'hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    Absensi Guru
                </a>

                <a href="{{ route('admin.absensi-siswa.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition {{ str_starts_with($currentRoute, 'admin.absensi-siswa') ? 'bg-blue-600 text-white' : 'hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    Absensi Siswa
                </a>

                <a href="{{ route('admin.laporan.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition {{ str_starts_with($currentRoute, 'admin.laporan') ? 'bg-blue-600 text-white' : 'hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Laporan & Google Drive
                </a>

            @elseif ($user->isGuru())
                <!-- MENU GURU -->
                <div class="px-3 pb-2 text-[11px] font-semibold tracking-wider text-slate-500 uppercase">Guru & Pengajar</div>

                <a href="{{ route('guru.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition {{ $currentRoute == 'guru.dashboard' ? 'bg-emerald-600 text-white shadow-sm' : 'hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Dashboard Guru
                </a>

                <a href="{{ route('guru.absensi.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition {{ str_starts_with($currentRoute, 'guru.absensi') ? 'bg-emerald-600 text-white' : 'hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Absensi Mandiri (GPS/Kamera)
                </a>

                <a href="{{ route('guru.izin.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition {{ str_starts_with($currentRoute, 'guru.izin') ? 'bg-emerald-600 text-white' : 'hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Pengajuan Izin / Sakit
                </a>

                <a href="{{ route('guru.jadwal.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition {{ str_starts_with($currentRoute, 'guru.jadwal') ? 'bg-emerald-600 text-white' : 'hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Jadwal Mengajar
                </a>

                @if ($user->isWaliKelas())
                    <div class="pt-4 px-3 pb-2 text-[11px] font-semibold tracking-wider text-amber-400 uppercase">Tugas Tambahan (Wali Kelas)</div>

                    <a href="{{ route('guru.wali-kelas.siswa') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition {{ str_starts_with($currentRoute, 'guru.wali-kelas.siswa') ? 'bg-amber-600 text-white' : 'hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        Daftar Siswa Kelas Saya
                    </a>

                    <a href="{{ route('guru.wali-kelas.absensi') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition {{ str_starts_with($currentRoute, 'guru.wali-kelas.absensi') ? 'bg-amber-600 text-white' : 'hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        Absensi Kelas Siswa
                    </a>

                    <a href="{{ route('guru.wali-kelas.rekap') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition {{ str_starts_with($currentRoute, 'guru.wali-kelas.rekap') ? 'bg-amber-600 text-white' : 'hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        Rekap & Peringatan &lt; 75%
                    </a>
                @endif

            @elseif ($user->isKepalaSekolah())
                <!-- MENU KEPALA SEKOLAH -->
                <div class="px-3 pb-2 text-[11px] font-semibold tracking-wider text-slate-500 uppercase">Eksekutif / Monitoring</div>

                <a href="{{ route('kepala_sekolah.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition {{ $currentRoute == 'kepala_sekolah.dashboard' ? 'bg-indigo-600 text-white shadow-sm' : 'hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Dashboard Monitoring
                </a>

                <a href="{{ route('kepala_sekolah.izin.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition {{ str_starts_with($currentRoute, 'kepala_sekolah.izin') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Persetujuan Izin Guru
                </a>

                <a href="{{ route('kepala_sekolah.monitoring.guru') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition {{ str_starts_with($currentRoute, 'kepala_sekolah.monitoring.guru') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Monitoring Keterlambatan (&gt;15m)
                </a>

                <a href="{{ route('kepala_sekolah.monitoring.siswa') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition {{ str_starts_with($currentRoute, 'kepala_sekolah.monitoring.siswa') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    Peringatan Siswa &lt; 75%
                </a>

                <a href="{{ route('kepala_sekolah.laporan.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition {{ str_starts_with($currentRoute, 'kepala_sekolah.laporan') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Laporan & Arsip Google Drive
                </a>
            @endif
        </nav>

        <!-- Akun & Logout di Footer Sidebar -->
        <div class="p-4 border-t border-slate-800 bg-slate-950/40">
            <div class="flex items-center justify-between">
                <div class="truncate">
                    <p class="text-xs font-semibold text-white truncate">{{ $user->name }}</p>
                    <p class="text-[11px] text-slate-400 capitalize">{{ str_replace('_', ' ', $user->role) }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-400 hover:bg-slate-800 rounded-lg transition" title="Logout">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Konten Utama -->
    <div class="flex-1 flex flex-col min-h-screen overflow-x-hidden">
        
        <!-- Header Atas Desktop -->
        <header class="bg-white border-b border-slate-200 hidden md:flex items-center justify-between px-8 py-3.5 sticky top-0 z-10 shadow-sm">
            <div>
                <h2 class="text-base font-bold text-slate-800">@yield('page_title', 'Dashboard')</h2>
                <p class="text-xs text-slate-500">Sistem Manajemen Presensi & Akademik UPTD SDN 2 Bati-Bati</p>
            </div>

            <div class="flex items-center gap-4">
                <div class="text-right">
                    <span class="text-xs font-semibold text-slate-800 block">{{ $user->name }}</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-100 text-blue-800 capitalize">
                        {{ str_replace('_', ' ', $user->role) }}
                    </span>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-slate-100 hover:bg-rose-50 text-slate-700 hover:text-rose-700 border border-slate-200 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Logout
                    </button>
                </form>
            </div>
        </header>

        <!-- Area Konten Dinamis -->
        <main class="flex-1 p-4 md:p-8 space-y-6">
            
            <!-- Notifikasi Flash -->
            @if (session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-sm text-emerald-800 flex items-start gap-3 shadow-sm">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-sm text-rose-800 flex items-start gap-3 shadow-sm">
                    <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-sm text-rose-800 shadow-sm">
                    <p class="font-semibold mb-1 flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Terdapat kesalahan pada input formulir:
                    </p>
                    <ul class="list-disc list-inside text-xs space-y-0.5 ml-2">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-slate-200 px-8 py-4 text-center text-xs text-slate-500">
            &copy; {{ date('Y') }} Sistem Informasi Manajemen Absensi UPTD SDN 2 Bati-Bati. Hak Cipta Dilindungi.
        </footer>
    </div>

    <!-- Toggle Script Mobile -->
    <script>
        document.getElementById('mobileMenuBtn')?.addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('hidden');
        });
    </script>
    @stack('scripts')
</body>
</html>
