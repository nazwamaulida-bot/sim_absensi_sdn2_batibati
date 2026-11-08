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
        Schema::create('pengajuan_izin', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('guru')->cascadeOnDelete();
            $table->enum('jenis', ['izin', 'sakit'])->comment('Jenis ketidakhadiran');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->text('keterangan');
            $table->string('dokumen_pendukung')->nullable()->comment('Path file bukti/surat');
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu');
            $table->foreignId('diverifikasi_oleh')->nullable()->constrained('users')->nullOnDelete()->comment('Kepala Sekolah yang memverifikasi');
            $table->text('catatan_kepsek')->nullable()->comment('Alasan persetujuan/penolakan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_izin');
    }
};
