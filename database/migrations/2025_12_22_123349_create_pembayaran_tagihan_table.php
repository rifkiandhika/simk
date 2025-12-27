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
        Schema::create('pembayaran_tagihan', function (Blueprint $table) {
            $table->uuid('id_pembayaran')->primary();
            $table->string('no_pembayaran', 50)->unique(); // Format: PAY-YYYYMMDD-XXX
            $table->uuid('id_tagihan');

            // Detail pembayaran
            $table->decimal('jumlah_bayar', 15, 2);
            $table->date('tanggal_bayar');
            $table->enum('metode_pembayaran', [
                'transfer',
                'cash',
                'giro',
                'kartu_kredit',
                'lainnya'
            ]);
            $table->string('nomor_referensi', 100)->nullable(); // Nomor transfer/giro/dll

            // Upload bukti
            $table->string('bukti_pembayaran')->nullable(); // Path file
            $table->text('catatan')->nullable();

            // Tracking
            $table->uuid('id_karyawan_input'); // Finance yang input
            $table->uuid('id_karyawan_approve')->nullable(); // Manager yang approve
            $table->timestamp('tanggal_approve')->nullable();
            $table->enum('status_pembayaran', [
                'pending',
                'diverifikasi',
                'ditolak'
            ])->default('pending');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_tagihan')->references('id_tagihan')->on('tagihan_po')->onDelete('cascade');
            $table->foreign('id_karyawan_input')->references('id_karyawan')->on('karyawans');
            $table->foreign('id_karyawan_approve')->references('id_karyawan')->on('karyawans');

            $table->index('id_tagihan');
            $table->index('no_pembayaran');
            $table->index('tanggal_bayar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran_tagihan');
    }
};
