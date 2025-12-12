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
        Schema::create('dokters', function (Blueprint $table) {
            $table->id('id_dokter')->primary();
            $table->foreignUuid('id_karyawan')->constrained('karyawans', 'id_karyawan')->onDelete('cascade');
            $table->string('no_sip', 50)->nullable()->comment('Surat Izin Praktik');
            $table->string('spesialisasi', 100)->nullable();
            $table->foreignUuid('id_poli')->nullable()->constrained('poli', 'id_poli')->onDelete('set null');
            $table->enum('status', ['Aktif', 'Nonaktif', 'Cuti'])->default('Aktif');
            $table->timestamps();

            $table->index('id_karyawan');
            $table->index('id_poli');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokters');
    }
};
