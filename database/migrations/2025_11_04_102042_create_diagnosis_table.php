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
        Schema::create('diagnosis', function (Blueprint $table) {
            $table->id('id_diagnosis');
            $table->foreignId('id_rekam_medis')->constrained('rekam_medis', 'id_rekam_medis')->onDelete('cascade');
            $table->string('skri', 10);
            $table->foreign('skri')->references('skri')->on('icd10')->onDelete('cascade');
            $table->enum('jenis', ['Utama', 'Sekunder'])->default('Utama');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index('id_rekam_medis');
            $table->index('skri');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diagnosis');
    }
};
