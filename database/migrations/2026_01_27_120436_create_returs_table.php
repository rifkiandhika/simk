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
        Schema::create('returs', function (Blueprint $table) {
            $table->uuid('id_retur')->primary();
            $table->string('no_retur', 50)->unique(); // Format: RTR-YYYYMMDD-XXX
            
            // Tipe & Sumber Retur
            $table->enum('tipe_retur', ['po', 'stock_apotik'])->comment('Retur dari PO atau Stock Apotik');
            $table->uuid('id_sumber')->comment('ID PO atau ID Stock Apotik');
            $table->string('kode_referensi', 50)->comment('no_po atau kode_transaksi untuk identifikasi');
            
            // Status Retur
            $table->enum('status', [
                'draft',
                'menunggu_persetujuan',
                'disetujui',
                'ditolak',
                'diproses',
                'selesai',
                'dibatalkan'
            ])->default('draft');
            
            // Alasan Retur
            $table->enum('alasan_retur', [
                'barang_rusak',
                'barang_kadaluarsa',
                'barang_tidak_sesuai',
                'kelebihan_pengiriman',
                'kesalahan_order',
                'kualitas_tidak_baik',
                'lainnya'
            ]);
            $table->text('keterangan_alasan')->nullable();
            
            // Informasi Pelapor
            $table->uuid('id_karyawan_pelapor');
            $table->uuid('id_unit_pelapor')->nullable()->comment('ID Apotik atau Gudang');
            $table->enum('unit_pelapor', ['apotik', 'gudang']);
            $table->date('tanggal_retur');
            
            // Tujuan Retur
            $table->uuid('id_unit_tujuan')->nullable()->comment('Gudang atau Supplier');
            $table->enum('unit_tujuan', ['gudang', 'supplier'])->nullable();
            $table->uuid('id_supplier')->nullable()->comment('Jika retur ke supplier');
            
            // Persetujuan
            $table->uuid('id_karyawan_approval')->nullable();
            $table->timestamp('tanggal_approval')->nullable();
            $table->text('catatan_approval')->nullable();
            $table->enum('status_approval', ['pending', 'disetujui', 'ditolak'])->default('pending');
            
            // Proses Retur
            $table->uuid('id_karyawan_pemroses')->nullable();
            $table->timestamp('tanggal_diproses')->nullable();
            $table->timestamp('tanggal_selesai')->nullable();
            
            // Total
            $table->decimal('total_nilai_retur', 15, 2)->default(0);
            $table->text('catatan')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign Keys
            $table->foreign('id_karyawan_pelapor')->references('id_karyawan')->on('karyawans');
            $table->foreign('id_karyawan_approval')->references('id_karyawan')->on('karyawans');
            $table->foreign('id_karyawan_pemroses')->references('id_karyawan')->on('karyawans');
            $table->foreign('id_supplier')->references('id')->on('suppliers');
            
            // Indexes
            $table->index('no_retur');
            $table->index('status');
            $table->index('tipe_retur');
            $table->index(['tipe_retur', 'id_sumber']);
            $table->index('kode_referensi');
        });

        // Detail item retur
        Schema::create('retur_items', function (Blueprint $table) {
            $table->uuid('id_retur_item')->primary();
            $table->uuid('id_retur');
            
            // Referensi ke item asli
            $table->uuid('id_item_sumber')->comment('ID PO Item atau Detail Stock Apotik');
            $table->uuid('id_produk');
            $table->string('nama_produk', 200);
            
            // Quantity
            $table->integer('qty_diretur')->comment('Jumlah yang diretur');
            $table->integer('qty_diterima_kembali')->default(0)->comment('Jumlah yang sudah diterima kembali');
            
            // Harga & Nilai
            $table->decimal('harga_satuan', 15, 2)->default(0);
            $table->decimal('subtotal_retur', 15, 2)->default(0);
            
            // Kondisi & Detail
            $table->enum('kondisi_barang', ['rusak', 'kadaluarsa', 'baik'])->default('rusak');
            $table->text('catatan_item')->nullable();
            
            $table->timestamps();
            
            $table->foreign('id_retur')->references('id_retur')->on('returs')->onDelete('cascade');
            
            $table->index('id_retur');
            $table->index('id_item_sumber');
        });

        // Detail batch untuk retur
        Schema::create('retur_item_batches', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_retur_item');
            
            // Detail Batch
            $table->string('batch_number');
            $table->date('tanggal_kadaluarsa')->nullable();
            
            // Quantity per batch
            $table->integer('qty_diretur')->comment('Jumlah yang diretur dari batch ini');
            
            // Kondisi
            $table->enum('kondisi', ['baik', 'rusak', 'kadaluarsa'])->default('rusak');
            $table->text('catatan')->nullable();
            
            $table->timestamps();
            
            $table->foreign('id_retur_item')
                ->references('id_retur_item')
                ->on('retur_items')
                ->onDelete('cascade');
            
            $table->index(['id_retur_item', 'batch_number']);
        });

        // History/Log perubahan status retur
        Schema::create('retur_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_retur');
            $table->enum('status_dari', [
                'draft',
                'menunggu_persetujuan',
                'disetujui',
                'ditolak',
                'diproses',
                'selesai',
                'dibatalkan'
            ]);
            $table->enum('status_ke', [
                'draft',
                'menunggu_persetujuan',
                'disetujui',
                'ditolak',
                'diproses',
                'selesai',
                'dibatalkan'
            ]);
            $table->uuid('id_karyawan')->comment('Karyawan yang melakukan perubahan');
            $table->text('catatan')->nullable();
            $table->timestamp('waktu_perubahan');
            $table->timestamps();
            
            $table->foreign('id_retur')->references('id_retur')->on('returs')->onDelete('cascade');
            $table->foreign('id_karyawan')->references('id_karyawan')->on('karyawans');
            
            $table->index('id_retur');
        });

        // Dokumen pendukung retur (opsional)
        Schema::create('retur_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_retur');
            $table->string('nama_dokumen');
            $table->string('file_path');
            $table->string('file_type', 50);
            $table->enum('tipe_dokumen', [
                'foto_barang',
                'surat_jalan',
                'berita_acara',
                'lainnya'
            ]);
            $table->text('keterangan')->nullable();
            $table->uuid('id_karyawan_upload');
            $table->timestamps();
            
            $table->foreign('id_retur')->references('id_retur')->on('returs')->onDelete('cascade');
            $table->foreign('id_karyawan_upload')->references('id_karyawan')->on('karyawans');
            
            $table->index('id_retur');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('retur_documents');
        Schema::dropIfExists('retur_histories');
        Schema::dropIfExists('retur_item_batches');
        Schema::dropIfExists('retur_items');
        Schema::dropIfExists('returs');
    }
};
