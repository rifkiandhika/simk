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
        Schema::create('apotiks', function (Blueprint $table) {
            $table->uuid('id');
            $table->string('no_rm', 20)->comment('Nomor Rekam Medis');
            $table->uuid('pasien_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apotiks');
    }
};
