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

    public function getUnpaidExpensesTotalAttribute(): float
{
    return $this->expenses()->where('is_paid', false)->sum('amount');
}

public function expenses()
{
    return $this->hasMany(EmployeeExpense::class, 'employee_id');
}

public function Totalbalance(){
    $earnedSalary = $this->calculateEarnedSalary(
        $this->present_days ?? 0,
        $this->half_days ?? 0,
        $this->leave_days ?? 0,
        false // Don't deduct unpaid expenses here
    );

    $totalExpenses = $this->expenses()->sum('amount');

    return $earnedSalary - $totalExpenses;
}

public function calculateEarnedSalary(int $presentDays, int $halfDays, int $leaveDays = 0, bool $deductUnpaidExpenses = true): float
{
    if (!$this->salary || $this->salary <= 0) {
        return 0;
    }

    $dailyRate = $this->salary / 30;

    // Full present and half day calculation
    $earned = ($presentDays * $dailyRate) + ($halfDays * ($dailyRate / 2));

    // Deduct leave days (unpaid leave)
    $earned -= ($leaveDays * $dailyRate);

    // Deduct unpaid expenses if requested
    if ($deductUnpaidExpenses) {
        $earned -= $this->unpaid_expenses_total;
    }

    return max(0, round($earned, 2)); // Ensure salary is not negative
}


}
