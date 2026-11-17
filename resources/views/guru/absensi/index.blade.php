@extends('layouts.app')

@section('title', 'Absensi Mandiri Guru')
@section('page_title', 'Presensi Mandiri (GPS & Wajah)')

@section('content')
<div class="space-y-6">

    <!-- Header & Info -->
    <div>
        <h3 class="text-lg font-bold text-slate-800">Presensi Mandiri Tenaga Pendidik</h3>
        <p class="text-xs text-slate-500">
            Titik Lokasi: <strong>UPTD SDN 2 Bati-Bati ({{ $schoolConfig['dms'] ?? '3°36\'17.3"S 114°42\'01.1"E' }})</strong> • Maksimal Radius: <strong>{{ $schoolConfig['radius'] }} Meter</strong> • Toleransi Jam Masuk: <strong>07:45 WITA</strong>.
        </p>
    </div>

    <!-- Panel Absensi Kamera & GPS -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Webcam & Snapshot Box -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
            <h4 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                1. Verifikasi Wajah (Kamera)
            </h4>

            <div class="relative bg-slate-950 rounded-xl overflow-hidden aspect-video flex items-center justify-center">
                <video id="webcam" autoplay playsinline class="w-full h-full object-cover"></video>
                <canvas id="snapshotCanvas" class="hidden"></canvas>
                <img id="snapshotPreview" class="hidden w-full h-full object-cover absolute inset-0 z-10" alt="Preview Foto">
                
                <!-- Overlay Face Scan Box -->
                <div class="absolute inset-0 border-2 border-dashed border-blue-400/50 m-6 rounded-2xl pointer-events-none flex items-center justify-center">
                    <span class="text-[11px] text-blue-300 font-medium bg-slate-900/60 px-2.5 py-1 rounded-full">Posisikan wajah Anda di tengah</span>
                </div>
            </div>

            <div class="flex gap-2">
                <button type="button" id="startCameraBtn" class="flex-1 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold transition">
                    Aktifkan Kamera
                </button>
                <button type="button" id="takeSnapshotBtn" class="flex-1 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold transition">
                    Ambil Foto Wajah
                </button>
            </div>
        </div>

        <!-- GPS Geolocation & Action Box -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between space-y-4">
            <div>
                <h4 class="text-sm font-bold text-slate-900 flex items-center gap-2 mb-3">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    2. Validasi Radius Lokasi (Metode Haversine)
                </h4>

                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs space-y-2">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Koordinat Anda:</span>
                        <span id="userCoordText" class="font-mono font-semibold text-slate-800">Mendeteksi GPS...</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Jarak ke SDN 2 Bati-Bati:</span>
                        <span id="distanceText" class="font-bold text-slate-800">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Status Radius:</span>
                        <span id="radiusStatusBadge" class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-200 text-slate-700 uppercase">
                            Menunggu GPS
                        </span>
                    </div>
                </div>

                <div class="mt-4 p-3 rounded-xl bg-blue-50 border border-blue-200 text-[11px] text-blue-900 space-y-1">
                    <p class="font-semibold">Aturan Jam Masuk & Keterlambatan:</p>
                    <p>• Masuk sebelum <strong>07:45 WITA</strong> dicatat sebagai <strong>HADIR TEPAT WAKTU</strong>.</p>
                    <p>• Masuk setelah <strong>07:45 WITA</strong> (&gt;15 menit) otomatis dicatat sebagai <strong>TERLAMBAT</strong>.</p>
                </div>
            </div>

            <!-- Formulir Submit Absensi Masuk & Pulang -->
            <div class="pt-4 border-t border-slate-100 space-y-3">
                @if (! $absensiHariIni || ! $absensiHariIni->jam_masuk)
                    <form method="POST" action="{{ route('guru.absensi.masuk') }}">
                        @csrf
                        <input type="hidden" name="latitude" id="latInput">
                        <input type="hidden" name="longitude" id="lonInput">
                        <input type="hidden" name="foto_wajah" id="fotoWajahInput">

                        <button type="submit" id="submitMasukBtn"
                            class="w-full py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-md shadow-emerald-500/20 transition flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                            Kirim Absensi Masuk
                        </button>
                    </form>
                @else
                    <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-800 text-center font-medium">
                        Anda telah absen masuk pukul <strong>{{ $absensiHariIni->jam_masuk }}</strong> 
                        ({{ strtoupper($absensiHariIni->status_kehadiran) }}).
                    </div>

                    @if (! $absensiHariIni->jam_pulang)
                        <form method="POST" action="{{ route('guru.absensi.pulang') }}">
                            @csrf
                            <button type="submit"
                                class="w-full py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-md shadow-blue-500/20 transition flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                Kirim Absensi Pulang
                            </button>
                        </form>
                    @else
                        <div class="p-3 rounded-xl bg-blue-50 border border-blue-200 text-xs text-blue-800 text-center font-medium">
                            Anda telah absen pulang pukul <strong>{{ $absensiHariIni->jam_pulang }}</strong>.
                        </div>
                    @endif
                @endif
            </div>
        </div>

    </div>

    <!-- Riwayat Absensi Guru Pribadi -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 font-bold text-xs uppercase tracking-wider text-slate-600">
            Riwayat Presensi Pribadi Anda
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="py-3 px-4">Tanggal</th>
                        <th class="py-3 px-4">Jam Masuk</th>
                        <th class="py-3 px-4">Jam Pulang</th>
                        <th class="py-3 px-4">Jarak (m)</th>
                        <th class="py-3 px-4">Verifikasi Wajah</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($riwayat as $item)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3 px-4 font-semibold text-slate-900">{{ date('d M Y', strtotime($item->tanggal)) }}</td>
                            <td class="py-3 px-4 font-mono font-medium">{{ $item->jam_masuk ?? '-' }}</td>
                            <td class="py-3 px-4 font-mono font-medium">{{ $item->jam_pulang ?? '-' }}</td>
                            <td class="py-3 px-4">{{ $item->jarak_meter ? $item->jarak_meter . ' m' : '-' }}</td>
                            <td class="py-3 px-4">
                                @if ($item->wajah_terverifikasi)
                                    <span class="text-emerald-700 font-semibold text-[11px] flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Valid
                                    </span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold {{ $item->status_kehadiran == 'hadir' ? 'bg-emerald-100 text-emerald-800' : ($item->status_kehadiran == 'terlambat' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800') }}">
                                    {{ ucfirst($item->status_kehadiran) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-slate-500">{{ $item->keterangan ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">Belum ada riwayat kehadiran.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@push('scripts')
<script>
    // 1. Geolokasi & Perhitungan Jarak Haversine Sisi Klien
    const schoolLat = {{ $schoolConfig['lat'] }};
    const schoolLon = {{ $schoolConfig['lon'] }};
    const maxRadius = {{ $schoolConfig['radius'] }};

    function haversineDistance(lat1, lon1, lat2, lon2) {
        const R = 6371000; // Radius bumi dalam meter
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                  Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                  Math.sin(dLon/2) * Math.sin(dLon/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return Math.round(R * c);
    }

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                const userLat = position.coords.latitude;
                const userLon = position.coords.longitude;

                document.getElementById('latInput').value = userLat;
                document.getElementById('lonInput').value = userLon;
                document.getElementById('userCoordText').textContent = userLat.toFixed(5) + ', ' + userLon.toFixed(5);

                const distance = haversineDistance(userLat, userLon, schoolLat, schoolLon);
                document.getElementById('distanceText').textContent = distance + ' meter';

                const badge = document.getElementById('radiusStatusBadge');
                if (distance <= maxRadius) {
                    badge.textContent = 'Dalam Radius (' + distance + ' m)';
                    badge.className = 'px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 uppercase';
                } else {
                    badge.textContent = 'Di Luar Radius (' + distance + ' m)';
                    badge.className = 'px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800 uppercase';
                }
            },
            function(error) {
                document.getElementById('userCoordText').textContent = 'Izin GPS Ditolak / Tidak Tersedia';
                document.getElementById('radiusStatusBadge').textContent = 'Gagal Deteksi Lokasi';
                document.getElementById('radiusStatusBadge').className = 'px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800 uppercase';
            },
            { enableHighAccuracy: true, timeout: 10000 }
        );
    }

    // 2. Kamera Webcam & Snapshot Wajah
    const video = document.getElementById('webcam');
    const canvas = document.getElementById('snapshotCanvas');
    const preview = document.getElementById('snapshotPreview');
    const startBtn = document.getElementById('startCameraBtn');
    const snapshotBtn = document.getElementById('takeSnapshotBtn');
    let streamActive = false;

    startBtn?.addEventListener('click', async function() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } });
            video.srcObject = stream;
            streamActive = true;
            startBtn.textContent = 'Kamera Aktif';
            startBtn.classList.replace('bg-slate-800', 'bg-emerald-600');
        } catch (err) {
            alert('Tidak dapat mengakses kamera: ' + err.message);
        }
    });

    snapshotBtn?.addEventListener('click', function() {
        if (!streamActive) {
            alert('Aktifkan kamera terlebih dahulu!');
            return;
        }

        canvas.width = video.videoWidth || 640;
        canvas.height = video.videoHeight || 480;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        const dataUrl = canvas.toDataURL('image/jpeg', 0.8);
        document.getElementById('fotoWajahInput').value = dataUrl;
        preview.src = dataUrl;
        preview.classList.remove('hidden');

        snapshotBtn.textContent = 'Foto Berhasil Diambil';
        snapshotBtn.classList.replace('bg-blue-600', 'bg-emerald-600');
    });
</script>
@endpush
@endsection
