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
        Schema::create('karyawans', function (Blueprint $table) {
            $table->uuid('id_karyawan')->primary();
            $table->string('nip', 20)->unique();
            $table->string('nama_lengkap', 100);
            $table->string('tempat_lahir', 50)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->text('alamat')->nullable();
            $table->string('no_telp', 15)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('pin', 6)->unique()->comment('PIN 6 digit untuk audit trail');
            $table->enum('status_aktif', ['Aktif', 'Nonaktif', 'Cuti'])->default('Aktif');
            $table->date('tanggal_bergabung')->nullable();
            $table->string('foto')->nullable();
            $table->timestamps();

            $table->index('pin');
            $table->index('nip');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('karyawans');
    }
};
