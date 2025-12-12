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
        Schema::create('po_audit_trails', function (Blueprint $table) {
            $table->uuid('id_audit')->primary();
            $table->uuid('id_po');
            $table->uuid('id_karyawan');
            $table->string('pin_karyawan', 6); // untuk verifikasi
            $table->enum('aksi', [
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
            ]);
            $table->text('deskripsi_aksi');
            $table->json('data_sebelum')->nullable(); // untuk tracking perubahan
            $table->json('data_sesudah')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('tanggal_aksi');
            $table->timestamps();

            $table->foreign('id_po')->references('id_po')->on('purchase_orders')->onDelete('cascade');
            $table->foreign('id_karyawan')->references('id_karyawan')->on('karyawans');

            $table->index('id_po');
            $table->index('id_karyawan');
            $table->index('tanggal_aksi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('po_audit_trails');
    }
};
