<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('apotiks', function (Blueprint $table) {
            $table->string('status')->comment('status pasien pada halaman apotik')->after('pasien_id');
            $table->date('tanggal')->default(DB::raw('CURRENT_DATE'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('apotiks', function (Blueprint $table) {
            //
        });
    }
};
