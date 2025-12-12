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
        Schema::create('tindakan_medis_detail', function (Blueprint $table) {
            $table->id('id_detail');
            $table->foreignId('id_rekam_medis')->constrained('rekam_medis', 'id_rekam_medis')->onDelete('cascade');
            $table->foreignId('id_tindakan')->constrained('tindakan_medis', 'id_tindakan')->onDelete('cascade');
            $table->integer('jumlah')->default(1);
            $table->decimal('tarif', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->text('keterangan')->nullable();
            $table->foreignUuid('created_by')->constrained('karyawans', 'id_karyawan');
            $table->timestamps();

            $table->index('id_rekam_medis');
            $table->index('id_tindakan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tindakan_medis_detail');
    }
};
