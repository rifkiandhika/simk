<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up()
    {
        // 1. Buat tabel baru untuk menyimpan bukti
        Schema::create('po_proofs', function (Blueprint $table) {
            $table->uuid('id_po_proof')->primary();
            $table->uuid('id_po');
            $table->enum('tipe_bukti', ['invoice', 'barang'])->comment('Tipe bukti: invoice atau barang');
            $table->string('file_path')->comment('Path file bukti');
            $table->string('file_name')->comment('Nama file asli');
            $table->string('file_type')->nullable()->comment('MIME type file');
            $table->integer('file_size')->nullable()->comment('Ukuran file dalam bytes');
            $table->uuid('id_karyawan_upload');
            $table->timestamp('tanggal_upload');
            $table->text('catatan')->nullable();
            $table->boolean('is_active')->default(true)->comment('Status aktif bukti');
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('id_po')->references('id_po')->on('purchase_orders')->onDelete('cascade');
            $table->foreign('id_karyawan_upload')->references('id_karyawan')->on('karyawans');

            // Indexes
            $table->index('id_po');
            $table->index('tipe_bukti');
            $table->index('is_active');
            $table->index(['id_po', 'tipe_bukti', 'is_active']);
        });

        // 2. Migrasi data existing ke tabel baru
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        // Migrasi bukti invoice yang ada
        DB::table('purchase_orders')
            ->whereNotNull('bukti_invoice')
            ->orderBy('created_at')
            ->chunk(100, function ($pos) {
                foreach ($pos as $po) {
                    DB::table('po_proofs')->insert([
                        'id_po_proof' => (string) Str::uuid(),
                        'id_po' => $po->id_po,
                        'tipe_bukti' => 'invoice',
                        'file_path' => $po->bukti_invoice,
                        'file_name' => basename($po->bukti_invoice),
                        'id_karyawan_upload' => $po->id_karyawan_upload_bukti ?? $po->id_karyawan_pemohon,
                        'tanggal_upload' => $po->tanggal_upload_bukti_invoice ?? $po->created_at,
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            });

        // Migrasi bukti barang yang ada
        DB::table('purchase_orders')
            ->whereNotNull('bukti_barang')
            ->orderBy('created_at')
            ->chunk(100, function ($pos) {
                foreach ($pos as $po) {
                    DB::table('po_proofs')->insert([
                        'id_po_proof' => (string) Str::uuid(),
                        'id_po' => $po->id_po,
                        'tipe_bukti' => 'barang',
                        'file_path' => $po->bukti_barang,
                        'file_name' => basename($po->bukti_barang),
                        'id_karyawan_upload' => $po->id_karyawan_upload_bukti_barang ?? $po->id_karyawan_pemohon,
                        'tanggal_upload' => $po->tanggal_upload_bukti_barang ?? $po->created_at,
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            });

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down()
    {
        // 1. Kembalikan kolom ke tabel purchase_orders
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->string('bukti_invoice')->nullable()->after('nomor_faktur_pajak');
            $table->timestamp('tanggal_upload_bukti_invoice')->nullable()->after('bukti_invoice');
            $table->uuid('id_karyawan_upload_bukti')->nullable()->after('tanggal_upload_bukti_invoice');
            $table->string('bukti_barang')->nullable()->after('id_karyawan_upload_bukti');
            $table->timestamp('tanggal_upload_bukti_barang')->nullable()->after('bukti_barang');
            $table->uuid('id_karyawan_upload_bukti_barang')->nullable()->after('tanggal_upload_bukti_barang');

            $table->foreign('id_karyawan_upload_bukti')->references('id_karyawan')->on('karyawans');
            $table->foreign('id_karyawan_upload_bukti_barang')->references('id_karyawan')->on('karyawans');
        });

        // 2. Kembalikan data dari po_proofs ke purchase_orders
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        // Ambil bukti invoice terakhir untuk setiap PO
        $invoiceProofs = DB::table('po_proofs')
            ->where('tipe_bukti', 'invoice')
            ->where('is_active', true)
            ->orderBy('tanggal_upload', 'desc')
            ->get()
            ->groupBy('id_po')
            ->map(function ($group) {
                return $group->first();
            });

        foreach ($invoiceProofs as $proof) {
            DB::table('purchase_orders')
                ->where('id_po', $proof->id_po)
                ->update([
                    'bukti_invoice' => $proof->file_path,
                    'tanggal_upload_bukti_invoice' => $proof->tanggal_upload,
                    'id_karyawan_upload_bukti' => $proof->id_karyawan_upload,
                ]);
        }

        // Ambil bukti barang terakhir untuk setiap PO
        $barangProofs = DB::table('po_proofs')
            ->where('tipe_bukti', 'barang')
            ->where('is_active', true)
            ->orderBy('tanggal_upload', 'desc')
            ->get()
            ->groupBy('id_po')
            ->map(function ($group) {
                return $group->first();
            });

        foreach ($barangProofs as $proof) {
            DB::table('purchase_orders')
                ->where('id_po', $proof->id_po)
                ->update([
                    'bukti_barang' => $proof->file_path,
                    'tanggal_upload_bukti_barang' => $proof->tanggal_upload,
                    'id_karyawan_upload_bukti_barang' => $proof->id_karyawan_upload,
                ]);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // 3. Drop tabel po_proofs
        Schema::dropIfExists('po_proofs');
    }
};