<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Semester extends Model
{
    use HasFactory;

    /**
     * Nama tabel di database.
     *
     * @var string
     */
    protected $table = 'semester';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var list<string>
     */
    protected $fillable = [
        'tahun_ajaran_id',
        'nama_semester',
        'tanggal_mulai',
        'tanggal_selesai',
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
        ];
    }

    /**
     * Relasi ke Tahun Ajaran induk.
     */
    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }

    /**
     * Relasi ke seluruh jadwal pelajaran pada semester ini.
     */
    public function jadwalPelajaran(): HasMany
    {
        return $this->hasMany(JadwalPelajaran::class, 'semester_id');
    }

    /**
     * Relasi ke laporan yang dibuat pada semester ini.
     */
    public function laporan(): HasMany
    {
        return $this->hasMany(Laporan::class, 'semester_id');
    }
}
