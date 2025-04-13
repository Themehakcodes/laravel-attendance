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
        Schema::table('users', function (Blueprint $table) {
            // Adding user_id column (string type)
            $table->string('user_id')->unique()->nullable();

            // Adding user_status column with enum values
            $table->enum('user_status', ['active', 'deactivated', 'hold'])->default('active');

            // Adding is_superadmin column as a boolean flag
            $table->boolean('is_superadmin')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Dropping the added columns
            $table->dropColumn('user_id');
            $table->dropColumn('user_status');
            $table->dropColumn('is_superadmin');
        });
    }
};
