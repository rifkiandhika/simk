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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->uuid('id_po')->primary();
            $table->string('no_po', 50)->unique(); // Format: PO-YYYYMMDD-XXX
            $table->enum('tipe_po', ['internal', 'eksternal']); // internal: apotik->gudang, eksternal: gudang->supplier
            $table->enum('status', [
                'draft',
                'menunggu_persetujuan_kepala_gudang',
                'menunggu_persetujuan_kasir',
                'disetujui',
                'dikirim_ke_supplier',
                'dalam_pengiriman',
                'diterima',
                'ditolak',
                'dibatalkan'
            ])->default('draft');

            // Informasi Pemohon
            $table->uuid('id_unit_pemohon'); // apotik atau gudang
            $table->enum('unit_pemohon', ['apotik', 'gudang']);
            $table->uuid('id_karyawan_pemohon');
            $table->date('tanggal_permintaan');
            $table->text('catatan_pemohon')->nullable();

            // Informasi Tujuan
            $table->uuid('id_unit_tujuan')->nullable(); // gudang atau supplier
            $table->enum('unit_tujuan', ['gudang', 'supplier'])->nullable();

            // Persetujuan (untuk PO eksternal ke supplier)
            $table->uuid('id_kepala_gudang_approval')->nullable();
            $table->timestamp('tanggal_approval_kepala_gudang')->nullable();
            $table->text('catatan_kepala_gudang')->nullable();
            $table->enum('status_approval_kepala_gudang', ['pending', 'disetujui', 'ditolak'])->default('pending');

            $table->uuid('id_kasir_approval')->nullable();
            $table->timestamp('tanggal_approval_kasir')->nullable();
            $table->text('catatan_kasir')->nullable();
            $table->enum('status_approval_kasir', ['pending', 'disetujui', 'ditolak'])->default('pending');

            // Informasi Supplier (untuk PO eksternal)
            $table->uuid('id_supplier')->nullable();
            $table->timestamp('tanggal_dikirim_ke_supplier')->nullable();
            $table->uuid('id_karyawan_pengirim')->nullable();

            // Total
            $table->decimal('total_harga', 15, 2)->default(0);
            $table->decimal('pajak', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_karyawan_pemohon')->references('id_karyawan')->on('karyawans');
            $table->foreign('id_kepala_gudang_approval')->references('id_karyawan')->on('karyawans');
            $table->foreign('id_kasir_approval')->references('id_karyawan')->on('karyawans');
            $table->foreign('id_karyawan_pengirim')->references('id_karyawan')->on('karyawans');

            $table->index('no_po');
            $table->index('status');
            $table->index('tipe_po');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
