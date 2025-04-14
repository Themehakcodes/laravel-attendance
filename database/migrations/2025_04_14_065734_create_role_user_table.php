<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('role_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // or admin_id if needed
            $table->timestamps();
            $table->softDeletes(); // 👈 Soft delete column
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_user');
    }
};
