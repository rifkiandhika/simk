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
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->string('bukti_barang')->nullable()->after('bukti_invoice');
            $table->timestamp('tanggal_upload_bukti_barang')->nullable()->after('tanggal_upload_bukti');
            $table->uuid('id_karyawan_upload_bukti_barang')->nullable()->after('id_karyawan_upload_bukti');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            //
        });
    }
};
