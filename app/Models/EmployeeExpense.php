<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeExpense extends Model
{
    protected $fillable = [
        'user_id',
        'employee_id',
        'type',
        'amount',
        'description',
         'expense_date', // ← new
        'is_paid',
        'paid_at',
        'payment_method',
        'notes',
    ];

    protected $casts = [
        'is_paid' => 'boolean',
        'paid_at' => 'date',
        'expense_date' => 'date', // ← new
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function employeeProfile()
    {
        return $this->belongsTo(EmployeeProfile::class, 'employee_id');
    }
    
}
