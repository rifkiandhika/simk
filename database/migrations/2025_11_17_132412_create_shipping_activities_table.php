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
        Schema::create('shipping_activities', function (Blueprint $table) {
            $table->uuid('id_shipping')->primary();
            $table->uuid('id_po');
            $table->enum('status_shipping', [
                'persiapan',
                'dikemas',
                'dalam_perjalanan',
                'tiba_di_tujuan',
                'diterima',
                'selesai'
            ]);
            $table->text('deskripsi_aktivitas');
            $table->uuid('id_karyawan_input')->nullable(); // siapa yang input aktivitas ini
            $table->timestamp('tanggal_aktivitas');
            $table->text('catatan')->nullable();
            $table->string('foto_bukti')->nullable(); // untuk dokumentasi
            $table->timestamps();

            $table->foreign('id_po')->references('id_po')->on('purchase_orders')->onDelete('cascade');
            $table->foreign('id_karyawan_input')->references('id_karyawan')->on('karyawans');

            $table->index('id_po');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_activities');
    }
};
