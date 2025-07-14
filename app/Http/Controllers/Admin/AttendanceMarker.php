<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\CarbonPeriod;
use App\Models\EmployeeProfile;
use App\Models\EmployeeAttendance;
use Carbon\Carbon;

class AttendanceMarker extends Controller
{
    public function index(Request $request)
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
                    $attendances = EmployeeAttendance::whereDate('punch_in', '>=', $fromDate->toDateString())->whereDate('punch_in', '<=', $toDate->toDateString())->where('user_id', $user->user_id)->get();

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
                        'employee_id' => $user->employeeProfile->employee_id ?? null,
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

        return view('admin.pages.attendance.view', compact('employees', 'filterType', 'fromDate', 'toDate', 'totalPresent', 'totalHalfDay', 'totalLeave', 'totalAbsent'));
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
                    $attendance = EmployeeAttendance::whereDate('punch_in', $date)->where('user_id', $user->user_id)->first();

                    return [
                        'name' => $user->name,
                        'user_id' => $user->user_id,
                        'email' => $user->email,
                        'punch_in' => $attendance ? $attendance->punch_in->format('h:i A') : null,
                        'punch_out' => $attendance ? $attendance->punch_out ? $attendance->punch_out->format('h:i A') : null : null,
                        'employee_profile' => $user->employeeProfile,
                        'attendance_marked' => $attendance ? true : false,
                        'attendance_details' => $attendance,
                    ];
                }
                // Exclude employees who are not active
                return null;
            })
            ->filter()
            ->values();

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
        $attendance = EmployeeAttendance::where('user_id', $userId)->where('employee_profile_id', $profileId)->whereDate('punch_in', $date)->first();

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

    public function attendanceCards()
    {
        return view('admin.pages.attendance.cardattendance');
    }

    public function markcardattendance(Request $request)
    {
        $employeeId = $request->barcode;

        $employee = EmployeeProfile::where('employee_id', $employeeId)->first();
        if (!$employee) {
            return back()->with('error', 'Employee not found.');
        }

        $userId = $employee->user->user_id;
        $profileId = $employee->id;

        $now = Carbon::now('Asia/Kolkata'); // Apply Asian timezone here
        $today = $now->copy()->startOfDay();

        $attendance = EmployeeAttendance::where('user_id', $userId)->where('employee_profile_id', $profileId)->whereDate('punch_in', $today)->first();

        // Get expected entry and exit as Carbon instances with same timezone
        $entryTime = $employee->entry_time ? Carbon::parse($employee->entry_time, 'Asia/Kolkata')->setDate($now->year, $now->month, $now->day) : null;

        $exitTime = $employee->exit_time ? Carbon::parse($employee->exit_time, 'Asia/Kolkata')->setDate($now->year, $now->month, $now->day) : null;

        if (!$entryTime || !$exitTime) {
            return back()->with('error', 'Employee shift time not set.');
        }

        $expectedDuration = $entryTime->diffInMinutes($exitTime);
        $seventyFivePercent = floor($expectedDuration * 0.75);
        $cutoffEntry = $entryTime->copy()->addMinutes($seventyFivePercent);

        if ($attendance) {
            $punchInTime = $attendance->punch_in;

            $workedMinutes = $punchInTime->diffInMinutes($now);

            $duration = 'full_time';
            if ($workedMinutes < $seventyFivePercent) {
                $duration = 'half_time';
            }

            $attendance->update([
                'duration' => $duration,
                'punch_out' => $now,
                'attendance_location' => 'Admin Panel',
                'verified' => true,
                'gverified_by' => auth()->user()->user_id,
            ]);

            return back()->with([
                'success' => 'Punch-out recorded for ' . $employee->employee_name . ' (' . strtoupper($duration) . ')',
                'employee' => $employee,
                'time' => $punchInTime->format('h:i A'),
                'punch_out' => $now->format('h:i A'),
                'duration' => $duration,
            ]);
        } else {
            $duration = 'full_time';
            if ($now->gt($cutoffEntry)) {
                $duration = 'half_time'; // Late punch-in
            }

            $attendance = EmployeeAttendance::create([
                'user_id' => $userId,
                'employee_profile_id' => $profileId,
                'punch_in' => $now,
                'duration' => $duration,
                'attendance_location' => 'Admin Panel',
                'verified' => true,
                'gverified_by' => auth()->user()->user_id,
            ]);

            return back()->with([
                'success' => 'Punch-in recorded for ' . $employee->employee_name . ' (' . strtoupper($duration) . ')',
                'employee' => $employee,
                'time' => $now->format('h:i A'),
                'punch_out' => null,
                'duration' => $duration,
            ]);
        }
    }
}
