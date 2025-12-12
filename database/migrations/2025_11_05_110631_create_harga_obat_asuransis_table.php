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
        Schema::create('harga_obat_asuransis', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('id_detail_obat_rs')->constrained('detail_obat_rs', 'id_detail_obat_rs')->onDelete('cascade');
            $table->foreignUuid('asuransi_id')->references('id')->on('asuransis')->onDelete('cascade');
            $table->bigInteger('harga')->default(0);
            $table->decimal('harga_persen', 12, 2)->default(0)->nullable();
            $table->string('mata_uang', 10)->default('IDR')->comment('IDR/USD');
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->boolean('aktif')->default(true);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index('id_detail_obat_rs');
            $table->index('asuransi_id');
            $table->index(['aktif', 'tanggal_mulai', 'tanggal_selesai']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('harga_obat_asuransis');
    }
};
