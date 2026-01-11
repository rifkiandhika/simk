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
        Schema::table('reseps', function (Blueprint $table) {
            $table->foreignId('verified_by')->nullable()->after('user_id')->constrained('users');
            $table->foreignId('dispensed_by')->nullable()->after('verified_by')->constrained('users');
            $table->timestamp('verified_at')->nullable()->after('dispensed_by');
            $table->timestamp('dispensed_at')->nullable()->after('verified_at');
            $table->text('rejection_reason')->nullable()->after('dispensed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reseps', function (Blueprint $table) {
            //
        });
    }
};
