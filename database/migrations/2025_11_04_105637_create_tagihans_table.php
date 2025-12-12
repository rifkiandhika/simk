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
        Schema::create('tagihans', function (Blueprint $table) {
            $table->id('id_tagihan');
            $table->string('no_tagihan', 50)->unique();
            $table->foreignId('id_registrasi')->constrained('registrasis', 'id_registrasi')->onDelete('cascade');
            $table->foreignId('id_pasien')->constrained('pasiens', 'id_pasien')->onDelete('cascade');
            $table->date('tanggal_tagihan');
            $table->decimal('total_tagihan', 15, 2)->default(0);
            $table->decimal('total_dibayar', 15, 2)->default(0);
            $table->decimal('sisa_tagihan', 15, 2)->default(0);
            $table->enum('status', ['Belum Lunas', 'Lunas', 'Cicilan'])->default('Belum Lunas');
            $table->enum('jenis_pembayaran', ['Tunai', 'BPJS', 'Asuransi', 'Debit', 'Credit'])->default('Tunai');
            $table->foreignUuid('created_by')->constrained('karyawans', 'id_karyawan');
            $table->timestamps();

            $table->index('no_tagihan');
            $table->index(['id_pasien', 'tanggal_tagihan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tagihans');
    }
};
