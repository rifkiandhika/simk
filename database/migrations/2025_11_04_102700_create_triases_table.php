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
        Schema::create('triases', function (Blueprint $table) {
            $table->id('id_triase');
            $table->foreignId('id_igd')->constrained('igd', 'id_igd')->onDelete('cascade');
            $table->enum('prioritas', ['P1 Merah', 'P2 Kuning', 'P3 Hijau', 'P4 Hitam']);
            $table->text('keluhan')->nullable();
            $table->text('vital_sign_ringkas')->nullable();
            $table->foreignUuid('petugas')->constrained('karyawans', 'id_karyawan');
            $table->timestamps();

            $table->index('id_igd');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('triases');
    }
};
