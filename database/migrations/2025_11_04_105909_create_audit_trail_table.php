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
        Schema::create('audit_trail', function (Blueprint $table) {
            $table->id('id_audit');
            $table->string('tabel', 100);
            $table->unsignedBigInteger('id_record');
            $table->enum('aksi', ['INSERT', 'UPDATE', 'DELETE']);
            $table->json('data_lama')->nullable()->comment('JSON');
            $table->json('data_baru')->nullable()->comment('JSON');
            $table->foreignUuid('id_karyawan')->constrained('karyawans', 'id_karyawan')->comment('Yang melakukan perubahan');
            $table->string('pin_karyawan', 6)->comment('PIN saat konfirmasi');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index(['tabel', 'id_record']);
            $table->index('id_karyawan');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_trail');
    }
};
