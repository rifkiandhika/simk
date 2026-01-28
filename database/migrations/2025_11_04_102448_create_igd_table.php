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
        Schema::create('igd', function (Blueprint $table) {
            $table->id('id_igd');
            $table->string('no_igd', 50)->unique();
            $table->string('no_rm', 50);
            $table->foreignId('id_pasien')->constrained('pasiens', 'id_pasien')->onDelete('cascade');
            $table->dateTime('waktu_datang');
            $table->enum('cara_datang', ['Jalan Kaki', 'Ambulans', 'Rujukan'])->default('Jalan Kaki');
            $table->text('keluhan_utama')->nullable();
            $table->enum('tingkat_kesadaran', ['CM', 'Apatis', 'Somnolen', 'Sopor', 'Koma'])->default('CM');
            $table->enum('status_triase', ['Merah', 'Kuning', 'Hijau', 'Hitam'])->nullable();
            $table->text('tindakan_awal')->nullable();
            $table->enum('status', ['Dalam Perawatan', 'Pulang', 'Rawat Inap', 'Rujuk', 'Meninggal'])->default('Dalam Perawatan');
            $table->dateTime('waktu_keluar')->nullable();
            $table->foreignId('dokter_jaga')->constrained('dokters', 'id_dokter');
            $table->foreignId('perawat_jaga')->constrained('perawats', 'id_perawat');
            $table->foreignUuid('created_by')->constrained('karyawans', 'id_karyawan');
            $table->timestamps();

            $table->index('no_igd');
            $table->index('waktu_datang');
            $table->index(['id_pasien', 'waktu_datang']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('igd');
    }
};
