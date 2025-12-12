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
        Schema::create('detail_stock_apotiks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('stock_apotik_id')->constrained('stock_apotiks')->onDelete('cascade');
            $table->uuid('obat_id');
            $table->string('no_batch');
            $table->bigInteger('stock_apotik');
            $table->bigInteger('min_persediaan')->default(0);
            $table->bigInteger('retur')->default(0);
            $table->date('tanggal_kadaluarsa')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_stock_apotiks');
    }
};
