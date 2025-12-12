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
        Schema::create('history_stockap', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('detail_apotik_id')->constrained('detail_stock_apotiks')->onDelete('cascade');
            $table->unsignedBigInteger('jumlah');
            $table->dateTime('waktu_proses');
            $table->enum('status', ['penerimaan', 'pengeluaran', 'retur']);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history_stockap');
    }
};
