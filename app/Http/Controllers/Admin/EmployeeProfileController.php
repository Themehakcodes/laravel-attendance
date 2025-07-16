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

        // ✅ Ensure fromDate is not before joining date
        $joiningDate = optional($employee->joining_date ?? $employee->created_at)->startOfDay();
        if ($fromDate->lessThan($joiningDate)) {
            $fromDate = $joiningDate;
        }

        $attendanceStats = EmployeeAttendance::where('employee_profile_id', $employee->id)
            ->whereBetween('created_at', [$fromDate, $toDate]) // wide enough range
            ->get()
            ->groupBy(function ($record) {
                return optional($record->punch_in)?->format('Y-m-d') ?? (optional($record->attendance_date)?->format('Y-m-d') ?? optional($record->created_at)?->format('Y-m-d'));
            })
            ->map(function ($dayRecords) {
                $durations = $dayRecords->pluck('duration')->unique();

                if ($durations->contains('full_time')) {
                    return 'full_time';
                }
                if ($durations->contains('half_time')) {
                    return 'half_time';
                }
                if ($durations->contains('leave')) {
                    return 'leave';
                }
                if ($durations->contains('absent')) {
                    return 'absent';
                }

                return 'other';
            })
            ->countBy();
        $calendarEvents = EmployeeAttendance::where('employee_profile_id', $employee->id)
            ->whereBetween('created_at', [$fromDate, $toDate]) // fallback safe range
            ->get()
            ->groupBy(fn($record) => optional($record->punch_in)?->format('Y-m-d') ?? (optional($record->attendance_date)?->format('Y-m-d') ?? optional($record->created_at)?->format('Y-m-d')))
            ->map(function ($recordsOfDay) use ($employee) {
                $durations = $recordsOfDay->pluck('duration')->unique();
                $chosen = $recordsOfDay->first(); // fallback

                if ($durations->contains('full_time')) {
                    $chosen = $recordsOfDay->firstWhere('duration', 'full_time');
                } elseif ($durations->contains('half_time')) {
                    $chosen = $recordsOfDay->firstWhere('duration', 'half_time');
                } elseif ($durations->contains('leave')) {
                    $chosen = $recordsOfDay->firstWhere('duration', 'leave');
                } elseif ($durations->contains('absent')) {
                    $chosen = $recordsOfDay->firstWhere('duration', 'absent');
                }

                // ✅ Check for lateness
                $entryTime = $employee->entry_time ?? '09:00:00';
                $isLate = false;

                if ($entryTime) {
                    try {
                        $scheduledEntry = Carbon::createFromFormat('H:i:s', $entryTime);
                    } catch (\Exception $e) {
                        $scheduledEntry = Carbon::createFromFormat('H:i:s', '09:00:00');
                    }

                    $graceTime = $scheduledEntry->copy()->addMinutes(10);

                    foreach ($recordsOfDay as $rec) {
                        $actualTime = optional($rec->punch_in)?->copy();
                        if ($actualTime && $actualTime->greaterThan($graceTime)) {
                            $isLate = true;
                            break;
                        }
                    }
                }

                $punchInTime = optional($chosen->punch_in)->format('H:i');
                $punchOutTime = optional($chosen->punch_out)->format('H:i');

                return [
                    'title' => ucfirst(str_replace('_', ' ', $chosen->duration)) . " ({$punchInTime} - {$punchOutTime})",
                    'start' => optional($chosen->punch_in)?->format('Y-m-d') ?? (optional($chosen->attendance_date)?->format('Y-m-d') ?? optional($chosen->created_at)?->format('Y-m-d')),
                    'color' => $isLate
                        ? '#FF0000'
                        : match ($chosen->duration) {
                            'full_time' => '#28a745',
                            'half_time' => '#ffc107',
                            'leave' => '#17a2b8',
                            'absent' => '#dc3545',
                            default => '#6c757d',
                        },
                    'extendedProps' => [
                        'punch_in_time' => $punchInTime,
                        'punch_out_time' => $punchOutTime,
                        'duration' => $chosen->duration,
                        'late' => $isLate,
                    ],
                ];
            })
            ->values();

        $thisMonthExpenses = $employee
            ->expenses()
            ->whereRaw('COALESCE(expense_date, created_at) BETWEEN ? AND ?', [$fromDate, $toDate])
            ->sum('amount');

        $totalExpenses = $employee->expenses()->sum('amount');
        $previousexpenses = $totalExpenses - $thisMonthExpenses;
        $totalsallery = $employee->Totalbalance();

        $joiningDate = optional($employee->joining_date ?? $employee->created_at)->startOfDay();
        $joiningDay = $joiningDate->day;

        $today = Carbon::now();
        $defaultTo = $today
            ->copy()
            ->setDay(min($joiningDay, $today->daysInMonth))
            ->endOfDay();
        $defaultFrom = $defaultTo->copy()->subMonth()->startOfDay();

        // If from date is before joining date, correct it
        if ($defaultFrom->lessThan($joiningDate)) {
            $defaultFrom = $joiningDate->copy();
        }

        return view('admin.pages.profile.index', [
            'employee' => $employee,
            'totalExpenses' => $totalExpenses,
            'thisMonthExpenses' => $thisMonthExpenses,
            'defaultFrom' => $defaultFrom->format('Y-m-d'),
            'defaultTo' => $defaultTo->format('Y-m-d'),
            'previousexpenses' => $previousexpenses,
            'totalsallery' => $totalsallery,
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
