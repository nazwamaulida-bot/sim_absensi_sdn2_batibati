@extends('layouts.app')

@section('title', 'Manajemen Jadwal Pelajaran')
@section('page_title', 'Jadwal Pelajaran')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h3 class="text-lg font-bold text-slate-800">Plotting Jadwal Mata Pelajaran</h3>
            <p class="text-xs text-slate-500">Jadwal ini digunakan sebagai rujukan pengajar dalam pencatatan presensi siswa.</p>
        </div>
    </div>

    <!-- Filter & Tambah Jadwal -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Form Tambah Jadwal -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm h-fit">
            <h4 class="text-sm font-bold text-slate-900 mb-3 flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Jadwal Pelajaran
            </h4>
            <form method="POST" action="{{ route('admin.jadwal.store') }}" class="space-y-3">
                @csrf
                <div>
                    <label for="kelas_id" class="block text-xs font-semibold text-slate-700 mb-1">Kelas *</label>
                    <select id="kelas_id" name="kelas_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach ($kelasList as $k)
                            <option value="{{ $k->id }}" {{ old('kelas_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="semester_id" class="block text-xs font-semibold text-slate-700 mb-1">Semester & TA *</label>
                    <select id="semester_id" name="semester_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Pilih Semester --</option>
                        @foreach ($semesters as $s)
                            <option value="{{ $s->id }}" {{ old('semester_id') == $s->id ? 'selected' : '' }}>
                                {{ $s->nama_semester }} - TA {{ $s->tahunAjaran->nama_tahun_ajaran ?? '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="mata_pelajaran" class="block text-xs font-semibold text-slate-700 mb-1">Mata Pelajaran *</label>
                    <input type="text" id="mata_pelajaran" name="mata_pelajaran" value="{{ old('mata_pelajaran') }}" required placeholder="Contoh: Matematika, Tematik, PJOK"
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label for="guru_id" class="block text-xs font-semibold text-slate-700 mb-1">Guru Pengampu Utama *</label>
                    <select id="guru_id" name="guru_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Pilih Guru --</option>
                        @foreach ($gurus as $g)
                            <option value="{{ $g->id }}" {{ old('guru_id') == $g->id ? 'selected' : '' }}>{{ $g->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="guru_pengganti_id" class="block text-xs font-semibold text-slate-700 mb-1">Guru Pengganti (Opsional)</label>
                    <select id="guru_pengganti_id" name="guru_pengganti_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Tidak Ada --</option>
                        @foreach ($gurus as $g)
                            <option value="{{ $g->id }}" {{ old('guru_pengganti_id') == $g->id ? 'selected' : '' }}>{{ $g->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                    <div>
                        <label for="hari" class="block text-xs font-semibold text-slate-700 mb-1">Hari *</label>
                        <select id="hari" name="hari" required class="w-full px-2 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $h)
                                <option value="{{ $h }}">{{ $h }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="jam_mulai" class="block text-xs font-semibold text-slate-700 mb-1">Mulai *</label>
                        <input type="time" id="jam_mulai" name="jam_mulai" required value="07:30"
                            class="w-full px-2 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="jam_selesai" class="block text-xs font-semibold text-slate-700 mb-1">Selesai *</label>
                        <input type="time" id="jam_selesai" name="jam_selesai" required value="09:00"
                            class="w-full px-2 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <button type="submit" class="w-full py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-sm transition">
                    Simpan Jadwal Pelajaran
                </button>
            </form>
        </div>

        <!-- Tabel Jadwal Pelajaran -->
        <div class="lg:col-span-2 space-y-4">
            <!-- Filter Bar -->
            <div class="bg-white p-3 rounded-2xl border border-slate-200 shadow-sm flex flex-wrap gap-2 items-center">
                <form method="GET" action="{{ route('admin.jadwal.index') }}" class="flex flex-wrap gap-2 w-full">
                    <select name="kelas_id" class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700">
                        <option value="">Semua Kelas</option>
                        @foreach ($kelasList as $k)
                            <option value="{{ $k->id }}" {{ $kelasId == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                    <select name="hari" class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700">
                        <option value="">Semua Hari</option>
                        @foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $h)
                            <option value="{{ $h }}" {{ $hari == $h ? 'selected' : '' }}>{{ $h }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-slate-800 text-white text-xs font-medium">Filter</button>
                    @if ($kelasId || $hari)
                        <a href="{{ route('admin.jadwal.index') }}" class="px-2.5 py-1.5 rounded-xl bg-slate-100 text-slate-600 text-xs">Reset</a>
                    @endif
                </form>
            </div>

            <!-- List Jadwal -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-700">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold uppercase tracking-wider text-[11px]">
                            <tr>
                                <th class="py-3 px-4">Hari & Jam</th>
                                <th class="py-3 px-4">Kelas</th>
                                <th class="py-3 px-4">Mata Pelajaran</th>
                                <th class="py-3 px-4">Guru Pengampu</th>
                                <th class="py-3 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($jadwals as $j)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-3 px-4">
                                        <span class="font-bold text-slate-900 block">{{ $j->hari }}</span>
                                        <span class="text-[11px] text-slate-500">{{ substr($j->jam_mulai, 0, 5) }} - {{ substr($j->jam_selesai, 0, 5) }}</span>
                                    </td>
                                    <td class="py-3 px-4 font-semibold text-blue-700">{{ $j->kelas->nama_kelas ?? '-' }}</td>
                                    <td class="py-3 px-4 font-semibold text-slate-800">{{ $j->mata_pelajaran }}</td>
                                    <td class="py-3 px-4">
                                        <span class="block text-slate-800">{{ $j->guru->nama ?? '-' }}</span>
                                        @if ($j->guruPengganti)
                                            <span class="text-[11px] text-amber-600 block">(Pengganti: {{ $j->guruPengganti->nama }})</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <form method="POST" action="{{ route('admin.jadwal.destroy', $j->id) }}" onsubmit="return confirm('Hapus jadwal ini?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1 rounded text-rose-600 hover:bg-rose-50" title="Hapus Jadwal">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-slate-400">Belum ada jadwal pelajaran yang cocok.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
