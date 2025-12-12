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
        Schema::create('alkes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('kode_alkes', 50)->unique();
            $table->string('nama_alkes', 200);
            $table->string('merk', 100)->nullable();
            $table->string('model', 100)->nullable();
            $table->text('spesifikasi')->nullable();
            $table->string('satuan', 50);
            $table->enum('kategori', ['Alat Medis', 'Alat Lab'])->default('Alat Medis');
            $table->date('tanggal_kalibrasi_terakhir')->nullable();
            $table->date('tanggal_kalibrasi_berikutnya')->nullable();
            $table->text('maintenance_schedule')->nullable();
            $table->integer('stok_minimal')->default(0);
            $table->integer('jumlah_stok')->default(0);
            $table->string('no_batch', 50)->nullable();
            $table->date('tanggal_kadaluarsa')->nullable();
            $table->enum('kondisi', ['Baik', 'Rusak', 'Perlu Maintenance'])->default('Baik');
            $table->string('lokasi_penyimpanan', 100)->nullable();
            $table->text('catatan')->nullable();
            $table->enum('status', ['Aktif', 'Nonaktif', 'Rusak', 'Dalam Perbaikan'])->default('Aktif');
            $table->foreignUuid('created_by')->constrained('karyawans', 'id_karyawan');
            $table->foreignUuid('updated_by')->nullable()->constrained('karyawans', 'id_karyawan');
            $table->timestamps();

            $table->index('kode_alkes');
            $table->index('nama_alkes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alkes');
    }
};
