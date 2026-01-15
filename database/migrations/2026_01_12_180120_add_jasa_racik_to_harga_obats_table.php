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
            $table->bigInteger('embalase')->default(0)->after('harga_bpjs');
            $table->bigInteger('jasa_racik')->default(0)->after('embalase');
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
