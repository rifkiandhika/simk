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
        Schema::create('kfa_mapping_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('id_obat_master_old')->nullable();
            $table->unsignedBigInteger('id_obat_master_new');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreignUuid('id_detail_obat_rs')
                ->references('id_detail_obat_rs')
                ->on('detail_obat_rs')
                ->onDelete('cascade');

            $table->foreign('id_obat_master_old')
                ->references('id_obat_master')
                ->on('obat_masters')
                ->onDelete('set null');

            $table->foreign('id_obat_master_new')
                ->references('id_obat_master')
                ->on('obat_masters')
                ->onDelete('cascade');


            $table->uuid('changed_by')->nullable();
            $table->foreign('changed_by')
                ->references('id_karyawan')
                ->on('karyawans')
                ->onDelete('set null');

            // Indexes
            $table->index('id_detail_obat_rs');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kfa_mapping_histories');
    }
};
