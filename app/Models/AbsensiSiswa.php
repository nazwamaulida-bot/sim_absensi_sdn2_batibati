<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbsensiSiswa extends Model
{
    use HasFactory;

    /**
     * Nama tabel di database.
     *
     * @var string
     */
    protected $table = 'absensi_siswa';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var list<string>
     */
    protected $fillable = [
        'siswa_id',
        'jadwal_id',
        'dicatat_oleh',
        'tanggal',
        'status_kehadiran',
        'keterangan',
        'dokumen_pendukung',
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
        ];
    }

    /**
     * Relasi ke Siswa yang diabsen.
     */
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    /**
     * Relasi ke Jadwal Pelajaran saat absensi dilakukan.
     */
    public function jadwalPelajaran(): BelongsTo
    {
        return $this->belongsTo(JadwalPelajaran::class, 'jadwal_id');
    }

    /**
     * Relasi ke User (guru/admin) yang mencatat kehadiran.
     */
    public function pencatat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }
}
