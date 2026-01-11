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
        Schema::create('tagihan_pembayarans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_tagihan')
                ->constrained('tagihans', 'id_tagihan')
                ->cascadeOnDelete();

            $table->date('tanggal_bayar');

            $table->decimal('jumlah_bayar', 15, 2);

            $table->enum('metode', [
                'TUNAI',
                'DEBIT',
                'CREDIT',
                'TRANSFER',
                'BPJS',
                'ASURANSI'
            ]);

            $table->string('no_referensi')->nullable();

            $table->text('keterangan')->nullable();

            $table->foreignUuid('created_by')
                ->constrained('karyawans', 'id_karyawan');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tagihan_pembayarans');
    }
};
