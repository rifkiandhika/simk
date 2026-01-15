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
        Schema::create('dosis', function (Blueprint $table) {
            $table->uuid('id');
            $table->string('jumlah')->comment('jumlah per minum( mg, tablet, ml)');
            $table->string('frekuensi')->comment('1x, 2x, 3x, sehari');
            $table->string('durasi')->nullable()->comment('3 hari, 7 hari');
            $table->string('rute')->nullable()->comment('oral, IV, IM');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dosis');
    }
};
