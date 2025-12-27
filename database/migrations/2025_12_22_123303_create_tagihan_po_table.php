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
        Schema::create('tagihan_po', function (Blueprint $table) {
            $table->uuid('id_tagihan')->primary();
            $table->string('no_tagihan', 50)->unique(); // Format: TAG-YYYYMMDD-XXX
            $table->uuid('id_po');
            $table->uuid('id_supplier');

            // Status tagihan
            $table->enum('status', [
                'draft',
                'menunggu_pembayaran',
                'dibayar_sebagian',
                'lunas',
                'dibatalkan'
            ])->default('draft');

            // Informasi keuangan
            $table->decimal('total_tagihan', 15, 2)->default(0); // Total sebelum pajak
            $table->decimal('pajak', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0); // Total setelah pajak
            $table->decimal('total_dibayar', 15, 2)->default(0); // Akumulasi pembayaran
            $table->decimal('sisa_tagihan', 15, 2)->default(0); // Sisa yang belum dibayar

            // Informasi pembayaran
            $table->date('tanggal_tagihan')->nullable(); // Saat barang diterima
            $table->date('tanggal_jatuh_tempo')->nullable(); // Batas waktu bayar
            $table->integer('tenor_hari')->default(30); // Default 30 hari

            // Tracking
            $table->uuid('id_karyawan_buat')->nullable(); // Yang buat tagihan (sistem/karyawan)
            $table->timestamp('tanggal_dibuat')->nullable();
            $table->uuid('id_karyawan_approve')->nullable(); // Finance yang approve
            $table->timestamp('tanggal_approve')->nullable();
            $table->text('catatan')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_po')->references('id_po')->on('purchase_orders')->onDelete('cascade');
            $table->foreign('id_supplier')->references('id')->on('suppliers')->onDelete('restrict');
            $table->foreign('id_karyawan_buat')->references('id_karyawan')->on('karyawans');
            $table->foreign('id_karyawan_approve')->references('id_karyawan')->on('karyawans');

            $table->index('no_tagihan');
            $table->index('status');
            $table->index('id_po');
            $table->index('id_supplier');
            $table->index('tanggal_jatuh_tempo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tagihan_po');
    }
};
