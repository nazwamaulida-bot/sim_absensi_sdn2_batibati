@extends('layouts.app')

@section('title', 'Jadwal Mengajar')
@section('page_title', 'Jadwal Mengajar Guru')

@section('content')
<div class="space-y-6">

    <!-- Header Info -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h3 class="text-base font-bold text-slate-800">Jadwal Mata Pelajaran Saya</h3>
            <p class="text-xs text-slate-500 mt-1">Guru: <strong>{{ $guru->nama ?? Auth::user()->name }}</strong> (NIP: {{ $guru->nip ?? '-' }})</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                Total {{ $jadwals->count() }} Jam / Sesi Pelajaran
            </span>
        </div>
    </div>

    <!-- Jadwal Pelajaran per Hari -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @php
            $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            $hariColors = [
                'Senin' => 'blue',
                'Selasa' => 'indigo',
                'Rabu' => 'emerald',
                'Kamis' => 'amber',
                'Jumat' => 'rose',
                'Sabtu' => 'purple',
            ];
        @endphp

        @foreach ($hariList as $hari)
            @php
                $jadwalHari = $jadwalPerHari->get($hari, collect());
                $color = $hariColors[$hari] ?? 'slate';
            @endphp
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
                <div class="bg-slate-50 px-5 py-3.5 border-b border-slate-200 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-{{ $color }}-500"></span>
                        <h4 class="font-bold text-slate-800 text-sm">{{ $hari }}</h4>
                    </div>
                    <span class="text-xs font-medium text-slate-500">{{ $jadwalHari->count() }} Mata Pelajaran</span>
                </div>

                <div class="p-4 flex-1 space-y-3">
                    @forelse ($jadwalHari as $item)
                        <div class="p-3 rounded-xl border border-slate-100 bg-slate-50/50 hover:bg-slate-50 transition">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="font-bold text-slate-800 text-xs">{{ $item->mata_pelajaran }}</span>
                                <span class="px-2 py-0.5 rounded-md bg-blue-100 text-blue-800 font-bold text-[10px]">
                                    Kelas {{ $item->kelas->nama_kelas ?? '-' }}
                                </span>
                            </div>
                            <div class="flex items-center gap-1.5 text-slate-500 text-[11px]">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>{{ substr($item->jam_mulai, 0, 5) }} - {{ substr($item->jam_selesai, 0, 5) }} WITA</span>
                            </div>
                        </div>
                    @empty
                        <div class="py-6 text-center text-slate-400 text-xs">
                            Tidak ada jadwal mengajar pada hari {{ $hari }}.
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>

</div>
@endsection
