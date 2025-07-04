<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeProfile;
use App\Models\EmployeeAttendance;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EmployeeProfileController extends Controller
{
    public function show(Request $request, $employee_id)
    {
        $employee = EmployeeProfile::where('employee_id', $employee_id)->firstOrFail();

        // Default to current month
        $filter = $request->input('filter', 'monthly');
        $fromDate = Carbon::now()->startOfMonth();
        $toDate = Carbon::now()->endOfMonth();

        if ($filter === 'custom' && $request->filled(['from', 'to'])) {
            $fromDate = Carbon::parse($request->input('from'))->startOfDay();
            $toDate = Carbon::parse($request->input('to'))->endOfDay();
        }

        // Attendance Summary
        $attendanceStats = EmployeeAttendance::where('employee_profile_id', $employee->id)
            ->whereBetween('punch_in', [$fromDate, $toDate])
            ->selectRaw("duration, COUNT(*) as count")
            ->groupBy('duration')
            ->pluck('count', 'duration');

        // Calendar Events
        $calendarEvents = EmployeeAttendance::where('employee_profile_id', $employee->id)
            ->whereBetween('punch_in', [$fromDate, $toDate])
            ->get()
            ->map(function ($record) {
                return [
                    'title' => ucfirst(str_replace('_', ' ', $record->duration)),
                    'start' => $record->punch_in->format('Y-m-d'),
                    'color' => match ($record->duration) {
                        'full_time' => '#28a745',   // green
                        'half_time' => '#ffc107',   // yellow
                        'leave'     => '#17a2b8',   // blue
                        'absent'    => '#dc3545',   // red
                        default     => '#6c757d'    // gray
                    },
                ];
            });

        return view('admin.pages.profile.index', [
            'employee' => $employee,
            'present' => $attendanceStats['full_time'] ?? 0,
            'halfTime' => $attendanceStats['half_time'] ?? 0,
            'leave' => $attendanceStats['leave'] ?? 0,
            'absent' => $attendanceStats['absent'] ?? 0,
            'filter' => $filter,
            'from' => $fromDate->format('Y-m-d'),
            'to' => $toDate->format('Y-m-d'),
            'calendarEvents' => $calendarEvents,
        ]);
    }
}
