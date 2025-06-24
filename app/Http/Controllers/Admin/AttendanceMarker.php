<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\EmployeeAttendance;
use Carbon\Carbon;

class AttendanceMarker extends Controller
{
    public function calendar(Request $request)
    {
        $selectedDate = $request->input('date') ?? Carbon::today()->toDateString();
        $date = Carbon::parse($selectedDate);
        $dayName = $date->format('l');

        $employees = User::with('employeeProfile')
            ->where('is_employee', true)
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
            'duration' => 'required|in:full_time,half_time,leave',
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
