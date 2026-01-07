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
        Schema::create('reseps', function (Blueprint $table) {
            $table->id();
            $table->string('no_resep', 50)->unique()->comment('Nomor Resep');
            $table->foreignId('pasien_id')
                ->nullable()
                ->constrained('pasiens', 'id_pasien')
                ->onDelete('cascade');
            // $table->foreignId('ruangan_id')
            //     ->nullable()
            //     ->constrained('ruangans')
            //     ->onDelete('set null');
            $table->enum('jenis_resep', ['resep', 'resep_luar'])->default('resep');
            $table->string('nama_pasien_luar', 100)->nullable();
            $table->integer('umur')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->text('alamat_luar')->nullable();
            $table->string('dokter_resep', 100)->nullable()->comment('Nama dokter/sumber resep untuk resep luar');
            $table->enum('status_obat', ['Racik', 'Non Racik'])->default('Non Racik');
            $table->string('jenis_racikan', 50)->nullable();
            $table->string('dosis_signa', 50)->nullable()->comment('Dosis/Signa: 3x1, 2x1, dll');
            $table->string('hasil_racikan', 50)->nullable()->comment('Kapsul, Tablet, Sirup, Puyer');
            $table->string('aturan_pakai', 50)->nullable()->comment('Sebelum/Sesudah/Saat Makan');
            $table->decimal('embalase', 10, 2)->default(0);
            $table->decimal('jasa_racik', 10, 2)->default(0);
            $table->decimal('total_harga', 15, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->enum('status', ['menunggu', 'proses', 'selesai', 'batal'])->default('menunggu');
            $table->dateTime('tanggal_resep');
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->timestamps();
            $table->index('no_resep');
            $table->index('jenis_resep');
            $table->index('status');
            $table->index('tanggal_resep');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reseps');
    }
};
