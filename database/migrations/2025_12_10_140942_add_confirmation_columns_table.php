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
        // Add columns to purchase_orders table
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->uuid('id_penerima')->nullable()->after('id_kasir_approval');
            $table->dateTime('tanggal_diterima')->nullable()->after('tanggal_approval_kasir');
            $table->text('catatan_penerima')->nullable()->after('catatan_kasir');

            // Foreign key
            $table->foreign('id_penerima')
                ->references('id_karyawan')
                ->on('karyawans')
                ->onDelete('set null');
        });

        // Add columns to purchase_order_items table
        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->enum('kondisi_barang', ['baik', 'rusak', 'kadaluarsa'])
                ->default('baik')
                ->after('qty_diterima');
            $table->text('catatan_penerimaan')->nullable()->after('kondisi_barang');
        });
    }

    public function down()
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropForeign(['id_penerima']);
            $table->dropColumn(['id_penerima', 'tanggal_diterima', 'catatan_penerima']);
        });

        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->dropColumn(['kondisi_barang', 'catatan_penerimaan']);
        });
    }
};
