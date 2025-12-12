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
        Schema::create('asuransis', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama_asuransi', 100);
            $table->string('tipe', 50)->nullable()->comment('Swasta/Pemerintah/Corporate');
            $table->string('no_kontrak')->nullable();
            $table->date('tanggal_kontrak_mulai')->nullable();
            $table->date('tanggal_kontrak_selesai')->nullable();
            $table->text('alamat')->nullable();
            $table->string('no_telp', 15)->nullable();
            $table->string('email', 100)->nullable();
            $table->enum('status', ['Aktif', 'Nonaktif'])->default('Aktif');
            $table->timestamps();

            $table->index('nama_asuransi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asuransis');
    }
};
