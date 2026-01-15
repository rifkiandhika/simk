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
            $table->bigInteger('total')->default(0)->after('jasa_racik');
            $table->bigInteger('total_bpjs')->default(0)->after('total');
            $table->bigInteger('total_khusus')->default(0)->after('total_bpjs');
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
