<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\CarbonPeriod;
use App\Models\EmployeeAttendance;
use Carbon\Carbon;

class AttendanceMarker extends Controller
{public function index(Request $request)
{
    $filterType = $request->input('filter', 'today');
    $fromDate = Carbon::today();
    $toDate = Carbon::today();

    if ($filterType === 'custom') {
        if ($request->filled('from') && $request->filled('to')) {
            $fromDate = Carbon::parse($request->input('from'));
            $toDate = Carbon::parse($request->input('to'));
        }
    } elseif ($filterType === 'this_month') {
        $fromDate = Carbon::now()->startOfMonth();
        $toDate = Carbon::now()->endOfMonth();
    } elseif ($filterType === 'last_month') {
        $fromDate = Carbon::now()->subMonth()->startOfMonth();
        $toDate = Carbon::now()->subMonth()->endOfMonth();
    }

    $totalPresent = 0;
    $totalHalfDay = 0;
    $totalLeave = 0;
    $totalAbsent = 0;

    $employees = User::with('employeeProfile')
        ->where('is_employee', true)
             ->orderBy('name') // Order alphabetically by name
        ->get()
        ->map(function ($user) use ($fromDate, $toDate, &$totalPresent, &$totalHalfDay, &$totalLeave, &$totalAbsent) {
            if ($user->employeeProfile && $user->employeeProfile->staff_status === 'active') {
                $attendances = EmployeeAttendance::whereBetween('punch_in', [
                        $fromDate->copy()->startOfDay(),
                        $toDate->copy()->endOfDay()
                    ])
                    ->where('user_id', $user->user_id)
                    ->get();

                $present = $attendances->where('duration', 'full_time')->count();
                $halfDay = $attendances->where('duration', 'half_time')->count();
                $leave = $attendances->where('duration', 'leave')->count();
                $absent = $attendances->where('duration', 'absent')->count();

                $totalPresent += $present;
                $totalHalfDay += $halfDay;
                $totalLeave += $leave;
                $totalAbsent += $absent;

                // Set values to employeeProfile instance
                $user->employeeProfile->present_days = $present;
                $user->employeeProfile->half_days = $halfDay;
                $user->employeeProfile->leave_days = $leave;

                // Earned salary (after unpaid expense deduction)
                $earnedSalary = $user->employeeProfile->calculateEarnedSalary($present, $halfDay, $leave);

                // Total balance (earned - all expenses)
                $totalBalance = $user->employeeProfile->Totalbalance();

                return [
                    'id' => $user->user_id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'profile' => $user->employeeProfile,
                    'attendances' => $attendances,
                    'present' => $present,
                    'half_day' => $halfDay,
                    'leave' => $leave,
                    'absent' => $absent,
                    'earned_salary' => $earnedSalary,
                    'total_balance' => $totalBalance,
                ];
            }
            return null;
        })
        ->filter()
        ->values();

    return view('admin.pages.attendance.view', compact(
        'employees', 'filterType', 'fromDate', 'toDate',
        'totalPresent', 'totalHalfDay', 'totalLeave', 'totalAbsent'
    ));
}

    public function calendar(Request $request)
    {
        $selectedDate = $request->input('date') ?? Carbon::today()->toDateString();
        $date = Carbon::parse($selectedDate);
        $dayName = $date->format('l');

        $employees = User::with('employeeProfile')
            ->where('is_employee', true)
            ->orderBy('name') // Order alphabetically by name
            ->get()
            ->map(function ($user) use ($date) {
                // Only include employees with staff_status = 'active'
                if ($user->employeeProfile && $user->employeeProfile->staff_status === 'active') {
                    $attendance = EmployeeAttendance::whereDate('punch_in', $date)
                        ->where('user_id', $user->user_id)
                        ->first();

                    return [
                        'name' => $user->name,
                        'user_id' => $user->user_id,
                        'email' => $user->email,
                        'employee_profile' => $user->employeeProfile,
                        'attendance_marked' => $attendance ? true : false,
                        'attendance_details' => $attendance,
                    ];
                }
                // Exclude employees who are not active
                return null;
            })->filter()->values();

        return view('admin.pages.attendance.index', [
            'employees' => $employees,
            'today' => $date->toDateString(),
            'dayName' => $dayName,
        ]);
    }

    public function mark(Request $request)
    {
        $request->validate([
            'user_id' => 'required|string',
            'employee_profile_id' => 'required|integer|exists:employee_profiles,id',
            'duration' => 'required|in:full_time,half_time,leave,absent',
            'date' => 'required|date',
        ]);

        $userId = $request->user_id;
        $profileId = $request->employee_profile_id;
        $date = Carbon::parse($request->date)->startOfDay();

        // Check if attendance already exists for this user, profile, and date
        $attendance = EmployeeAttendance::where('user_id', $userId)
            ->where('employee_profile_id', $profileId)
            ->whereDate('punch_in', $date)
            ->first();

        if ($attendance) {
            // Update existing attendance
            $attendance->update([
                'duration' => $request->duration,
                'attendance_location' => 'Admin Panel',
                'verified' => true,
                'gverified_by' => auth()->user()->user_id,
                'punch_in' => $date->copy()->setTime(now()->hour, now()->minute),
            ]);
        } else {
            // Create new attendance
            EmployeeAttendance::create([
                'user_id' => $userId,
                'employee_profile_id' => $profileId,
                'punch_in' => $date->copy()->setTime(now()->hour, now()->minute),
                'duration' => $request->duration,
                'attendance_location' => 'Admin Panel',
                'verified' => true,
                'gverified_by' => auth()->user()->user_id,
            ]);
        }

        return back()->with('success', 'Attendance saved for ' . $userId);
    }
}
