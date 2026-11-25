<?php

use App\Http\Controllers\Admin\AbsensiGuruController as AdminAbsensiGuruController;
use App\Http\Controllers\Admin\AbsensiSiswaController as AdminAbsensiSiswaController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\GuruController as AdminGuruController;
use App\Http\Controllers\Admin\JadwalPelajaranController as AdminJadwalController;
use App\Http\Controllers\Admin\KelasController as AdminKelasController;
use App\Http\Controllers\Admin\LaporanController as AdminLaporanController;
use App\Http\Controllers\Admin\SiswaController as AdminSiswaController;
use App\Http\Controllers\Admin\TahunAjaranController as AdminTahunAjaranController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Guru\AbsensiController as GuruAbsensiController;
use App\Http\Controllers\Guru\DashboardController as GuruDashboardController;
use App\Http\Controllers\Guru\IzinController as GuruIzinController;
use App\Http\Controllers\Guru\WaliKelasController as GuruWaliKelasController;
use App\Http\Controllers\KepalaSekolah\DashboardController as KepalaSekolahDashboardController;
use App\Http\Controllers\KepalaSekolah\IzinApprovalController as KepalaSekolahIzinController;
use App\Http\Controllers\KepalaSekolah\LaporanController as KepalaSekolahLaporanController;
use App\Http\Controllers\KepalaSekolah\MonitoringController as KepalaSekolahMonitoringController;
use Illuminate\Support\Facades\Route;

// Halaman Awal dialihkan ke Login
Route::get('/', function () {
    return redirect()->route('login');
});

// Route Autentikasi untuk Tamu (Belum Login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.process');
});

// Route untuk Pengguna yang Sudah Login
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // ==========================================
    // 1. ROLE: ADMIN TU
    // ==========================================
    Route::middleware('role:admin_tu')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Master Data Guru & Akun Pengguna
        Route::resource('guru', AdminGuruController::class);

        // Master Data Siswa
        Route::resource('siswa', AdminSiswaController::class);

        // Master Data Rombel / Kelas & Penugasan Wali Kelas
        Route::resource('kelas', AdminKelasController::class)->except(['create', 'show', 'edit']);
        Route::post('kelas/{kelas}/tetapkan-wali', [AdminKelasController::class, 'tetapkanWali'])->name('kelas.tetapkan-wali');

        // Master Data Tahun Ajaran & Semester
        Route::resource('tahun-ajaran', AdminTahunAjaranController::class)->except(['create', 'show', 'edit']);
        Route::post('tahun-ajaran/{tahunAjaran}/set-aktif', [AdminTahunAjaranController::class, 'setAktif'])->name('tahun-ajaran.set-aktif');

        // Jadwal Pelajaran
        Route::resource('jadwal', AdminJadwalController::class)->except(['create', 'show', 'edit']);

        // Presensi & Rekapitulasi
        Route::get('absensi-guru', [AdminAbsensiGuruController::class, 'index'])->name('absensi-guru.index');
        Route::get('absensi-siswa', [AdminAbsensiSiswaController::class, 'index'])->name('absensi-siswa.index');

        // Laporan & Google Drive
        Route::get('laporan', [AdminLaporanController::class, 'index'])->name('laporan.index');
        Route::get('laporan/cetak/{laporan?}', [AdminLaporanController::class, 'cetak'])->name('laporan.cetak');
        Route::post('laporan/arsipkan', [AdminLaporanController::class, 'arsipkan'])->name('laporan.arsipkan');
    });

    // ==========================================
    // 2. ROLE: GURU
    // ==========================================
    Route::middleware('role:guru')->prefix('guru')->name('guru.')->group(function () {
        Route::get('/dashboard', [GuruDashboardController::class, 'index'])->name('dashboard');
        Route::get('/jadwal', [GuruDashboardController::class, 'jadwal'])->name('jadwal.index');

        // Presensi Mandiri (GPS Haversine & Verifikasi Wajah)
        Route::get('/absensi', [GuruAbsensiController::class, 'index'])->name('absensi.index');
        Route::post('/absensi/masuk', [GuruAbsensiController::class, 'absenMasuk'])->name('absensi.masuk');
        Route::post('/absensi/pulang', [GuruAbsensiController::class, 'absenPulang'])->name('absensi.pulang');

        // Pengajuan Izin / Sakit
        Route::get('/izin', [GuruIzinController::class, 'index'])->name('izin.index');
        Route::post('/izin', [GuruIzinController::class, 'store'])->name('izin.store');

        // Tugas Tambahan: Wali Kelas
        Route::prefix('wali-kelas')->name('wali-kelas.')->group(function () {
            Route::get('/siswa', [GuruWaliKelasController::class, 'siswa'])->name('siswa');
            Route::get('/absensi', [GuruWaliKelasController::class, 'absensi'])->name('absensi');
            Route::post('/absensi', [GuruWaliKelasController::class, 'simpanAbsensi'])->name('simpan-absensi');
            Route::get('/rekap', [GuruWaliKelasController::class, 'rekap'])->name('rekap');
        });
    });

    // ==========================================
    // 3. ROLE: KEPALA SEKOLAH
    // ==========================================
    Route::middleware('role:kepala_sekolah')->prefix('kepala-sekolah')->name('kepala_sekolah.')->group(function () {
        Route::get('/dashboard', [KepalaSekolahDashboardController::class, 'index'])->name('dashboard');

        // Persetujuan Izin Guru
        Route::get('/izin', [KepalaSekolahIzinController::class, 'index'])->name('izin.index');
        Route::post('/izin/{pengajuanIzin}/verify', [KepalaSekolahIzinController::class, 'verify'])->name('izin.verify');

        // Monitoring Khusus Aturan IF-THEN
        Route::get('/monitoring/guru', [KepalaSekolahMonitoringController::class, 'guru'])->name('monitoring.guru');
        Route::get('/monitoring/siswa', [KepalaSekolahMonitoringController::class, 'siswa'])->name('monitoring.siswa');

        // Laporan & Arsip Google Drive
        Route::get('/laporan', [KepalaSekolahLaporanController::class, 'index'])->name('laporan.index');
    });
});
