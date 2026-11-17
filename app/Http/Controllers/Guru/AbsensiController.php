<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\AbsensiGuru;
use App\Services\HaversineService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AbsensiController extends Controller
{
    /**
     * Menampilkan form absensi mandiri (kamera & GPS) dan riwayat kehadiran guru.
     */
    public function index(): View
    {
        $guru = Auth::user()->guru;
        $today = Carbon::today()->format('Y-m-d');

        $absensiHariIni = $guru ? AbsensiGuru::where('guru_id', $guru->id)->where('tanggal', $today)->first() : null;

        $riwayat = $guru ? AbsensiGuru::where('guru_id', $guru->id)
            ->orderBy('tanggal', 'desc')
            ->paginate(15) : collect();

        $schoolConfig = [
            'lat' => config('school.latitude', -3.604806),
            'lon' => config('school.longitude', 114.700306),
            'dms' => config('school.koordinat_dms', '3°36\'17.3"S 114°42\'01.1"E'),
            'radius' => config('school.radius_meter', 100),
            'jam_masuk' => config('school.jam_masuk', '07:30:00'),
            'toleransi_menit' => config('school.toleransi_terlambat_menit', 15),
        ];

        return view('guru.absensi.index', compact('guru', 'absensiHariIni', 'riwayat', 'schoolConfig'));
    }

    /**
     * Memproses Absensi Masuk (Check-In) dengan kalkulasi radius Haversine & verifikasi wajah.
     */
    public function absenMasuk(Request $request): RedirectResponse
    {
        $request->validate([
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
            'foto_wajah' => ['nullable', 'string'], // Data snapshot kamera base64
        ], [
            'latitude.required' => 'Koordinat lokasi GPS tidak terdeteksi. Pastikan GPS aktif.',
            'longitude.required' => 'Koordinat lokasi GPS tidak terdeteksi. Pastikan GPS aktif.',
        ]);

        $guru = Auth::user()->guru;
        if (! $guru) {
            return back()->with('error', 'Akun Anda belum ditautkan ke data master Guru.');
        }

        $today = Carbon::today()->format('Y-m-d');
        $now = Carbon::now();
        $jamSekarang = $now->format('H:i:s');

        // 1. Cek apakah sudah pernah absen masuk hari ini
        $existing = AbsensiGuru::where('guru_id', $guru->id)->where('tanggal', $today)->first();
        if ($existing && $existing->jam_masuk) {
            return back()->with('error', 'Anda sudah melakukan absensi masuk hari ini pada pukul ' . $existing->jam_masuk);
        }

        // 2. Validasi Jarak Radius Sekolah Menggunakan Metode Haversine
        $geoCheck = HaversineService::checkInSchoolRadius((float) $request->latitude, (float) $request->longitude);
        $jarakMeter = $geoCheck['distance_meters'];

        if (! $geoCheck['in_radius']) {
            return back()->with('error', sprintf(
                'Posisi Anda berada di luar radius sekolah! Jarak Anda: %s meter (Maksimal radius: %s meter). Silakan mendekat ke area UPTD SDN 2 Bati-Bati.',
                $jarakMeter,
                $geoCheck['max_radius']
            ));
        }

        // 3. Penerapan Aturan IF-THEN Keterlambatan:
        // Jika guru datang lebih dari 15 menit dari jam masuk sekolah (07:30 + 15m = 07:45), tandai 'terlambat'
        $jamMasukSekolah = Carbon::createFromTimeString(config('school.jam_masuk', '07:30:00'));
        $toleransiMenit = config('school.toleransi_terlambat_menit', 15);
        $batasToleransi = (clone $jamMasukSekolah)->addMinutes($toleransiMenit);

        $statusKehadiran = 'hadir';
        $keterangan = 'Absensi mandiri di area sekolah';

        if ($now->greaterThan($batasToleransi)) {
            $statusKehadiran = 'terlambat';
            $selisihMenit = $now->diffInMinutes($jamMasukSekolah);
            $keterangan = "Terlambat {$selisihMenit} menit dari jadwal masuk ({$jamMasukSekolah->format('H:i')})";
        }

        // 4. Verifikasi Wajah (Simpan snapshot webcam)
        $wajahTerverifikasi = ! empty($request->foto_wajah);

        // 5. Simpan Record Absensi Guru
        AbsensiGuru::updateOrCreate(
            [
                'guru_id' => $guru->id,
                'tanggal' => $today,
            ],
            [
                'jam_masuk' => $jamSekarang,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'jarak_meter' => $jarakMeter,
                'wajah_terverifikasi' => $wajahTerverifikasi,
                'status_kehadiran' => $statusKehadiran,
                'keterangan' => $keterangan,
            ]
        );

        $pesanSukses = $statusKehadiran === 'terlambat'
            ? "Absensi masuk tercatat pukul {$jamSekarang} (Status: TERLAMBAT, {$keterangan})."
            : "Absensi masuk berhasil tercatat pukul {$jamSekarang} tepat waktu (Jarak: {$jarakMeter} m).";

        return redirect()->route('guru.absensi.index')->with($statusKehadiran === 'terlambat' ? 'error' : 'success', $pesanSukses);
    }

    /**
     * Memproses Absensi Pulang (Check-Out).
     */
    public function absenPulang(Request $request): RedirectResponse
    {
        $guru = Auth::user()->guru;
        $today = Carbon::today()->format('Y-m-d');
        $jamSekarang = Carbon::now()->format('H:i:s');

        $absensi = AbsensiGuru::where('guru_id', $guru->id)->where('tanggal', $today)->first();

        if (! $absensi || ! $absensi->jam_masuk) {
            return back()->with('error', 'Anda belum melakukan absensi masuk hari ini.');
        }

        if ($absensi->jam_pulang) {
            return back()->with('error', 'Anda sudah melakukan absensi pulang pada pukul ' . $absensi->jam_pulang);
        }

        $absensi->update([
            'jam_pulang' => $jamSekarang,
        ]);

        return redirect()->route('guru.absensi.index')->with('success', "Absensi pulang berhasil dicatat pada pukul {$jamSekarang}. Terima kasih!");
    }
}
