@extends('layouts.app')

@section('title', 'Manajemen Data Guru')
@section('page_title', 'Manajemen Data Guru')

@section('content')
<div class="space-y-6">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h3 class="text-lg font-bold text-slate-800">Daftar Tenaga Pendidik & Guru</h3>
            <p class="text-xs text-slate-500">Kelola master data guru, akun login, dan status kepegawaian.</p>
        </div>
        <a href="{{ route('admin.guru.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-sm transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Guru Baru
        </a>
    </div>

    <!-- Pencarian -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('admin.guru.index') }}" class="flex gap-2">
            <div class="relative flex-1">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama guru, NIP, atau email..."
                    class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <button type="submit" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold transition">
                Cari
            </button>
            @if ($search)
                <a href="{{ route('admin.guru.index') }}" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-medium transition">Reset</a>
            @endif
        </form>
    </div>

    <!-- Tabel Data Guru -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="py-3.5 px-4">No</th>
                        <th class="py-3.5 px-4">Nama Lengkap</th>
                        <th class="py-3.5 px-4">NIP</th>
                        <th class="py-3.5 px-4">Email Login</th>
                        <th class="py-3.5 px-4">No. HP</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($gurus as $index => $guru)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-4">{{ $gurus->firstItem() + $index }}</td>
                            <td class="py-3.5 px-4 font-semibold text-slate-900">{{ $guru->nama }}</td>
                            <td class="py-3.5 px-4 font-mono">{{ $guru->nip ?? '-' }}</td>
                            <td class="py-3.5 px-4">{{ $guru->email }}</td>
                            <td class="py-3.5 px-4">{{ $guru->no_hp ?? '-' }}</td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('admin.guru.show', $guru->id) }}" class="p-1.5 rounded-lg text-blue-600 hover:bg-blue-50 transition" title="Lihat Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    <a href="{{ route('admin.guru.edit', $guru->id) }}" class="p-1.5 rounded-lg text-amber-600 hover:bg-amber-50 transition" title="Edit Guru">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.guru.destroy', $guru->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data guru ini beserta akun loginnya?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-rose-600 hover:bg-rose-50 transition" title="Hapus Guru">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">Belum ada data guru yang tersimpan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($gurus->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $gurus->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
