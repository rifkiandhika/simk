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
        Schema::create('detail_suppliers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('supplier_id');
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
            $table->uuid('department_id');
            $table->foreign('department_id')->references('id')->on('department')->onDelete('cascade');
            $table->string('no_batch')->nullable();
            $table->string('judul', 200)->comment('Nama produk/katalog');
            $table->string('nama', 200)->comment('Nama barang detail');
            $table->enum('jenis', ['Obat', 'Alkes', 'Reagensia', 'Lainnya']);
            $table->string('merk', 100)->nullable();
            $table->string('satuan', 50);
            $table->date('exp_date')->nullable();
            $table->bigInteger('stock_live')->default(0)->comment('Stock tersedia');
            $table->bigInteger('stock_po')->default(0)->comment('Stock dalam PO');
            $table->bigInteger('min_persediaan')->default(0);
            $table->string('kode_rak', 50)->nullable()->comment('Lokasi penyimpanan');
            $table->decimal('harga_beli', 15, 2)->default(0);
            $table->timestamps();

            $table->index('supplier_id');
            $table->index('department_id');
            $table->index('jenis');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_suppliers');
    }
};
