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
        Schema::create('sync_log_obats', function (Blueprint $table) {
            $table->id('id_sync_log');
            $table->foreignId('id_obat_master')->nullable()->constrained('obat_masters', 'id_obat_master')->onDelete('set null');
            $table->string('kfa_code', 50);
            $table->enum('status', ['Success', 'Failed'])->default('Success');
            $table->text('response')->nullable()->comment('Response dari API');
            $table->text('error_message')->nullable()->comment('Jika ada error');
            $table->dateTime('sync_at');
            $table->foreignUuid('synced_by')->constrained('karyawans', 'id_karyawan');

            $table->index('kfa_code');
            $table->index('sync_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sync_log_obats');
    }
};
