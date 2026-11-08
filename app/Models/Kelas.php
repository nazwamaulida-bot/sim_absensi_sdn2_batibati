<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelas extends Model
{
    use HasFactory;

    /**
     * Nama tabel di database.
     *
     * @var string
     */
    protected $table = 'kelas';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nama_kelas',
        'tingkat',
    ];

    /**
     * Relasi ke seluruh siswa di kelas ini.
     */
    public function siswa(): HasMany
    {
        return $this->hasMany(Siswa::class, 'kelas_id');
    }

    /**
     * Relasi ke penugasan Wali Kelas pada kelas ini.
     */
    public function waliKelas(): HasMany
    {
        return $this->hasMany(WaliKelas::class, 'kelas_id');
    }

    /**
     * Relasi ke seluruh jadwal pelajaran di kelas ini.
     */
    public function jadwalPelajaran(): HasMany
    {
        return $this->hasMany(JadwalPelajaran::class, 'kelas_id');
    }

    /**
     * Relasi ke laporan terkait kelas ini.
     */
    public function laporan(): HasMany
    {
        return $this->hasMany(Laporan::class, 'kelas_id');
    }
}
