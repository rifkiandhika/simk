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
        Schema::create('rekam_medis', function (Blueprint $table) {
            $table->id('id_rekam_medis');
            $table->string('no_rekam_medis', 50)->unique();
            $table->foreignId('id_registrasi')->constrained('registrasis', 'id_registrasi')->onDelete('cascade');
            $table->foreignId('id_pasien')->constrained('pasiens', 'id_pasien')->onDelete('cascade');
            $table->foreignId('id_dokter')->constrained('dokters', 'id_dokter')->onDelete('cascade');
            $table->date('tanggal_periksa');
            $table->text('anamnesis')->nullable();
            $table->text('riwayat_penyakit')->nullable();
            $table->text('riwayat_alergi')->nullable();
            $table->enum('status', ['Draft', 'Final'])->default('Draft');
            $table->foreignUuid('created_by')->constrained('karyawans', 'id_karyawan');
            $table->foreignUuid('updated_by')->nullable()->constrained('karyawans', 'id_karyawan');
            $table->timestamps();

            $table->index('no_rekam_medis');
            $table->index(['id_pasien', 'tanggal_periksa']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rekam_medis');
    }
};
