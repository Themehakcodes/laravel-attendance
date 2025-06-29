<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateDurationEnumInEmployeeAttendances extends Migration
{
    public function up(): void
    {
        // Step 1: Rename old column
        Schema::table('employee_attendances', function (Blueprint $table) {
            $table->string('duration_tmp')->nullable();
        });

        // Step 2: Copy data
        DB::table('employee_attendances')->update([
            'duration_tmp' => DB::raw('duration')
        ]);

        // Step 3: Drop old enum column
        Schema::table('employee_attendances', function (Blueprint $table) {
            $table->dropColumn('duration');
        });

        // Step 4: Create new enum column with 'absent' included
        Schema::table('employee_attendances', function (Blueprint $table) {
            $table->enum('duration', ['half_time', 'full_time', 'leave', 'absent'])->default('full_time');
        });

        // Step 5: Copy data back
        DB::table('employee_attendances')->update([
            'duration' => DB::raw('duration_tmp')
        ]);

        // Step 6: Drop temp column
        Schema::table('employee_attendances', function (Blueprint $table) {
            $table->dropColumn('duration_tmp');
        });
    }

    public function down(): void
    {
        // Revert to previous enum state (without 'absent')
        Schema::table('employee_attendances', function (Blueprint $table) {
            $table->string('duration_tmp')->nullable();
        });

        DB::table('employee_attendances')->update([
            'duration_tmp' => DB::raw('duration')
        ]);

        Schema::table('employee_attendances', function (Blueprint $table) {
            $table->dropColumn('duration');
        });

        Schema::table('employee_attendances', function (Blueprint $table) {
            $table->enum('duration', ['half_time', 'full_time', 'leave'])->default('full_time');
        });

        DB::table('employee_attendances')->update([
            'duration' => DB::raw('duration_tmp')
        ]);

        Schema::table('employee_attendances', function (Blueprint $table) {
            $table->dropColumn('duration_tmp');
        });
    }
}
