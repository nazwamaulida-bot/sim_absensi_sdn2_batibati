<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JadwalPelajaran extends Model
{
    use HasFactory;

    /**
     * Nama tabel di database.
     *
     * @var string
     */
    protected $table = 'jadwal_pelajaran';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var list<string>
     */
    protected $fillable = [
        'guru_id',
        'guru_pengganti_id',
        'kelas_id',
        'semester_id',
        'mata_pelajaran',
        'hari',
        'jam_mulai',
        'jam_selesai',
    ];

    /**
     * Relasi ke Guru utama pengampu mapel.
     */
    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    /**
     * Relasi ke Guru pengganti (jika ada).
     */
    public function guruPengganti(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'guru_pengganti_id');
    }

    /**
     * Relasi ke Kelas.
     */
    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    /**
     * Relasi ke Semester.
     */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    /**
     * Relasi ke absensi siswa pada jadwal pelajaran ini.
     */
    public function absensiSiswa(): HasMany
    {
        return $this->hasMany(AbsensiSiswa::class, 'jadwal_id');
    }
}
