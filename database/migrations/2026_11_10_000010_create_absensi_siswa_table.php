<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('absensi_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();
            $table->foreignId('jadwal_id')->constrained('jadwal_pelajaran')->cascadeOnDelete();
            $table->foreignId('dicatat_oleh')->constrained('users')->cascadeOnDelete()->comment('User (guru/admin) yang mencatat absensi');
            $table->date('tanggal');
            $table->enum('status_kehadiran', ['hadir', 'izin', 'sakit', 'alpha'])->default('hadir');
            $table->text('keterangan')->nullable();
            $table->string('dokumen_pendukung')->nullable()->comment('Path file bukti surat dokter/izin');
            $table->timestamps();

            // Memastikan 1 siswa hanya dicatat 1 kali pada jadwal & tanggal yang sama
            $table->unique(['siswa_id', 'jadwal_id', 'tanggal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi_siswa');
    }
};
