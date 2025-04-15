<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sidebar_items', function (Blueprint $table) {
            $table->id();
            $table->string('item_name');
            $table->string('item_route');
            $table->string('item_icon');
            $table->string('item_order'); // Type of the sidebar item (e.g., link, dropdown)
            $table->string('item_parent_id'); // Type of the sidebar item (e.g., link, dropdown)
            $table->foreignId('item_permission')->constrained('permissions')->onDelete('cascade'); // Foreign key to permissions table
            $table->timestamps();
            $table->softDeletes(); // Enables soft deletes
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sidebar_items');
    }
};
