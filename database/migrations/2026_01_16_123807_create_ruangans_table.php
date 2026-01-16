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
        Schema::create('ruangans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('kode_ruangan', 50)->unique();
            $table->string('nama_ruangan');
            $table->enum('jenis', [
                'rawat_jalan',
                'rawat_inap',
                'igd',
                'penunjang'
            ]);
            // $table->foreignId('poli_id')
            //       ->nullable()
            //       ->constrained('polis')
            //       ->nullOnDelete();
            $table->integer('kapasitas')->default(0);
            $table->string('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ruangans');
    }
};
