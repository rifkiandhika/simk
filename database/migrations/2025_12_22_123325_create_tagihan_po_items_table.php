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
        Schema::create('tagihan_po_items', function (Blueprint $table) {
            $table->uuid('id_tagihan_item')->primary();
            $table->uuid('id_tagihan');
            $table->uuid('id_po_item');
            $table->uuid('id_produk');
            $table->string('nama_produk', 200);

            // Qty tracking
            $table->integer('qty_diminta'); // Dari PO
            $table->integer('qty_diterima'); // Dari konfirmasi penerimaan
            $table->integer('qty_ditagihkan'); // Yang ditagihkan (= qty_diterima)

            // Harga
            $table->decimal('harga_satuan', 15, 2);
            $table->decimal('subtotal', 15, 2); // harga_satuan * qty_ditagihkan

            // Info batch
            $table->string('batch_number', 50)->nullable();
            $table->date('tanggal_kadaluarsa')->nullable();

            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('id_tagihan')->references('id_tagihan')->on('tagihan_po')->onDelete('cascade');
            $table->foreign('id_po_item')->references('id_po_item')->on('purchase_order_items')->onDelete('restrict');

            $table->index('id_tagihan');
            $table->index('id_po_item');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tagihan_po_items');
    }
};
