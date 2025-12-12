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
        Schema::create('cppts', function (Blueprint $table) {
            $table->id('id_cppt');
            $table->foreignId('id_rekam_medis')->constrained('rekam_medis', 'id_rekam_medis')->onDelete('cascade');
            $table->dateTime('tanggal_waktu');
            $table->enum('profesi', ['Dokter', 'Perawat']);
            $table->foreignUuid('id_petugas')->constrained('karyawans', 'id_karyawan');
            $table->text('subjective')->nullable();
            $table->text('objective')->nullable();
            $table->text('assessment')->nullable();
            $table->text('plan')->nullable();
            $table->text('instruksi')->nullable();
            $table->enum('verifikasi', ['Belum', 'Sudah'])->default('Belum');
            $table->foreignId('verified_by')->nullable()->constrained('dokters', 'id_dokter');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index('id_rekam_medis');
            $table->index('tanggal_waktu');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cppts');
    }
};
