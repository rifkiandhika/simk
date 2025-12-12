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
        Schema::table('alkes', function (Blueprint $table) {
            $table->bigInteger('harga_beli')->default(0)->after('lokasi_penyimpanan');
            $table->bigInteger('harga_jual_umum')->default(0)->after('harga_beli');
            $table->bigInteger('harga_jual_bpjs')->default(0)->after('harga_jual_umum');
            $table->date('tanggal_mulai')->nullable()->after('harga_jual_bpjs');
            $table->date('tanggal_selesai')->nullable()->after('tanggal_mulai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alkes', function (Blueprint $table) {
            //
        });
    }
};
