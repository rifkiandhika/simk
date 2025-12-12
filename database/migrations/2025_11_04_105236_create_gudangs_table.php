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
        Schema::create('gudangs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('kode_gudang', 50)->unique();
            $table->string('nama_gudang', 100);
            $table->foreignUuid('supplier_id')
                ->nullable()
                ->constrained('suppliers')
                ->onDelete('set null');
            $table->string('lokasi', 200)->nullable();
            $table->string('penanggung_jawab', 100)->nullable();
            $table->text('keterangan')->nullable();
            $table->enum('status', ['Aktif', 'Nonaktif'])->default('Aktif');
            $table->timestamps();

            $table->index('kode_gudang');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gudangs');
    }
};
