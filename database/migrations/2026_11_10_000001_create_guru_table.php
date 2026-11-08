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
        Schema::create('guru', function (Blueprint $table) {
            $table->id();
            $table->string('nip', 30)->nullable()->unique();
            $table->string('nama');
            $table->string('email')->unique();
            $table->string('no_hp', 20)->nullable();
            $table->longText('data_wajah')->nullable()->comment('Embedding / data referensi wajah untuk face recognition');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guru');
    }
};
