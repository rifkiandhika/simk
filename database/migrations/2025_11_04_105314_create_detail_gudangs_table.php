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
        Schema::create('detail_gudangs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('gudang_id')->references('id')->on('gudangs')->onDelete('cascade');
            $table->string('barang_type', 50)->comment('obat/alkes/reagensia - Polymorphic');
            $table->uuid('barang_id')->comment('Polymorphic ID');
            $table->string('no_batch', 50)->nullable();
            $table->bigInteger('stock_gudang')->default(0);
            $table->bigInteger('min_persediaan')->default(0);
            $table->date('tanggal_masuk')->nullable();
            $table->date('tanggal_kadaluarsa')->nullable();
            $table->string('lokasi_rak', 50)->nullable();
            $table->enum('kondisi', ['Baik', 'Rusak', 'Kadaluarsa'])->default('Baik');
            $table->timestamps();

            $table->index('gudang_id');
            $table->index(['barang_type', 'barang_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_gudangs');
    }
};
