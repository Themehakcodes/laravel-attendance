<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('employee_profiles', function (Blueprint $table) {
        $table->string('fingerprint_hash_1')->nullable();
        $table->string('fingerprint_hash_2')->nullable();
    });
}

public function down()
{
    Schema::table('employee_profiles', function (Blueprint $table) {
        $table->dropColumn(['fingerprint_hash_1', 'fingerprint_hash_2']);
    });
}

};
