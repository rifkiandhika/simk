<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // ============================================
        // 1. UPDATE TABLE: purchase_orders
        //    Menambahkan GR dan Invoice fields
        // ============================================
        Schema::table('purchase_orders', function (Blueprint $table) {
            // === GOOD RECEIPT (GR) Section ===
            // Diisi saat konfirmasi penerimaan barang
            $table->string('no_gr')->nullable()->unique()->after('no_po')
                ->comment('Nomor Good Receipt, auto-generate saat barang diterima');

            // === INVOICE Section ===
            // Diisi setelah GR, saat faktur dari supplier masuk
            $table->string('no_invoice')->nullable()->after('tanggal_diterima')
                ->comment('Nomor Invoice/Faktur dari Supplier');
            $table->date('tanggal_invoice')->nullable()->after('no_invoice')
                ->comment('Tanggal Invoice dari Supplier');
            $table->date('tanggal_jatuh_tempo')->nullable()->after('tanggal_invoice')
                ->comment('Tanggal Jatuh Tempo Pembayaran');
            $table->string('nomor_faktur_pajak')->nullable()->after('tanggal_jatuh_tempo')
                ->comment('Nomor Faktur Pajak dari Supplier');

            // === Tracking ===
            $table->uuid('id_karyawan_input_invoice')->nullable()->after('nomor_faktur_pajak')
                ->comment('Karyawan yang input invoice');
            $table->timestamp('tanggal_input_invoice')->nullable()->after('id_karyawan_input_invoice')
                ->comment('Kapan invoice diinput');

            // Foreign key
            $table->foreign('id_karyawan_input_invoice')
                ->references('id_karyawan')
                ->on('karyawans')
                ->onDelete('set null');
        });

        Schema::create('purchase_order_item_batches', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_po_item')->comment('FK ke purchase_order_items');

            // Detail Batch
            $table->string('batch_number')->comment('Nomor Batch dari produk');
            $table->date('tanggal_kadaluarsa')->nullable()->comment('Tanggal Kadaluarsa batch ini');

            // Quantity & Kondisi
            $table->integer('qty_diterima')->default(0)->comment('Jumlah yang diterima untuk batch ini');
            $table->enum('kondisi', ['baik', 'rusak', 'kadaluarsa'])->default('baik')
                ->comment('Kondisi barang batch ini');

            // Additional Info
            $table->text('catatan')->nullable()->comment('Catatan untuk batch ini');

            $table->timestamps();

            // Foreign Key & Index
            $table->foreign('id_po_item')
                ->references('id_po_item')
                ->on('purchase_order_items')
                ->onDelete('cascade');

            $table->index(['id_po_item', 'batch_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // Drop table batches dulu (karena ada FK)
        Schema::dropIfExists('purchase_order_item_batches');

        // Drop columns dari purchase_orders
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropForeign(['id_karyawan_input_invoice']);
            $table->dropColumn([
                'no_gr',
                'no_invoice',
                'tanggal_invoice',
                'tanggal_jatuh_tempo',
                'nomor_faktur_pajak',
                'id_karyawan_input_invoice',
                'tanggal_input_invoice'
            ]);
        });
    }
};
