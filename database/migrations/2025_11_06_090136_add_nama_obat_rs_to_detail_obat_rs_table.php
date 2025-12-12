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
        Schema::table('detail_obat_rs', function (Blueprint $table) {
            $table->string('nama_obat_rs')->after('kode_obat_rs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_obat_rs', function (Blueprint $table) {
            //
        });
    }
};
