<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeProfile extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'employee_id',
        'employee_name',
        'employee_email',
        'employee_phone_number',
        'employee_phone_number2',
        'gender',
        'marital_status',
        'employee_dob',
        'employee_address',
        'employee_state',
        'district',
        'city',
        'pincode',
        'permanent_address',
        'current_address',
        'highest_qualification',
        'education_details',
        'languages_known',
        'job_title',
        'department',
        'joining_date',
        'salary',
        'entry_time',
        'exit_time',
        'employee_status',
        'staff_status',
        'work_experience',
        'notes',
        'employee_aadhaar_no',
        'aadhaar_photo',
        'photo',
        'pan_number',
    ];

    protected $casts = [
        'education_details' => 'array',
        'work_experience' => 'array',
        'joining_date' => 'date',
        'employee_dob' => 'date',
        'entry_time' => 'datetime:H:i:s',
        'exit_time' => 'datetime:H:i:s',
    ];

    // Relationships (optional but recommended)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
