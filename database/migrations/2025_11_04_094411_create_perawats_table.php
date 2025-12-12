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
        Schema::create('perawats', function (Blueprint $table) {
            $table->id('id_perawat');
            $table->foreignUuid('id_karyawan')->constrained('karyawans', 'id_karyawan')->onDelete('cascade');
            $table->string('no_sip', 50)->nullable()->comment('Surat Izin Praktik/STR');
            $table->foreignId('id_unit')->nullable()->constrained('unit', 'id_unit')->onDelete('set null');
            $table->enum('status', ['Aktif', 'Nonaktif', 'Cuti'])->default('Aktif');
            $table->timestamps();

            $table->index('id_karyawan');
            $table->index('id_unit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perawats');
    }
};
