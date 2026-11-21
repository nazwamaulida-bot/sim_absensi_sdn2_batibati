@extends('layouts.app')

@section('title', 'Input Absensi Siswa - Kelas ' . ($kelas->nama_kelas ?? ''))
@section('page_title', 'Pencatatan Presensi Siswa Kelas')

@section('content')
<div class="space-y-6">

    <!-- Filter Jadwal & Tanggal -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('guru.wali-kelas.absensi') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <div>
                <label for="tanggal" class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Presensi</label>
                <input type="date" name="tanggal" id="tanggal" value="{{ $tanggal }}" class="w-full text-xs rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label for="jadwal_id" class="block text-xs font-semibold text-slate-700 mb-1">Mata Pelajaran / Sesi</label>
                <select name="jadwal_id" id="jadwal_id" class="w-full text-xs rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                    @forelse ($jadwals as $jadwal)
                        <option value="{{ $jadwal->id }}" {{ $selectedJadwal && $selectedJadwal->id == $jadwal->id ? 'selected' : '' }}>
                            {{ $jadwal->hari }} • {{ $jadwal->mata_pelajaran }} ({{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }})
                        </option>
                    @empty
                        <option value="">Belum ada jadwal untuk kelas ini</option>
                    @endforelse
                </select>
            </div>

            <div>
                <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold shadow-sm transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    @if ($selectedJadwal)
        <!-- Form Pencatatan Presensi Siswa -->
        <form action="{{ route('guru.wali-kelas.simpan-absensi') }}" method="POST">
            @csrf
            <input type="hidden" name="jadwal_id" value="{{ $selectedJadwal->id }}">
            <input type="hidden" name="tanggal" value="{{ $tanggal }}">

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <!-- Info Bar & Tombol Cepat Hadir Semua -->
                <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                    <div>
                        <h4 class="text-sm font-bold text-slate-800">
                            {{ $kelas->nama_kelas }} • {{ $selectedJadwal->mata_pelajaran }}
                        </h4>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Tanggal: <strong>{{ Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}</strong> | Guru Pengampu: {{ $selectedJadwal->guru->nama ?? '-' }}
                        </p>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" onclick="setSemuaHadir()" class="px-3 py-1.5 rounded-lg border border-emerald-300 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 text-xs font-semibold transition">
                            Centang Semua Hadir
                        </button>
                        <button type="submit" class="px-4 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-sm transition">
                            Simpan Presensi
                        </button>
                    </div>
                </div>

                <!-- Tabel Input Presensi -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-slate-100 text-slate-400 uppercase tracking-wider text-[10px] bg-white">
                                <th class="py-3 px-4 w-12 text-center">No</th>
                                <th class="py-3 px-4">Nama Siswa</th>
                                <th class="py-3 px-4">NIS</th>
                                <th class="py-3 px-4 text-center">Status Kehadiran</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse ($siswas as $idx => $siswa)
                                @php
                                    $currentStatus = $absensiMap[$siswa->id] ?? 'hadir';
                                @endphp
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="py-3 px-4 text-center text-slate-400 font-medium">{{ $idx + 1 }}</td>
                                    <td class="py-3 px-4">
                                        <span class="font-bold text-slate-900 block">{{ $siswa->nama }}</span>
                                        <span class="text-[10px] text-slate-400">{{ $siswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                                    </td>
                                    <td class="py-3 px-4 font-mono text-slate-500">{{ $siswa->nis ?? '-' }}</td>
                                    <td class="py-3 px-4">
                                        <div class="flex items-center justify-center gap-3">
                                            <label class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 cursor-pointer has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50 has-[:checked]:text-emerald-800 transition">
                                                <input type="radio" name="kehadiran[{{ $siswa->id }}]" value="hadir" class="kehadiran-radio kehadiran-hadir text-emerald-600 focus:ring-emerald-500" {{ $currentStatus == 'hadir' ? 'checked' : '' }}>
                                                <span class="text-xs font-semibold">Hadir</span>
                                            </label>

                                            <label class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 cursor-pointer has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50 has-[:checked]:text-blue-800 transition">
                                                <input type="radio" name="kehadiran[{{ $siswa->id }}]" value="izin" class="kehadiran-radio text-blue-600 focus:ring-blue-500" {{ $currentStatus == 'izin' ? 'checked' : '' }}>
                                                <span class="text-xs font-semibold">Izin</span>
                                            </label>

                                            <label class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 cursor-pointer has-[:checked]:border-amber-500 has-[:checked]:bg-amber-50 has-[:checked]:text-amber-800 transition">
                                                <input type="radio" name="kehadiran[{{ $siswa->id }}]" value="sakit" class="kehadiran-radio text-amber-600 focus:ring-amber-500" {{ $currentStatus == 'sakit' ? 'checked' : '' }}>
                                                <span class="text-xs font-semibold">Sakit</span>
                                            </label>

                                            <label class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 cursor-pointer has-[:checked]:border-rose-500 has-[:checked]:bg-rose-50 has-[:checked]:text-rose-800 transition">
                                                <input type="radio" name="kehadiran[{{ $siswa->id }}]" value="alpha" class="kehadiran-radio text-rose-600 focus:ring-rose-500" {{ $currentStatus == 'alpha' ? 'checked' : '' }}>
                                                <span class="text-xs font-semibold">Alpha</span>
                                            </label>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-slate-400">
                                        Tidak ada siswa terdaftar di kelas ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end">
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-sm transition">
                        Simpan Seluruh Presensi
                    </button>
                </div>
            </div>
        </form>
    @else
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 text-center text-amber-800">
            <h4 class="font-bold text-sm">Tidak Ada Jadwal Pelajaran yang Dipilih</h4>
            <p class="text-xs mt-1">Silakan hubungi Admin TU untuk membuat jadwal pelajaran bagi {{ $kelas->nama_kelas }}.</p>
        </div>
    @endif

</div>

<script>
    function setSemuaHadir() {
        document.querySelectorAll('.kehadiran-hadir').forEach(function(radio) {
            radio.checked = true;
            // Trigger visual style update
            radio.dispatchEvent(new Event('change'));
        });
    }
</script>
@endsection
