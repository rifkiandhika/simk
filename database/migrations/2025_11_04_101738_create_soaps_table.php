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
        Schema::create('soaps', function (Blueprint $table) {
            $table->id('id_soap');
            $table->foreignId('id_rekam_medis')->constrained('rekam_medis', 'id_rekam_medis')->onDelete('cascade');
            $table->text('subjective')->nullable()->comment('Keluhan Pasien');
            $table->text('objective')->nullable()->comment('Pemeriksaan Fisik');
            $table->text('assessment')->nullable()->comment('Diagnosis/Assessment');
            $table->text('plan')->nullable()->comment('Rencana Tindakan');
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
        Schema::dropIfExists('soaps');
    }
};
