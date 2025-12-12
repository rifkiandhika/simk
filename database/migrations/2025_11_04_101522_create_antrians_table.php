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
        Schema::create('antrians', function (Blueprint $table) {
            $table->id('id_antrian');
            $table->foreignId('id_registrasi')->constrained('registrasis', 'id_registrasi')->onDelete('cascade');
            $table->foreignUuid('id_poli')->constrained('poli', 'id_poli')->onDelete('cascade');
            $table->string('no_antrian', 20);
            $table->date('tanggal');
            $table->enum('status', ['Menunggu', 'Dipanggil', 'Selesai', 'Batal'])->default('Menunggu');
            $table->time('waktu_panggil')->nullable();
            $table->timestamps();

            $table->index(['id_poli', 'tanggal']);
            $table->index('no_antrian');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('antrians');
    }
};
