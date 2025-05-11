<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmployeeAttendance;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        return view('employee.pages.cameraattendance.cemera');
    }

public function punchIn(Request $request)
{
    $request->validate([
        'in_photo' => 'nullable|string',
    ]);

    $user = Auth::user();
    $imagePath = null;

    if ($request->filled('in_photo')) {
        $image = $request->input('in_photo');

        if (preg_match("/^data:image\/(\w+);base64,/", $image, $matches)) {
            $imageType = $matches[1];
            $imageData = base64_decode(str_replace("data:image/{$imageType};base64,", '', $image));
            $imageName = 'attendance_' . uniqid() . '.' . $imageType;
            $imagePath = 'attendance_photos/' . $imageName;

            // Ensure directory exists
            $directory = public_path('storage/attendance_photos');
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            file_put_contents(public_path('storage/' . $imagePath), $imageData);
        } else {
            return redirect()->back()->with('error', 'Invalid image format.');
        }
    }

    EmployeeAttendance::create([
        'user_id' => $user->user_id,
        'employee_profile_id' => $user->employeeProfile->id ?? null,
        'punch_in' => now(),
        'in_photo' => $imagePath,
        'attendance_location' => $request->attendance_location,
        'verified' => false,
    ]);

    return redirect()->back()->with('success', 'Punch-in recorded successfully!');
}


    // ✅ Punch Out Method
    public function punchOut(Request $request)
    {
        $request->validate([
            'out_photo' => 'required|image|mimes:jpeg,png,jpg',
        ]);

        $user = Auth::user();

        $attendance = EmployeeAttendance::where('user_id', $user->user_id)
            ->whereNull('punch_out')
            ->latest()
            ->first();

        if (!$attendance) {
            return redirect()->back()->with('error', 'No active punch-in record found.');
        }

        // Save punch-out photo
        $photoPath = $request->file('out_photo')->store('attendance_photos', 'public');
        $punchOutTime = Carbon::now();

        // Update attendance record
        $attendance->update([
            'punch_out' => $punchOutTime,
            'out_photo' => $photoPath,
            'duration' => $punchOutTime->diffInMinutes($attendance->punch_in),
        ]);

        return redirect()->back()->with('success', 'Punch-out recorded successfully.');
    }
}
