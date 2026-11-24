<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan - UPTD SDN 2 Bati-Bati</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Times New Roman', Times, serif; }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; background: white; }
        }
    </style>
</head>
<body class="bg-slate-100 p-8 text-black">

    <!-- Floating Action Toolbar (No Print) -->
    <div class="no-print max-w-4xl mx-auto mb-6 flex justify-between items-center bg-white p-4 rounded-xl shadow-md border border-slate-200">
        <div>
            <span class="font-bold text-sm text-slate-800">Pratinjau Laporan Siap Cetak / Unduh PDF</span>
            <p class="text-xs text-slate-500">Klik tombol di samping untuk mencetak atau menyimpan sebagai PDF.</p>
        </div>
        <div class="flex gap-2">
            <button onclick="window.print()" class="px-5 py-2 rounded-lg bg-blue-600 text-white font-semibold text-xs hover:bg-blue-700 shadow transition flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak / Simpan PDF
            </button>
            <button onclick="window.close()" class="px-4 py-2 rounded-lg border border-slate-300 text-slate-700 font-semibold text-xs hover:bg-slate-50 transition">
                Tutup
            </button>
        </div>
    </div>

    <!-- Halaman Dokumen Resmi -->
    <div class="max-w-4xl mx-auto bg-white p-12 shadow-lg border border-slate-200 rounded-lg min-h-[1000px]">
        
        <!-- KOP SURAT SEKOLAH -->
        <div class="text-center border-b-4 border-double border-black pb-4 mb-6">
            <h3 class="text-base font-bold uppercase tracking-wider">Pemerintah Kabupaten Tanah Laut</h3>
            <h2 class="text-lg font-bold uppercase tracking-wider">Dinas Pendidikan dan Kebudayaan</h2>
            <h1 class="text-2xl font-black uppercase tracking-wide">UPTD SDN 2 Bati-Bati</h1>
            <p class="text-xs italic mt-1">Alamat: Kecamatan Bati-Bati, Kabupaten Tanah Laut, Kalimantan Selatan - Kode Pos 70852</p>
        </div>

        <!-- Judul Laporan -->
        <div class="text-center mb-6">
            <h2 class="text-base font-bold uppercase underline">
                {{ $jenis == 'absensi_guru' ? 'Laporan Rekapitulasi Presensi Tenaga Pendidik & Guru' : 'Laporan Rekapitulasi Presensi Peserta Didik' }}
            </h2>
            <p class="text-xs mt-1">
                Periode: <strong>{{ date('d F Y', strtotime($mulai)) }}</strong> s/d <strong>{{ date('d F Y', strtotime($selesai)) }}</strong>
            </p>
            <p class="text-xs">
                Semester: <strong>{{ $semester->nama_semester }}</strong> • Tahun Ajaran: <strong>{{ $semester->tahunAjaran->nama_tahun_ajaran ?? '-' }}</strong>
                @if ($kelas)
                    • Rombel: <strong>{{ $kelas->nama_kelas }}</strong>
                @endif
            </p>
        </div>

        <!-- Tabel Data -->
        @if ($jenis == 'absensi_guru')
            <table class="w-full text-xs border border-black mb-8">
                <thead>
                    <tr class="bg-slate-100 border-b border-black text-center font-bold">
                        <th class="border border-black p-2 w-10">No</th>
                        <th class="border border-black p-2">Tanggal</th>
                        <th class="border border-black p-2">NIP</th>
                        <th class="border border-black p-2">Nama Guru</th>
                        <th class="border border-black p-2">Masuk</th>
                        <th class="border border-black p-2">Pulang</th>
                        <th class="border border-black p-2">Status</th>
                        <th class="border border-black p-2">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $index => $item)
                        <tr>
                            <td class="border border-black p-2 text-center">{{ $index + 1 }}</td>
                            <td class="border border-black p-2 text-center">{{ date('d/m/Y', strtotime($item->tanggal)) }}</td>
                            <td class="border border-black p-2 font-mono">{{ $item->guru->nip ?? '-' }}</td>
                            <td class="border border-black p-2 font-semibold">{{ $item->guru->nama ?? '-' }}</td>
                            <td class="border border-black p-2 text-center">{{ $item->jam_masuk ?? '-' }}</td>
                            <td class="border border-black p-2 text-center">{{ $item->jam_pulang ?? '-' }}</td>
                            <td class="border border-black p-2 text-center font-bold">{{ strtoupper($item->status_kehadiran) }}</td>
                            <td class="border border-black p-2">{{ $item->keterangan ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="border border-black p-4 text-center italic">Tidak ada rekaman kehadiran untuk periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @else
            <table class="w-full text-xs border border-black mb-8">
                <thead>
                    <tr class="bg-slate-100 border-b border-black text-center font-bold">
                        <th class="border border-black p-2 w-10">No</th>
                        <th class="border border-black p-2">Tanggal</th>
                        <th class="border border-black p-2">NIS</th>
                        <th class="border border-black p-2">Nama Siswa</th>
                        <th class="border border-black p-2">Mata Pelajaran</th>
                        <th class="border border-black p-2">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $index => $item)
                        <tr>
                            <td class="border border-black p-2 text-center">{{ $index + 1 }}</td>
                            <td class="border border-black p-2 text-center">{{ date('d/m/Y', strtotime($item->tanggal)) }}</td>
                            <td class="border border-black p-2 text-center font-mono">{{ $item->siswa->nis ?? '-' }}</td>
                            <td class="border border-black p-2 font-semibold">{{ $item->siswa->nama ?? '-' }}</td>
                            <td class="border border-black p-2">{{ $item->jadwalPelajaran->mata_pelajaran ?? '-' }}</td>
                            <td class="border border-black p-2 text-center font-bold">{{ strtoupper($item->status_kehadiran) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="border border-black p-4 text-center italic">Tidak ada rekaman presensi siswa untuk periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @endif

        <!-- Tanda Tangan Pengesahan -->
        <div class="grid grid-cols-2 mt-12 text-xs">
            <div></div>
            <div class="text-center">
                <p>Bati-Bati, {{ Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
                <p class="font-bold mt-1">Kepala Sekolah UPTD SDN 2 Bati-Bati</p>
                <div class="h-20"></div>
                <p class="font-bold underline text-sm">Hj. Siti Rahmah, M.Pd.</p>
                <p>NIP. 196803151991032008</p>
            </div>
        </div>

    </div>

</body>
</html>
