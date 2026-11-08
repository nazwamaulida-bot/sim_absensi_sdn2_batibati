<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TahunAjaran extends Model
{
    use HasFactory;

    /**
     * Nama tabel di database.
     *
     * @var string
     */
    protected $table = 'tahun_ajaran';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nama_tahun_ajaran',
        'tanggal_mulai',
        'tanggal_selesai',
        'status_aktif',
    ];

    /**
     * Konversi tipe data otomatis (casting).
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'status_aktif' => 'boolean',
        ];
    }

    /**
     * Relasi ke semester di tahun ajaran ini.
     */
    public function semester(): HasMany
    {
        return $this->hasMany(Semester::class, 'tahun_ajaran_id');
    }

    /**
     * Relasi ke penetapan wali kelas pada tahun ajaran ini.
     */
    public function waliKelas(): HasMany
    {
        return $this->hasMany(WaliKelas::class, 'tahun_ajaran_id');
    }
}
