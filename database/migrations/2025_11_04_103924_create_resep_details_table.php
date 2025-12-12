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
        Schema::create('resep_details', function (Blueprint $table) {
            $table->id('id_resep_detail');
            $table->foreignId('id_resep')->constrained('reseps', 'id_resep')->onDelete('cascade');
            $table->foreignUuid('id_obat_rs')->constrained('obat_rs', 'id_obat_rs')->onDelete('cascade');
            $table->integer('jumlah')->default(1);
            $table->text('aturan_pakai')->nullable();
            $table->decimal('harga_satuan', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->timestamps();

            $table->index('id_resep');
            $table->index('id_obat_rs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resep_details');
    }
};
