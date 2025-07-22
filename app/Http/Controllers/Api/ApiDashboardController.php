<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmployeeAttendance;
use App\Models\EmployeeExpense;
use App\Models\EmployeeProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ApiDashboardController extends Controller
{
    
  public function todayAttendance(Request $request)
{
    $date = $request->input('date') ?? Carbon::today()->toDateString();

    try {
        $parsedDate = Carbon::parse($date)->toDateString();
    } catch (\Exception $e) {
        return response()->json([
            'status' => 422,
            'message' => 'Invalid date format. Use YYYY-MM-DD.',
        ]);
    }

    // Get all attendance records for the date (even if punch_in is null)
    $attendances = EmployeeAttendance::with('employeeProfile')
        ->whereDate('attendance_date', $parsedDate)
        ->orderByDesc('punch_in')
        ->get();

    $data = $attendances->map(function ($record) {
        $employee = $record->employeeProfile;
        $entryTime = $employee?->entry_time;
        $punchIn = $record->punch_in;

        $timingStatus = null;

        if ($entryTime && $punchIn) {
            $scheduled = Carbon::parse($entryTime);
            $actual = Carbon::parse($punchIn);

            $diffInMinutes = $actual->diffInMinutes($scheduled, false); // negative = late

            if ($diffInMinutes > 10) {
                $timingStatus = 'early';
            } elseif ($diffInMinutes >= -10 && $diffInMinutes <= 10) {
                $timingStatus = 'on time';
            } else {
                $timingStatus = 'late';
            }
        }

        return [
            'name'           => $employee?->employee_name ?? 'Unknown',
            'user_id'        => $record->user_id,
            'punch_in'       => optional($record->punch_in)->format('h:i A'),
            'punch_out'      => optional($record->punch_out)->format('h:i A'),
            'status'         => $record->duration ?? 'Not Marked',
            'profile_id'     => $record->employee_profile_id,
            'entry_time'     => $entryTime?->format('h:i A') ?? 'Not added',
            'timing_status'  => $timingStatus,
        ];
    });

    return response()->json([
        'status'  => 200,
        'message' => 'Attendance for ' . $parsedDate,
        'data'    => $data
    ]);
}



public function todayAttendanceSummary(Request $request)
{
    // Use provided date or default to today
    $date = $request->input('date') ?? \Carbon\Carbon::today()->toDateString();

    try {
        $parsedDate = \Carbon\Carbon::parse($date)->toDateString();
    } catch (\Exception $e) {
        return response()->json([
            'status' => 422,
            'message' => 'Invalid date format. Use YYYY-MM-DD.',
        ]);
    }

    // Get all active employees
    $activeEmployees = \App\Models\User::with('employeeProfile')
        ->where('is_employee', true)
        ->whereHas('employeeProfile', function ($q) {
            $q->where('staff_status', 'active');
        })
        ->get();

    $present = [];
    $halfDay = [];
    $absent = [];
    $onLeave = [];

    foreach ($activeEmployees as $employee) {
        $attendance = \App\Models\EmployeeAttendance::where('user_id', $employee->user_id)
            ->whereDate('attendance_date', $parsedDate)
            ->first();

        $name = $employee->employeeProfile?->employee_name ?? $employee->name;

        if (!$attendance || $attendance->duration === 'absent') {
            $absent[] = [
                'name'    => $name,
                'user_id' => $employee->user_id,
                'status'  => 'Absent',
            ];
        } elseif (strtolower($attendance->duration) === 'leave') {
            $onLeave[] = [
                'name'    => $name,
                'user_id' => $employee->user_id,
                'status'  => 'Leave',
            ];
        } elseif (strtolower($attendance->duration) === 'half_time') {
            $halfDay[] = [
                'name'      => $name,
                'user_id'   => $employee->user_id,
                'punch_in'  => optional($attendance->punch_in)->format('h:i A'),
                'punch_out' => optional($attendance->punch_out)->format('h:i A'),
                'status'    => 'Half Day',
            ];
        } else {
            $present[] = [
                'name'      => $name,
                'user_id'   => $employee->user_id,
                'punch_in'  => optional($attendance->punch_in)->format('h:i A'),
                'punch_out' => optional($attendance->punch_out)->format('h:i A'),
                'status'    => 'Present',
            ];
        }
    }

    return response()->json([
        'status'  => 200,
        'message' => 'Attendance Summary for ' . $parsedDate,
        'totals'  => [
            'total_employees' => $activeEmployees->count(),
            'present'         => count($present),
            'half_day'        => count($halfDay),
            'leave'           => count($onLeave),
            'absent'          => count($absent),
        ],
        'data' => [
            'present'  => $present,
            'half_day' => $halfDay,
            'leave'    => $onLeave,
            'absent'   => $absent,
        ]
    ]);
}


public function getAllEmployees(Request $request)
{
    $employees = User::with('employeeProfile')
        ->where('is_employee', true)
        ->whereHas('employeeProfile', function ($q) {
            $q->where('staff_status', 'active');
        })
        ->get()
        ->map(function ($user) {
            $profile = $user->employeeProfile;

            return [
                'user_id'       => $user->user_id,
                'name'          => $profile?->employee_name ?? $user->name,
                'employee_id'   => $profile?->employee_id ?? 'N/A',
                'job_title'     => $profile?->job_title ?? 'N/A',
                'department'    => $profile?->department ?? 'N/A',
                'salary'        => $profile?->salary ?? 0,
                'entry_time'    => $profile?->entry_time?->format('h:i A') ?? null,
                'status'        => $profile?->staff_status ?? 'unknown',
            ];
        });

    return response()->json([
        'status'  => 200,
        'message' => 'Employee list fetched successfully.',
        'data'    => $employees,
    ]);
}


public function getEmployeeExpenses(Request $request)
{
    $expenses = EmployeeExpense::with(['user', 'employeeProfile'])
        ->orderByDesc('expense_date')
        ->get()
        ->map(function ($expense) {
            return [
                'id'             => $expense->id,
                'user_id'        => $expense->user_id,
                'employee_id'    => $expense->employee_id,
                'employee_name'  => $expense->employeeProfile?->employee_name ?? 'N/A',
                'type'           => $expense->type,
                'amount'         => $expense->amount,
                'description'    => $expense->description,
                'expense_date'   => optional($expense->expense_date)->format('Y-m-d'),
                'is_paid'        => $expense->is_paid,
                'paid_at'        => optional($expense->paid_at)->format('Y-m-d'),
                'payment_method' => $expense->payment_method,
                'notes'          => $expense->notes,
            ];
        });

    return response()->json([
        'status'  => 200,
        'message' => 'All employee expenses fetched successfully.',
        'data'    => $expenses,
    ]);
}
}
