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
        Schema::create('jenis_pemeriksaan_radiologis', function (Blueprint $table) {
            $table->id('id_jenis_pemeriksaan');
            $table->string('kode_pemeriksaan', 50)->unique();
            $table->string('nama_pemeriksaan', 200);
            $table->text('deskripsi')->nullable();
            $table->decimal('tarif', 15, 2)->default(0);
            $table->enum('status', ['Aktif', 'Nonaktif'])->default('Aktif');
            $table->timestamps();

            $table->index('kode_pemeriksaan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jenis_pemeriksaan_radiologis');
    }
};
