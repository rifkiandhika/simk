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
        Schema::create('reseps', function (Blueprint $table) {
            $table->id('id_resep');
            $table->string('no_resep', 50)->unique();
            $table->foreignId('id_rekam_medis')->constrained('rekam_medis', 'id_rekam_medis')->onDelete('cascade');
            $table->foreignId('id_pasien')->constrained('pasiens', 'id_pasien')->onDelete('cascade');
            $table->foreignId('id_dokter')->constrained('dokters', 'id_dokter')->onDelete('cascade');
            $table->date('tanggal_resep');
            $table->enum('jenis_resep', ['Racikan', 'Non Racikan'])->default('Non Racikan');
            $table->text('catatan')->nullable();
            $table->enum('status', ['Pending', 'Proses', 'Selesai', 'Batal'])->default('Pending');
            $table->decimal('total_harga', 15, 2)->default(0);
            $table->foreignUuid('apoteker')->nullable()->constrained('karyawans', 'id_karyawan');
            $table->dateTime('waktu_selesai')->nullable();
            $table->foreignUuid('created_by')->constrained('karyawans', 'id_karyawan');
            $table->timestamps();

            $table->index('no_resep');
            $table->index(['id_pasien', 'tanggal_resep']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reseps');
    }
};
