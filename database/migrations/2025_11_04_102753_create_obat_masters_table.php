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
        Schema::create('obat_masters', function (Blueprint $table) {
            $table->id('id_obat_master');
            $table->string('kfa_code', 50)->unique()->comment('Kode KFA dari Satu Sehat');
            $table->string('nama_obat', 200)->comment('Nama dagang dari API');
            $table->string('nama_generik', 200)->nullable()->comment('Nama generik dari API');
            $table->string('bentuk_sediaan', 100)->nullable()->comment('Tablet/Kapsul/Sirup/Injeksi');
            $table->string('kekuatan', 50)->nullable()->comment('Contoh: 500mg, 10ml');
            $table->string('satuan_kekuatan', 20)->nullable()->comment('mg/ml/mcg/IU');
            $table->string('kemasan', 50)->nullable()->comment('Strip/Box/Botol/Tube');
            $table->string('isi_kemasan', 50)->nullable()->comment('Contoh: 10 tablet/strip');
            $table->string('manufacturer', 200)->nullable()->comment('Produsen');
            $table->string('nie', 100)->nullable()->comment('Nomor Izin Edar');
            $table->text('komposisi')->nullable();
            $table->text('indikasi')->nullable();
            $table->text('kontraindikasi')->nullable();
            $table->text('efek_samping')->nullable();
            $table->text('peringatan')->nullable();
            $table->text('dosis')->nullable();
            $table->enum('kategori', ['Generik', 'Paten', 'OTC'])->nullable();
            $table->enum('golongan', ['Bebas', 'Bebas Terbatas', 'Keras', 'Narkotika', 'Psikotropika'])->nullable();
            $table->json('data_api')->nullable()->comment('Raw data dari API Satu Sehat');
            $table->enum('status', ['Aktif', 'Nonaktif'])->default('Aktif');
            $table->timestamp('last_sync')->nullable()->comment('Terakhir sync dari API');
            $table->timestamps();

            $table->index('kfa_code');
            $table->index('nama_obat');
            $table->index('nama_generik');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('obat_masters');
    }
};
