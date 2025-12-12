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
        Schema::create('history_gudangs', function (Blueprint $table) {
            $table->uuid('id');
            $table->uuid('supplier_id');
            $table->uuid('barang_id');
            $table->unsignedBigInteger('jumlah');
            $table->dateTime('waktu_proses');
            $table->enum('status', ['penerimaan', 'pengiriman']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history_gudangs');
    }
};
