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

        // Get active employees
        $activeEmployees = User::with('employeeProfile')
            ->where('is_employee', true)
            ->whereHas('employeeProfile', function ($q) {
                $q->where('staff_status', 'active');
            })
            ->get();

        foreach ($activeEmployees as $employee) {
            $alreadyExists = EmployeeAttendance::whereDate('attendance_date', $date)->where('user_id', $employee->user_id)->exists();

            if (!$alreadyExists) {
                // Insert default absent entry
                EmployeeAttendance::create([
                    'user_id' => $employee->user_id,
                    'employee_profile_id' => $employee->employeeProfile->id ?? null,
                    'attendance_date' => $date,
                    'punch_in' => null,
                    'punch_out' => null,
                    'in_photo' => null,
                    'out_photo' => null,
                    'attendance_location' => null,
                    'verified' => 1,
                    'gverified_by' => null,
                    'duration' => 'absent',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Now fetch all attendance info
        $employees = $activeEmployees
            ->sortBy('name')
            ->map(function ($user) use ($date) {
                $attendance = EmployeeAttendance::where(function ($query) use ($date) {
                    $query->whereDate('attendance_date', $date)->orWhere(function ($q) use ($date) {
                        $q->whereNull('attendance_date')->whereDate('created_at', $date);
                    });
                })
                    ->where('user_id', $user->user_id)
                    ->first();

                return [
                    'name' => $user->name,
                    'user_id' => $user->user_id,
                    'email' => $user->email,
                    'punch_in' => $attendance?->punch_in?->format('h:i A'),
                    'punch_out' => $attendance?->punch_out?->format('h:i A'),
                    'employee_profile' => $user->employeeProfile,
                    'attendance_marked' => (bool) $attendance,
                    'attendance_details' => $attendance,
                ];
            })
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
        'time' => 'nullable|string',
        'date' => 'required|date',
    ]);

   

    $userId = $request->user_id;
    $profileId = $request->employee_profile_id;

    // Use start of the given date
    $date = Carbon::parse($request->date)->startOfDay();

    // Use time of now but with date from request
    $currentTime = Carbon::now('Asia/Kolkata');
    $timeToUse = $date->copy()->setTimeFrom($currentTime);  // Final datetime = requested date + current time

    $attendanceData = [
        'user_id' => $userId,
        'employee_profile_id' => $profileId,
        'attendance_date' => $date,
        'duration' => $request->duration,
        'attendance_location' => 'Admin Panel',
        'verified' => true,
        'gverified_by' => auth()->user()->user_id,
    ];

    // Add punch_in or punch_out with correct datetime
    if ($request->duration === 'full_time') {
        $attendanceData['punch_in'] = $timeToUse;
    } elseif ($request->duration === 'half_time') {
        $attendanceData['punch_out'] = $timeToUse;
    }

    // Update or create
    $attendance = EmployeeAttendance::where('user_id', $userId)
        ->where('employee_profile_id', $profileId)
        ->whereDate('attendance_date', $date)
        ->first();

    if ($attendance) {
        $attendance->update($attendanceData);
    } else {
        EmployeeAttendance::create($attendanceData);
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

        $attendance = EmployeeAttendance::where('user_id', $userId)->where('employee_profile_id', $profileId)->whereDate('attendance_date', $today)->first();

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
    }public function punchIn(Request $request)
{
    $request->validate([
        'user_id' => 'required|string',
        'employee_profile_id' => 'required|integer|exists:employee_profiles,id',
        'date' => 'required|date',
        'time' => 'nullable|date_format:H:i', // allow custom time (HH:MM)
    ]);

    $userId = $request->user_id;
    $profileId = $request->employee_profile_id;

    $date = Carbon::parse($request->date)->startOfDay();

    // ✅ Use provided time if available, otherwise fallback to now
    if ($request->filled('time')) {
        [$hour, $minute] = explode(':', $request->time);
        $punchInTime = $date->copy()->setTime($hour, $minute);
    } else {
        $currentTime = Carbon::now('Asia/Kolkata');
        $punchInTime = $date->copy()->setTimeFrom($currentTime);
    }

    $attendance = EmployeeAttendance::where('user_id', $userId)
        ->where('employee_profile_id', $profileId)
        ->whereDate('attendance_date', $date)
        ->first();

    if ($attendance) {
        $attendance->update([
            'punch_in' => $punchInTime,
            'attendance_location' => 'Admin Panel',
            'verified' => true,
            'duration' => 'full_time',
            'gverified_by' => auth()->user()->user_id,
        ]);
    } else {
        EmployeeAttendance::create([
            'user_id' => $userId,
            'employee_profile_id' => $profileId,
            'attendance_date' => $date,
            'duration' => 'full_time',
            'punch_in' => $punchInTime,
            'attendance_location' => 'Admin Panel',
            'verified' => true,
            'gverified_by' => auth()->user()->user_id,
        ]);
    }

    return back()->with('success', 'Punch In recorded successfully.');
}

public function punchOut(Request $request)
{
    $request->validate([
        'user_id' => 'required|string',
        'employee_profile_id' => 'required|integer|exists:employee_profiles,id',
        'date' => 'required|date',
        'time' => 'nullable|date_format:H:i', // allow optional time
    ]);

    $userId = $request->user_id;
    $profileId = $request->employee_profile_id;

    $date = Carbon::parse($request->date)->startOfDay();

    // ✅ Use provided time if available, otherwise fallback to current time
    if ($request->filled('time')) {
        [$hour, $minute] = explode(':', $request->time);
        $punchOutTime = $date->copy()->setTime($hour, $minute);
    } else {
        $currentTime = Carbon::now('Asia/Kolkata');
        $punchOutTime = $date->copy()->setTimeFrom($currentTime);
    }

    $attendance = EmployeeAttendance::where('user_id', $userId)
        ->where('employee_profile_id', $profileId)
        ->whereDate('attendance_date', $date)
        ->first();

    if ($attendance) {
        $attendance->update([
            'punch_out' => $punchOutTime,
            'attendance_location' => 'Admin Panel',
            'verified' => true,
            'duration' => 'full_time',
            'gverified_by' => auth()->user()->user_id,
        ]);

        return back()->with('success', 'Punch Out recorded successfully.');
    }

    return back()->with('error', 'Attendance record not found for the selected date.');
}



}
