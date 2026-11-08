<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbsensiGuru extends Model
{
    use HasFactory;

    /**
     * Nama tabel di database.
     *
     * @var string
     */
    protected $table = 'absensi_guru';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var list<string>
     */
    protected $fillable = [
        'guru_id',
        'tanggal',
        'jam_masuk',
        'jam_pulang',
        'latitude',
        'longitude',
        'jarak_meter',
        'wajah_terverifikasi',
        'status_kehadiran',
        'keterangan',
    ];

    /**
     * Konversi tipe data otomatis (casting).
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'wajah_terverifikasi' => 'boolean',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'jarak_meter' => 'decimal:2',
        ];
    }

    /**
     * Relasi ke Guru yang melakukan absensi.
     */
    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }
}
