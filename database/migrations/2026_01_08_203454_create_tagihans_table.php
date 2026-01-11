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
        Schema::create('tagihans', function (Blueprint $table) {
            $table->id('id_tagihan');
            $table->string('no_tagihan', 50)->unique();

            $table->foreignId('id_registrasi')
                ->constrained('registrasis', 'id_registrasi')
                ->cascadeOnDelete();

            $table->foreignId('id_pasien')
                ->constrained('pasiens', 'id_pasien')
                ->cascadeOnDelete();

            $table->date('tanggal_tagihan');

            $table->enum('jenis_tagihan', [
                'IGD',
                'RAWAT_JALAN',
                'RAWAT_INAP'
            ]);

            $table->decimal('total_tagihan', 15, 2)->default(0);
            $table->decimal('total_dibayar', 15, 2)->default(0);
            $table->decimal('sisa_tagihan', 15, 2)->default(0);

            $table->enum('status', [
                'BELUM_LUNAS',
                'CICILAN',
                'LUNAS'
            ])->default('BELUM_LUNAS');

            $table->timestamp('locked_at')->nullable();
            $table->foreignUuid('locked_by')->nullable()
                ->constrained('karyawans', 'id_karyawan');

            $table->enum('status_klaim', [
                'NON_KLAIM',
                'PENDING',
                'DISETUJUI',
                'DITOLAK'
            ])->default('NON_KLAIM');

            // $table->check('sisa_tagihan >= 0');
            // $table->check('total_dibayar <= total_tagihan');

            $table->date('tanggal_lunas')->nullable();

            $table->boolean('locked')->default(false);

            $table->text('catatan')->nullable();

            $table->foreignUuid('created_by')
                ->constrained('karyawans', 'id_karyawan');

            $table->timestamps();
            $table->softDeletes();

            $table->index('no_tagihan');
            $table->index(['id_pasien', 'tanggal_tagihan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tagihans');
    }
};
