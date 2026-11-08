<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Guru extends Model
{
    use HasFactory;

    /**
     * Nama tabel di database.
     *
     * @var string
     */
    protected $table = 'guru';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nip',
        'nama',
        'email',
        'no_hp',
        'data_wajah',
    ];

    /**
     * Relasi ke akun User (1 guru memiliki 1 user login).
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'guru_id');
    }

    /**
     * Relasi ke penugasan Wali Kelas.
     */
    public function waliKelas(): HasMany
    {
        return $this->hasMany(WaliKelas::class, 'guru_id');
    }

    /**
     * Relasi ke Jadwal Pelajaran sebagai guru utama.
     */
    public function jadwalPelajaran(): HasMany
    {
        return $this->hasMany(JadwalPelajaran::class, 'guru_id');
    }

    /**
     * Relasi ke Jadwal Pelajaran sebagai guru pengganti.
     */
    public function jadwalPengganti(): HasMany
    {
        return $this->hasMany(JadwalPelajaran::class, 'guru_pengganti_id');
    }

    /**
     * Relasi ke riwayat Absensi Guru.
     */
    public function absensiGuru(): HasMany
    {
        return $this->hasMany(AbsensiGuru::class, 'guru_id');
    }

    /**
     * Relasi ke riwayat Pengajuan Izin / Sakit Guru.
     */
    public function pengajuanIzin(): HasMany
    {
        return $this->hasMany(PengajuanIzin::class, 'guru_id');
    }
}
