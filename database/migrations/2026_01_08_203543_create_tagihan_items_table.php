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
        Schema::create('tagihan_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_tagihan')
                ->constrained('tagihans', 'id_tagihan')
                ->cascadeOnDelete();

            $table->enum('kategori', [
                'APOTIK',
                'TINDAKAN',
                'LAB',
                'RADIOLOGI',
                'KAMAR',
                'ADMIN'
            ]);

            $table->string('referensi_tipe');
            // contoh: resep, tindakan, lab

            $table->uuid('referensi_id')->nullable();
            // id_resep / id_tindakan / id_lab

            $table->string('deskripsi');

            $table->integer('qty')->default(1);
            $table->decimal('harga', 15, 2);
            $table->decimal('subtotal', 15, 2);

            $table->boolean('ditanggung')->default(false); // BPJS/Asuransi

            $table->timestamps();

            $table->foreignUuid('created_by')
                ->constrained('karyawans', 'id_karyawan');

            $table->index(['id_tagihan', 'kategori']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tagihan_items');
    }
};
