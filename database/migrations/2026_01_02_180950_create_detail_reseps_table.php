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
        Schema::create('detail_reseps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resep_id')
                ->constrained('reseps')
                ->onDelete('cascade');
            $table->foreignUuid('detail_supplier_id')
                ->constrained('detail_suppliers')
                ->onDelete('cascade');
            $table->uuid('detail_obat_rs_id')->constrained('detail_obat_rs', 'id_detail_obat_rs')->onDelete('cascade');
            $table->integer('jumlah')->default(1);
            $table->decimal('harga_satuan', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->timestamps();
            $table->index('resep_id');
            $table->index('detail_supplier_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_reseps');
    }
};
