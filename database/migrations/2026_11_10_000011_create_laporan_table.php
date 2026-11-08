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
        Schema::create('laporan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dibuat_oleh')->constrained('users')->cascadeOnDelete()->comment('User yang men-generate laporan');
            $table->foreignId('semester_id')->constrained('semester')->cascadeOnDelete();
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->nullOnDelete()->comment('Nullable jika laporan seluruh sekolah/guru');
            $table->string('jenis_laporan', 50)->comment('Contoh: absensi_guru, absensi_siswa, rekap_bulanan');
            $table->date('periode_mulai');
            $table->date('periode_selesai');
            $table->string('file_pdf')->nullable()->comment('Path file PDF lokal');
            $table->string('drive_file_id')->nullable()->comment('Google Drive File ID');
            $table->text('drive_url')->nullable()->comment('Web view URL Google Drive');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan');
    }
};
