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
        Schema::create('reagensias', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('kode_reagensia', 50)->unique();
            $table->string('nama_reagensia', 200);
            $table->string('merk', 100)->nullable();
            $table->string('no_katalog', 100)->nullable();
            $table->text('komposisi')->nullable();
            $table->string('satuan', 50);
            $table->string('volume_kemasan', 50)->nullable();
            $table->decimal('suhu_penyimpanan_min', 5, 2)->nullable()->comment('Celcius');
            $table->decimal('suhu_penyimpanan_max', 5, 2)->nullable()->comment('Celcius');
            $table->text('kondisi_penyimpanan')->nullable();
            $table->integer('stabilitas_hari')->nullable()->comment('Setelah dibuka');
            $table->text('prosedur_penggunaan')->nullable();
            $table->text('bahaya_keselamatan')->nullable()->comment('MSDS info');
            $table->integer('stok_minimal')->default(0);
            $table->string('lokasi_penyimpanan', 100)->nullable();
            $table->enum('status', ['Aktif', 'Nonaktif'])->default('Aktif');
            $table->foreignUuid('created_by')->constrained('karyawans', 'id_karyawan');
            $table->foreignUuid('updated_by')->nullable()->constrained('karyawans', 'id_karyawan');
            $table->timestamps();

            $table->index('kode_reagensia');
            $table->index('nama_reagensia');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reagensias');
    }
};
