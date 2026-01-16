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
        Schema::create('dokter_ruangans', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->unsignedBigInteger('dokter_id');
            $table->uuid('ruangan_id');

            $table->string('hari')->nullable();
            $table->time('jam_mulai')->nullable();
            $table->time('jam_selesai')->nullable();

            $table->timestamps();

            $table->unique(['dokter_id', 'ruangan_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokter_ruangans');
    }
};
