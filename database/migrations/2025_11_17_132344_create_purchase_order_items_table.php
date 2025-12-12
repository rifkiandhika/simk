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
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->uuid('id_po_item')->primary();
            $table->uuid('id_po');
            $table->uuid('id_produk'); // referensi ke tabel produk/obat
            $table->string('nama_produk', 200);
            $table->integer('qty_diminta');
            $table->integer('qty_disetujui')->nullable();
            $table->integer('qty_diterima')->default(0);
            $table->decimal('harga_satuan', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->date('tanggal_kadaluarsa')->nullable(); // untuk tracking expiry
            $table->string('batch_number', 50)->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('id_po')->references('id_po')->on('purchase_orders')->onDelete('cascade');

            $table->index('id_po');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
    }
};
