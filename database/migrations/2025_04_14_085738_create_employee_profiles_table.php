<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_profiles', function (Blueprint $table) {
            $table->id();

            // Relational
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Basic Info
            $table->string('employee_id')->unique();
            $table->string('employee_name');
            $table->string('employee_phone_number');
            $table->string('employee_phone_number2')->nullable();
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('husband_name')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed'])->nullable();
            $table->date('employee_dob')->nullable();

            // Address & Location
            $table->string('employee_address');
            $table->string('employee_state');
            $table->string('district');
            $table->string('city');
            $table->string('pincode');
            $table->text('permanent_address')->nullable();
            $table->text('current_address')->nullable();

            // Education & Skills
            $table->string('highest_qualification')->nullable();
            $table->json('education_details')->nullable(); // e.g., college, degree, year
            $table->text('languages_known')->nullable();

            // Job Info
            $table->string('job_title')->nullable();
            $table->string('department')->nullable();
            $table->date('joining_date')->nullable();
            $table->decimal('salary', 10, 2)->nullable();
            $table->time('entry_time')->nullable();
            $table->time('exit_time')->nullable();
            $table->enum('employee_status', ['full_day', 'part_time'])->default('full_day');
            $table->enum('staff_status', ['active', 'inactive'])->default('active');
            $table->json('work_experience')->nullable();
            $table->text('notes')->nullable(); // internal notes

            // Documents
            $table->string('employee_aadhaar_no')->nullable();
            $table->string('aadhaar_photo')->nullable();
            $table->string('photo')->nullable();
            $table->string('pan_number')->nullable();

            // Timestamps
            $table->timestamps();
            $table->softDeletes(); // For soft delete functionality
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_profiles');
    }
}
