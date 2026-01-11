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
        Schema::create('pasiens', function (Blueprint $table) {
            $table->uuid('id_pasien')->primary();
            $table->string('no_rm', 20)->unique()->comment('Nomor Rekam Medis');
            $table->string('nik', 16)->nullable()->unique();
            $table->string('nama_lengkap', 100);
            $table->string('tempat_lahir', 50)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->enum('golongan_darah', ['A', 'B', 'AB', 'O', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])->nullable();
            $table->text('alamat')->nullable();
            $table->string('no_telp', 15)->nullable();
            $table->string('no_telp_darurat', 15)->nullable();
            $table->string('nama_kontak_darurat', 100)->nullable();
            $table->string('hubungan_kontak_darurat', 50)->nullable();
            $table->enum('status_perkawinan', ['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati'])->nullable();
            $table->string('pekerjaan', 100)->nullable();
            $table->enum('jenis_pembayaran', ['BPJS', 'Umum', 'Asuransi'])->default('Umum');
            $table->string('no_bpjs', 20)->nullable();
            $table->foreignUuid('asuransi_id')->nullable()
                ->constrained('asuransis')
                ->onDelete('set null');
            $table->string('no_polis_asuransi')->nullable();
            $table->string('foto')->nullable();
            $table->enum('status_aktif', ['Aktif', 'Nonaktif'])->default('Aktif');
            $table->date('tanggal')->nullable();
            $table->timestamps();

            $table->index('no_rm');
            $table->index('nik');
            $table->index('nama_lengkap');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pasiens');
    }
};
