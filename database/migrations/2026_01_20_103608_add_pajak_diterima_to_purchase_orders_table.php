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
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->decimal('pajak_diterima', 15, 2)->nullable()->after('total_diterima')
                ->comment('Pajak proporsional dari barang yang diterima');
            
            $table->decimal('grand_total_diterima', 15, 2)->nullable()->after('pajak_diterima')
                ->comment('Total akhir yang diterima (total_diterima + pajak_diterima)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            //
        });
    }
};
