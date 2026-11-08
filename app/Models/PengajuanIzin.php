<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengajuanIzin extends Model
{
    use HasFactory;

    /**
     * Nama tabel di database.
     *
     * @var string
     */
    protected $table = 'pengajuan_izin';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var list<string>
     */
    protected $fillable = [
        'guru_id',
        'jenis',
        'tanggal_mulai',
        'tanggal_selesai',
        'keterangan',
        'dokumen_pendukung',
        'status',
        'diverifikasi_oleh',
        'catatan_kepsek',
    ];

    /**
     * Konversi tipe data otomatis.
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
     * Relasi ke Guru yang mengajukan izin.
     */
    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    /**
     * Relasi ke User (Kepala Sekolah) yang memverifikasi.
     */
    public function verifikator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }
}
