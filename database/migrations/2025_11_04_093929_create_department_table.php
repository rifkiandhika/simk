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
        Schema::create('department', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('kode_department', 20)->unique();
            $table->string('nama_department', 100)->comment('Farmasi, Lab, Radiologi, dll');
            $table->string('lokasi')->nullable();
            $table->string('kepala_department')->nullable();
            $table->enum('jenis', ['Medis', 'Non-Medis', 'Penunjang'])->default('Medis');
            $table->enum('status', ['Aktif', 'Nonaktif'])->default('Aktif');
            $table->timestamps();

            $table->index('kode_department');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('department');
    }
};
