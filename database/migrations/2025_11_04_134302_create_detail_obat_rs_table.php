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
        Schema::create('detail_obat_rs', function (Blueprint $table) {
            $table->uuid('id_detail_obat_rs')->primary();
            $table->foreignUuid('id_obat_rs')->constrained('obat_rs', 'id_obat_rs')->onDelete('cascade');
            $table->foreignId('id_obat_master')->constrained('obat_masters', 'id_obat_master')->onDelete('cascade');
            $table->string('kode_obat_rs', 50)->unique()->comment('Kode internal RS');
            $table->integer('stok_minimal')->default(0)->comment('Stok minimal untuk alert');
            $table->integer('stok_maksimal')->default(0)->comment('Stok maksimal');
            $table->string('lokasi_penyimpanan', 100)->nullable()->comment('Rak/Lemari penyimpanan');
            $table->text('catatan_khusus')->nullable()->comment('Catatan internal RS');
            $table->enum('status_aktif', ['Aktif', 'Nonaktif', 'Diskontinyu'])->default('Aktif');
            $table->foreignUuid('created_by')->constrained('karyawans', 'id_karyawan');
            $table->foreignUuid('updated_by')->nullable()->constrained('karyawans', 'id_karyawan');
            $table->timestamps();

            $table->index('kode_obat_rs');
            $table->index('id_obat_master');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_obat_rs');
    }
};
