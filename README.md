# Sistem Informasi Manajemen Absensi Guru dan Siswa Berbasis Web Terintegrasi Google Drive pada UPTD SDN 2 Bati-Bati

[![Laravel Version](https://img.shields.io/badge/Laravel-13.x-red.svg)](https://laravel.com)
[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-blue.svg)](https://php.net)
[![Database](https://img.shields.io/badge/MySQL-8.0%2B-orange.svg)](https://mysql.com)
[![License](https://img.shields.io/badge/License-Academic-green.svg)]()

Aplikasi web Sistem Informasi Manajemen (SIM) Absensi yang dikembangkan sebagai proyek Tugas Akhir pada **UPTD SDN 2 Bati-Bati, Kecamatan Bati-Bati, Kabupaten Tanah Laut, Kalimantan Selatan**.

---

## 🌟 Fitur Utama Sistem

### 1. Multi-Role & Hak Akses Berjenjang
* **Administrator Tata Usaha (TU)**: Manajemen data induk guru, siswa, kelas, rombel, tahun ajaran, semester, jadwal pelajaran, monitoring kehadiran harian, dan pencetakan laporan.
* **Guru & Wali Kelas**:
  * **Guru Mata Pelajaran**: Presensi masuk & pulang mandiri, pengajuan izin/sakit dengan bukti surat, dan pemantauan jadwal mengajar mingguan.
  * **Tugas Tambahan Wali Kelas**: Akses khusus untuk pencatatan kehadiran siswa per mata pelajaran, rekapitulasi kehadiran kelas, dan tindak lanjut bimbingan siswa.
* **Kepala Sekolah**: Dashboard eksekutif, persetujuan/penolakan izin sakit guru, monitoring keterlambatan guru (> 15 menit), monitoring siswa kehadiran rendah (< 75%), dan arsip dokumen laporan.

### 2. Presensi Mandiri Guru (GPS Haversine & Verifikasi Kamera Wajah)
* Validasi radius lokasi sekolah (UPTD SDN 2 Bati-Bati di koordinat `3°36'17.3"S 114°42'01.1"E`) dengan jarak toleransi maksimal **100 meter** menggunakan perhitungan jarak lingkaran besar (*Haversine Formula*).
* Capture foto wajah secara *real-time* via kamera webcam laptop/ponsel sebagai bukti otentik kehadiran fisik.

### 3. Dua Aturan Evaluasi IF-THEN
* **Aturan 1 (Kehadiran Rendah Siswa < 75%)**: 
  $$\text{Persentase} = \left(\frac{\text{Hadir}}{\text{Total Pertemuan}}\right) \times 100\%$$
  Jika $\text{Persentase} < 75\%$, sistem secara otomatis memberikan status **Peringatan Dini**, menampilkan alert pada dashboard Wali Kelas & Kepala Sekolah, dan merekomendasikan penerbitan surat panggilan orang tua / bimbingan khusus.
* **Aturan 2 (Keterlambatan Masuk Guru > 15 Menit)**:
  Jam masuk baku ditetapkan pukul **07:30 WITA** dengan toleransi maksimal 15 menit (**07:45 WITA**). Presensi masuk setelah pukul 07:45 WITA otomatis tercatat sebagai **Terlambat** beserta kalkulasi menit keterlambatan.

### 4. Pelaporan Resmi & Integrasi Google Drive
* Format cetak laporan ramah printer/PDF berstandar kedinasan lengkap dengan kop surat resmi UPTD SDN 2 Bati-Bati dan lembar pengesahan Kepala Sekolah.
* Fitur arsip otomatis dan sinkronisasi berkas rekapitulasi presensi ke Google Drive sekolah melalui integrasi REST API Google Drive v3.

---

## 🛠️ Arsitektur & Teknologi

* **Backend Framework**: Laravel 13 (PHP 8.2+)
* **Basis Data**: MySQL (15 Migrations, 12 Eloquent Models)
* **Frontend UI**: Tailwind CSS, Blade Template Engine, Chart.js
* **Geolokasi**: HTML5 Geolocation API & Backend Haversine Distance Calculation
* **Multimedia**: HTML5 MediaDevices Webcam Capture (Canvas Base64)
* **Cloud Storage**: Google Drive API Integration Service

---

## 🚀 Panduan Instalasi Lokal

1. **Clone Repository**:
   ```bash
   git clone https://github.com/nazwamaulida-bot/sim_absensi_sdn2_batibati.git
   cd sim_absensi_sdn2_batibati
   ```

2. **Install Dependensi PHP**:
   ```bash
   composer install
   ```

3. **Konfigurasi Environment**:
   Salin `.env.example` menjadi `.env` lalu sesuaikan konfigurasi database:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Konfigurasi Basis Data di `.env`**:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=db_absensi_sdn2_batibati
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Jalankan Migrasi & Seeder Data Uji Coba**:
   ```bash
   php artisan migrate
   php artisan db:seed --class=AkunUjiCobaSeeder
   php artisan storage:link
   ```

6. **Jalankan Server Lokal**:
   ```bash
   php artisan serve
   ```
   Akses aplikasi pada peramban web di: `http://127.0.0.1:8000`

---

## 🔑 Kredensial Akun Uji Coba

| Peran (Role) | Email | Password | Keterangan |
| :--- | :--- | :--- | :--- |
| **Admin TU** | `admin@sdn2batibati.sch.id` | `password123` | Administrator Sistem & Master Data |
| **Guru / Wali Kelas** | `guru@sdn2batibati.sch.id` | `password123` | Budi Santoso, S.Pd. (Wali Kelas 1) |
| **Kepala Sekolah** | `kepsek@sdn2batibati.sch.id` | `password123` | Hj. Siti Rahmah, M.Pd. |

---

## 👩‍🎓 Penulis & Instansi
* **Mahasiswa**: Nazwa Maulida
* **Program Studi**: Teknik Informatika / Sistem Informasi
* **Kampus**: Politeknik Negeri Tanah Laut (Politala)
* **Lokasi Penelitian**: UPTD SDN 2 Bati-Bati, Kabupaten Tanah Laut, Kalimantan Selatan
