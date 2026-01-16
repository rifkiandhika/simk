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
        Schema::table('harga_obats', function (Blueprint $table) {
            $table->bigInteger('embalase_bpjs')->default(0)->after('embalase');
            $table->bigInteger('embalase_khusus')->default(0)->after('embalase_bpjs');
            $table->bigInteger('jasa_racik_bpjs')->default(0)->after('jasa_racik');
            $table->bigInteger('jasa_racik_khusus')->default(0)->after('jasa_racik_bpjs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('harga_obats', function (Blueprint $table) {
            //
        });
    }
};
