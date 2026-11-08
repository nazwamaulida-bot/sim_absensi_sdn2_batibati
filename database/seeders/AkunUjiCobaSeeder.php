<?php

namespace Database\Seeders;

use App\Models\AbsensiGuru;
use App\Models\AbsensiSiswa;
use App\Models\Guru;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\PengajuanIzin;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\User;
use App\Models\WaliKelas;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AkunUjiCobaSeeder extends Seeder
{
    /**
     * Menjalankan seeder akun pengguna dan data awal untuk kebutuhan pengujian sistem.
     */
    public function run(): void
    {
        // 1. Akun Uji Coba: Admin TU
        $admin = User::updateOrCreate(
            ['email' => 'admin@sdn2batibati.sch.id'],
            [
                'name' => 'Administrator TU',
                'password' => Hash::make('password123'),
                'role' => 'admin_tu',
                'status' => 'aktif',
                'guru_id' => null,
            ]
        );

        // 2. Data Master Guru & Akun Uji Coba: Guru
        $guru = Guru::updateOrCreate(
            ['nip' => '198507122010011005'],
            [
                'nama' => 'Budi Santoso, S.Pd.',
                'email' => 'guru@sdn2batibati.sch.id',
                'no_hp' => '081234567890',
                'data_wajah' => null,
            ]
        );

        $userGuru = User::updateOrCreate(
            ['email' => 'guru@sdn2batibati.sch.id'],
            [
                'name' => 'Budi Santoso, S.Pd.',
                'password' => Hash::make('password123'),
                'role' => 'guru',
                'status' => 'aktif',
                'guru_id' => $guru->id,
            ]
        );

        // 3. Akun Uji Coba: Kepala Sekolah
        $kepsek = User::updateOrCreate(
            ['email' => 'kepsek@sdn2batibati.sch.id'],
            [
                'name' => 'Hj. Siti Rahmah, M.Pd.',
                'password' => Hash::make('password123'),
                'role' => 'kepala_sekolah',
                'status' => 'aktif',
                'guru_id' => null,
            ]
        );

        // 4. Tahun Ajaran Aktif & Semester
        $tahunAjaran = TahunAjaran::updateOrCreate(
            ['nama_tahun_ajaran' => '2024/2025'],
            [
                'tanggal_mulai' => '2024-07-15',
                'tanggal_selesai' => '2025-06-20',
                'status_aktif' => true,
            ]
        );

        $semester = Semester::updateOrCreate(
            [
                'tahun_ajaran_id' => $tahunAjaran->id,
                'nama_semester' => 'Ganjil',
            ],
            [
                'tanggal_mulai' => '2024-07-15',
                'tanggal_selesai' => '2024-12-20',
            ]
        );

        // 5. Data Kelas (SDN 2 Bati-Bati: Kelas 1 s/d Kelas 6)
        $kelas1 = Kelas::updateOrCreate(['nama_kelas' => 'Kelas 1'], ['tingkat' => 1]);
        $kelas2 = Kelas::updateOrCreate(['nama_kelas' => 'Kelas 2'], ['tingkat' => 2]);
        Kelas::updateOrCreate(['nama_kelas' => 'Kelas 3'], ['tingkat' => 3]);
        Kelas::updateOrCreate(['nama_kelas' => 'Kelas 4'], ['tingkat' => 4]);
        Kelas::updateOrCreate(['nama_kelas' => 'Kelas 5'], ['tingkat' => 5]);
        Kelas::updateOrCreate(['nama_kelas' => 'Kelas 6'], ['tingkat' => 6]);

        // 6. Penugasan Wali Kelas (Pak Budi Santoso sebagai Wali Kelas 1 di TA 2024/2025)
        WaliKelas::updateOrCreate(
            [
                'kelas_id' => $kelas1->id,
                'tahun_ajaran_id' => $tahunAjaran->id,
            ],
            [
                'guru_id' => $guru->id,
            ]
        );

        // 7. Data Siswa
        $siswa1 = Siswa::updateOrCreate(
            ['nis' => '1001'],
            [
                'nama' => 'Ahmad Rizki Pratama',
                'kelas_id' => $kelas1->id,
                'jenis_kelamin' => 'L',
                'status' => 'aktif',
            ]
        );

        $siswa2 = Siswa::updateOrCreate(
            ['nis' => '1002'],
            [
                'nama' => 'Siti Nur Aisyah',
                'kelas_id' => $kelas1->id,
                'jenis_kelamin' => 'P',
                'status' => 'aktif',
            ]
        );

        $siswa3 = Siswa::updateOrCreate(
            ['nis' => '1003'],
            [
                'nama' => 'Muhammad Fajar',
                'kelas_id' => $kelas1->id,
                'jenis_kelamin' => 'L',
                'status' => 'aktif',
            ]
        );

        // 8. Jadwal Pelajaran
        $jadwal1 = JadwalPelajaran::updateOrCreate(
            [
                'kelas_id' => $kelas1->id,
                'mata_pelajaran' => 'Pendidikan Pancasila',
                'hari' => 'Senin',
            ],
            [
                'guru_id' => $guru->id,
                'guru_pengganti_id' => null,
                'semester_id' => $semester->id,
                'jam_mulai' => '07:30:00',
                'jam_selesai' => '09:00:00',
            ]
        );

        $jadwal2 = JadwalPelajaran::updateOrCreate(
            [
                'kelas_id' => $kelas1->id,
                'mata_pelajaran' => 'Bahasa Indonesia',
                'hari' => 'Selasa',
            ],
            [
                'guru_id' => $guru->id,
                'guru_pengganti_id' => null,
                'semester_id' => $semester->id,
                'jam_mulai' => '07:30:00',
                'jam_selesai' => '09:00:00',
            ]
        );

        // 9. Sample Pengajuan Izin Guru (untuk diuji oleh Kepala Sekolah)
        PengajuanIzin::updateOrCreate(
            [
                'guru_id' => $guru->id,
                'tanggal_mulai' => Carbon::now()->addDays(2)->format('Y-m-d'),
            ],
            [
                'jenis' => 'izin',
                'tanggal_selesai' => Carbon::now()->addDays(2)->format('Y-m-d'),
                'keterangan' => 'Menghadiri bimbingan teknis kurikulum merdeka tingkat kecamatan',
                'dokumen_pendukung' => null,
                'status' => 'menunggu',
                'diverifikasi_oleh' => null,
                'catatan_kepsek' => null,
            ]
        );
    }
}
