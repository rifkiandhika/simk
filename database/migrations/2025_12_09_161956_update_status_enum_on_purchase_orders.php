<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
        ALTER TABLE purchase_orders
        MODIFY COLUMN status ENUM(
            'draft',
            'menunggu_persetujuan_kepala_gudang',
            'menunggu_persetujuan_kasir',
            'disetujui',
            'dikirim_ke_supplier',
            'dalam_pengiriman',
            'diterima',
            'ditolak',
            'dibatalkan',
            'selesai'
        ) NOT NULL DEFAULT 'draft';
    ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
