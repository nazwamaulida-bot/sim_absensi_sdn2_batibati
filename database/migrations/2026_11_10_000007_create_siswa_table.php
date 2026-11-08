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
        Schema::create('siswa', function (Blueprint $table) {
            $table->id();
            $table->string('nis', 30)->unique();
            $table->string('nama');
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->enum('jenis_kelamin', ['L', 'P'])->comment('L: Laki-laki, P: Perempuan');
            $table->string('status', 20)->default('aktif')->comment('Status: aktif, lulus, pindah, dsb.');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswa');
    }
};
