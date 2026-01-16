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
        Schema::table('pasiens', function (Blueprint $table) {
            $table->enum('jenis_ruangan', [
                'rawat_jalan',
                'rawat_inap',
                'igd',
                'penunjang'
            ])->nullable()->after('hubungan_kontak_darurat');
            
            // Tambah relasi ke tabel ruangans
            $table->uuid('ruangan_id')->nullable()->after('jenis_ruangan');
            $table->foreign('ruangan_id')
                  ->references('id')
                  ->on('ruangans')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pasiens', function (Blueprint $table) {
            //
        });
    }
};
