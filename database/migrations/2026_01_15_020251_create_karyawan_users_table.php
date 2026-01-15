<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('karyawan_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignUuid('karyawan_id')->constrained('karyawans', 'id_karyawan')->onDelete('cascade');
            $table->boolean('is_primary')->default(false)->comment('Karyawan utama untuk user ini');
            $table->timestamps();

            // Prevent duplicate entries
            $table->unique(['user_id', 'karyawan_id']);
            $table->index('user_id');
            $table->index('karyawan_id');
        });

        // 2. Migrate existing data from users table
        DB::table('users')->whereNotNull('id_karyawan')->each(function ($user) {
            DB::table('karyawan_users')->insert([
                'user_id' => $user->id,
                'karyawan_id' => $user->id_karyawan,
                'is_primary' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        // 3. Drop foreign key and column id_karyawan from users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['id_karyawan']);
            $table->dropColumn('id_karyawan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('karyawan_users');
    }
};
