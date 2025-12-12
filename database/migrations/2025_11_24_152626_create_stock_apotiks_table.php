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
        Schema::create('stock_apotiks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('gudang_id')->constrained('gudangs')->onDelete('cascade');
            $table->string('kode_transaksi')->unique();
            $table->dateTime('tanggal_penerimaan');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_apotiks');
    }
};
