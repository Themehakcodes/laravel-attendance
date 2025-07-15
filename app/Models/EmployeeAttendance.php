<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeAttendance extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'employee_profile_id',
        'punch_in',
        'punch_out',
        'attendance_date',
        'duration',
        'in_photo',
        'out_photo',
        'attendance_location',
        'verified',
        'gverified_by',
    ];

    protected $casts = [
        'punch_in' => 'datetime',
        'punch_out' => 'datetime',
        'verified' => 'boolean',
    ];

    // Relationships
    public function employeeProfile()
    {
        return $this->belongsTo(EmployeeProfile::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'gverified_by', 'user_id');
    }

    public function expenses()
    {
        return $this->hasMany(EmployeeExpense::class, 'employee_attendance_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
