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
        Schema::table('reseps', function (Blueprint $table) {
            $table->decimal('diskon', 15, 2)->default(0)->after('embalase');
            $table->enum('diskon_type', ['percent', 'idr'])->default('percent')->after('diskon');
            $table->decimal('pajak', 15, 2)->default(0)->after('diskon_type');
            $table->enum('pajak_type', ['percent', 'idr'])->default('percent')->after('pajak');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reseps', function (Blueprint $table) {
            //
        });
    }
};
