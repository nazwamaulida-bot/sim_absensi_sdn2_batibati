<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Laporan extends Model
{
    use HasFactory;

    /**
     * Nama tabel di database.
     *
     * @var string
     */
    protected $table = 'laporan';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var list<string>
     */
    protected $fillable = [
        'dibuat_oleh',
        'semester_id',
        'kelas_id',
        'jenis_laporan',
        'periode_mulai',
        'periode_selesai',
        'file_pdf',
        'drive_file_id',
        'drive_url',
    ];

    /**
     * Konversi tipe data otomatis (casting).
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'periode_mulai' => 'date',
            'periode_selesai' => 'date',
        ];
    }

    /**
     * Relasi ke User yang membuat/menerbitkan laporan.
     */
    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    /**
     * Relasi ke Semester cakupan laporan.
     */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    /**
     * Relasi ke Kelas (opsional/nullable jika laporan per tingkat atau seluruh sekolah).
     */
    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }
}
