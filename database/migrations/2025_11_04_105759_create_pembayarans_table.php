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
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id('id_pembayaran');
            $table->string('no_kwitansi', 50)->unique();
            $table->foreignId('id_tagihan')->constrained('tagihans', 'id_tagihan')->onDelete('cascade');
            $table->date('tanggal_pembayaran');
            $table->decimal('jumlah_bayar', 15, 2)->default(0);
            $table->enum('metode_pembayaran', ['Tunai', 'Transfer', 'Debit', 'Credit', 'BPJS'])->default('Tunai');
            $table->string('no_referensi', 100)->nullable();
            $table->text('keterangan')->nullable();
            $table->foreignUuid('kasir')->constrained('karyawans', 'id_karyawan');
            $table->timestamps();

            $table->index('no_kwitansi');
            $table->index('tanggal_pembayaran');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayarans');
    }
};
