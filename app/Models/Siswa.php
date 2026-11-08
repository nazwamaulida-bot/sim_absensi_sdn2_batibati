<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Siswa extends Model
{
    use HasFactory;

    /**
     * Nama tabel di database.
     *
     * @var string
     */
    protected $table = 'siswa';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nis',
        'nama',
        'kelas_id',
        'jenis_kelamin',
        'status',
    ];

    /**
     * Relasi ke Kelas tempat siswa terdaftar.
     */
    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    /**
     * Relasi ke seluruh catatan Absensi Siswa.
     */
    public function absensiSiswa(): HasMany
    {
        return $this->hasMany(AbsensiSiswa::class, 'siswa_id');
    }
}
