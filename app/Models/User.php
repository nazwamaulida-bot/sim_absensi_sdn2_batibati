<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'guru_id', 'role', 'status'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Atribut yang dapat diisi secara massal (Mass Assignable).
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'guru_id',
        'role',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ==========================================
    // RELASI ELOQUENT
    // ==========================================

    /**
     * Relasi ke profil Guru (jika user merupakan guru/wali kelas).
     */
    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    /**
     * Relasi absensi siswa yang dicatat oleh user ini.
     */
    public function absensiSiswaDicatat(): HasMany
    {
        return $this->hasMany(AbsensiSiswa::class, 'dicatat_oleh');
    }

    /**
     * Relasi laporan yang dibuat/digenerate oleh user ini.
     */
    public function laporan(): HasMany
    {
        return $this->hasMany(Laporan::class, 'dibuat_oleh');
    }

    // ==========================================
    // HELPER ROLE & STATUS
    // ==========================================

    /**
     * Cek apakah user memiliki peran sebagai Admin TU.
     */
    public function isAdminTu(): bool
    {
        return $this->role === 'admin_tu';
    }

    /**
     * Cek apakah user memiliki peran sebagai Guru.
     */
    public function isGuru(): bool
    {
        return $this->role === 'guru';
    }

    /**
     * Cek apakah user memiliki peran sebagai Kepala Sekolah.
     */
    public function isKepalaSekolah(): bool
    {
        return $this->role === 'kepala_sekolah';
    }

    /**
     * Cek apakah user merupakan Wali Kelas berdasarkan data penugasan di tabel wali_kelas.
     * Bukan hanya mengecek role 'guru', melainkan memvalidasi apakah guru tersebut
     * benar-benar ditugaskan sebagai wali kelas.
     *
     * @param  int|null  $tahunAjaranId  (Opsional) Filter berdasarkan ID tahun ajaran tertentu
     */
    public function isWaliKelas(?int $tahunAjaranId = null): bool
    {
        // Wali kelas harus memiliki role 'guru' dan memiliki keterkaitan ke data master guru
        if (! $this->isGuru() || ! $this->guru_id || ! $this->guru) {
            return false;
        }

        $query = $this->guru->waliKelas();

        if ($tahunAjaranId !== null) {
            $query->where('tahun_ajaran_id', $tahunAjaranId);
        }

        return $query->exists();
    }

    /**
     * Cek apakah akun user dalam keadaan aktif.
     */
    public function isAktif(): bool
    {
        return $this->status === 'aktif';
    }
}
