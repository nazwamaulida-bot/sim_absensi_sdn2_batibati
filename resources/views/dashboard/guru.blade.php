<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Guru (Pengujian) - UPTD SDN 2 Bati-Bati</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="min-h-full bg-slate-50 text-slate-800">
    <!-- Navbar -->
    <nav class="bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 bg-emerald-600 text-white rounded-xl flex items-center justify-center font-bold">
                        GR
                    </div>
                    <div>
                        <h1 class="text-base font-semibold text-slate-900 leading-tight">UPTD SDN 2 Bati-Bati</h1>
                        <p class="text-xs text-emerald-600 font-medium">Dashboard Guru & Pengajar (Uji Coba)</p>
                    </div>
                </div>

                <!-- Tombol Logout -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" 
                        class="inline-flex items-center gap-2 px-3.5 py-2 border border-slate-200 text-xs font-semibold rounded-lg text-rose-700 bg-rose-50 hover:bg-rose-100 focus:outline-none transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Konten Dashboard -->
    <main class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-6">
        <!-- Banner Informasi Pengujian -->
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-sm text-emerald-900 flex items-start gap-3">
            <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <span class="font-semibold">Halaman Dashboard Sementara:</span>
                Halaman ini dibuat khusus untuk memverifikasi bahwa proses login berhasil dan middleware membatasi akses sesuai role <code>guru</code>.
            </div>
        </div>

        <!-- Kartu Identitas Akun Pengguna -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-slate-900 mb-4">Informasi Sesi Pengguna</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                        <p class="text-xs text-slate-500 font-medium">Nama Guru</p>
                        <p class="text-sm font-semibold text-slate-800 mt-1">{{ Auth::user()->name }}</p>
                    </div>
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                        <p class="text-xs text-slate-500 font-medium">Alamat Email</p>
                        <p class="text-sm font-semibold text-slate-800 mt-1">{{ Auth::user()->email }}</p>
                    </div>
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                        <p class="text-xs text-slate-500 font-medium">Hak Akses (Role)</p>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 mt-1">
                            {{ Auth::user()->role }}
                        </span>
                    </div>
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                        <p class="text-xs text-slate-500 font-medium">Tugas Tambahan (Wali Kelas)</p>
                        @php
                            $isWali = Auth::user()->isWaliKelas();
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $isWali ? 'bg-indigo-100 text-indigo-800' : 'bg-slate-100 text-slate-600' }} mt-1">
                            {{ $isWali ? 'Ditugaskan Sebagai Wali Kelas' : 'Bukan Wali Kelas' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
