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
        Schema::create('vital_signs', function (Blueprint $table) {
            $table->id('id_vital_sign');
            $table->foreignId('id_rekam_medis')->constrained('rekam_medis', 'id_rekam_medis')->onDelete('cascade');
            $table->decimal('tekanan_darah_sistol', 5, 2)->nullable();
            $table->decimal('tekanan_darah_diastol', 5, 2)->nullable();
            $table->decimal('nadi', 5, 2)->nullable()->comment('per menit');
            $table->decimal('suhu', 4, 2)->nullable()->comment('celcius');
            $table->decimal('pernapasan', 5, 2)->nullable()->comment('per menit');
            $table->decimal('tinggi_badan', 5, 2)->nullable()->comment('cm');
            $table->decimal('berat_badan', 5, 2)->nullable()->comment('kg');
            $table->decimal('spo2', 5, 2)->nullable()->comment('saturasi oksigen');
            $table->text('keterangan')->nullable();
            $table->foreignUuid('created_by')->constrained('karyawans', 'id_karyawan');
            $table->timestamps();

            $table->index('id_rekam_medis');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vital_signs');
    }
};
