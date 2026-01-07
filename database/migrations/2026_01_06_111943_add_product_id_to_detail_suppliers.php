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
        Schema::table('detail_suppliers', function (Blueprint $table) {
            $table->uuid('product_id')->nullable()->after('detail_obat_rs_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_suppliers', function (Blueprint $table) {
            //
        });
    }
};
