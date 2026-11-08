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
        Schema::create('absensi_guru', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('guru')->cascadeOnDelete();
            $table->date('tanggal');
            $table->time('jam_masuk')->nullable();
            $table->time('jam_pulang')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->decimal('jarak_meter', 8, 2)->nullable()->comment('Jarak guru ke titik radius sekolah (meter)');
            $table->boolean('wajah_terverifikasi')->default(false)->comment('Hasil verifikasi face recognition');
            $table->enum('status_kehadiran', ['hadir', 'izin', 'sakit', 'alpha', 'terlambat'])->default('hadir');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            // Memastikan 1 guru hanya punya 1 record absensi per tanggal
            $table->unique(['guru_id', 'tanggal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi_guru');
    }
};
