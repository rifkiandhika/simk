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
        Schema::table('reagensias', function (Blueprint $table) {
            $table->bigInteger('harga_beli')->default(0)->after('lokasi_penyimpanan');
            $table->bigInteger('harga_per_test')->default(0)->comment('Harga per pemeriksaan')->after('harga_beli');
            $table->date('tanggal_mulai')->nullable()->after()->after('harga_per_test');
            $table->date('tanggal_selesai')->nullable()->after('tanggal_mulai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reagensias', function (Blueprint $table) {
            //
        });
    }
};
