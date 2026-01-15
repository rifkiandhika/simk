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
            $table->string('no_kwitansi')->nullable()->after('nomor_faktur_pajak')
                ->comment('Nomor Kwitansi');
            $table->string('bukti_invoice')->nullable()->after('tanggal_invoice');
            $table->timestamp('tanggal_upload_bukti_invoice')->nullable()->after('bukti_invoice');
            $table->uuid('id_karyawan_upload_bukti')->nullable()->after('tanggal_upload_bukti_invoice');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('po', function (Blueprint $table) {
            //
        });
    }
};
