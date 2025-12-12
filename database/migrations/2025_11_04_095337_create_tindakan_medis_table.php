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
        Schema::create('tindakan_medis', function (Blueprint $table) {
            $table->id('id_tindakan');
            $table->string('kode_tindakan', 50)->unique();
            $table->string('nama_tindakan', 200);
            $table->text('deskripsi')->nullable();
            $table->decimal('tarif', 15, 2)->default(0);
            $table->string('kategori', 100)->nullable();
            $table->enum('status', ['Aktif', 'Nonaktif'])->default('Aktif');
            $table->timestamps();

            $table->index('kode_tindakan');
            $table->index('kategori');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tindakan_medis');
    }
};
