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
        Schema::create('registrasis', function (Blueprint $table) {
            $table->id('id_registrasi');
            $table->string('no_registrasi', 50)->unique();
            $table->foreignId('id_pasien')->constrained('pasiens', 'id_pasien')->onDelete('cascade');
            $table->foreignUuid('id_poli')->constrained('poli', 'id_poli')->onDelete('cascade');
            $table->foreignId('id_dokter')->constrained('dokters', 'id_dokter')->onDelete('cascade');
            $table->date('tanggal_kunjungan');
            $table->time('jam_kunjungan');
            $table->enum('jenis_kunjungan', ['Baru', 'Kontrol'])->default('Baru');
            $table->enum('jenis_pelayanan', ['Rawat Jalan', 'IGD', 'Rawat Inap'])->default('Rawat Jalan');
            $table->text('keluhan_utama')->nullable();
            $table->enum('status_registrasi', ['Menunggu', 'Dilayani', 'Selesai', 'Batal'])->default('Menunggu');
            $table->foreignUuid('created_by')->constrained('karyawans', 'id_karyawan');
            $table->timestamps();

            $table->index('no_registrasi');
            $table->index('tanggal_kunjungan');
            $table->index(['id_pasien', 'tanggal_kunjungan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registrasis');
    }
};
