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
        Schema::create('harga_obats', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('id_detail_obat_rs')->constrained('detail_obat_rs', 'id_detail_obat_rs')->onDelete('cascade');
            $table->bigInteger('harga_obat')->default(0)->comment('Harga umum');
            $table->bigInteger('harga_khusus')->default(0)->comment('Harga khusus/promo');
            $table->bigInteger('harga_bpjs')->default(0)->comment('Harga BPJS');
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();

            $table->index('id_detail_obat_rs');
            $table->index(['aktif', 'tanggal_mulai', 'tanggal_selesai']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('harga_obats');
    }
};
