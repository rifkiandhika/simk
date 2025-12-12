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
        // Tambah enum value 'konfirmasi_penerimaan'
        DB::statement("ALTER TABLE `po_audit_trails` MODIFY COLUMN `aksi` ENUM(
            'buat_po',
            'edit_po',
            'submit_approval',
            'approve_kepala_gudang',
            'reject_kepala_gudang',
            'approve_kasir',
            'reject_kasir',
            'kirim_ke_supplier',
            'update_shipping',
            'terima_barang',
            'konfirmasi_penerimaan',
            'batalkan_po',
            'hapus_po'
        ) NOT NULL");

        // Ubah user_agent ke TEXT
        Schema::table('po_audit_trails', function (Blueprint $table) {
            $table->text('user_agent')->nullable()->change();
        });

        // Tambah index untuk aksi
        Schema::table('po_audit_trails', function (Blueprint $table) {
            $table->index('aksi');
        });
    }

    public function down(): void
    {
        // Hapus enum 'konfirmasi_penerimaan'
        DB::statement("ALTER TABLE `po_audit_trails` MODIFY COLUMN `aksi` ENUM(
            'buat_po',
            'edit_po',
            'submit_approval',
            'approve_kepala_gudang',
            'reject_kepala_gudang',
            'approve_kasir',
            'reject_kasir',
            'kirim_ke_supplier',
            'update_shipping',
            'terima_barang',
            'batalkan_po',
            'hapus_po'
        ) NOT NULL");

        Schema::table('po_audit_trails', function (Blueprint $table) {
            $table->string('user_agent')->nullable()->change();
            $table->dropIndex(['aksi']);
        });
    }
};
