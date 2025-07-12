<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employee_attendances', function (Blueprint $table) {
            $table->id();

            // Reference to user (string user_id)
            $table->string('user_id');

            // Foreign key to employee_profiles
            $table->foreignId('employee_profile_id')->constrained('employee_profiles')->onDelete('cascade');

            // Attendance fields
            $table->timestamp('punch_in')->nullable();
            $table->timestamp('punch_out')->nullable();

            // Duration enum
            $table->enum('duration', ['half_time', 'full_time', 'leave'])->default('full_time');

            // Photos
            $table->string('in_photo')->nullable();
            $table->string('out_photo')->nullable();

            // Location
            $table->string('attendance_location')->nullable();

            // Verified flag and verified by (user_id as string)
            $table->boolean('verified')->default(false);
            $table->string('gverified_by')->nullable(); // user_id of the verifier

            // Timestamps & Soft Deletes
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_attendances');
    }
    
}
