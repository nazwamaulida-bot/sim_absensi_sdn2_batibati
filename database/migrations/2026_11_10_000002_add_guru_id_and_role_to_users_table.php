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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('guru_id')
                ->nullable()
                ->after('id')
                ->constrained('guru')
                ->nullOnDelete();
            $table->enum('role', ['admin_tu', 'guru', 'kepala_sekolah'])
                ->default('guru')
                ->after('password');
            $table->string('status', 20)
                ->default('aktif')
                ->after('role')
                ->comment('Status akun: misal aktif, nonaktif');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['guru_id']);
            $table->dropColumn(['guru_id', 'role', 'status']);
        });
    }
};
