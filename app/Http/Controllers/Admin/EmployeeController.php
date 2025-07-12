<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeProfile;
use App\Models\EmployeeAttendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str; 
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function index()
  {
    // Fetch all employees from the database
    $employees = EmployeeProfile::orderBy('employee_name')->get();

    // Return the view with the employees data
    return view('admin.pages.employees.index', compact('employees'));
  }

    public function create()
    {
        // Return the view to create a new employee
        return view('admin.pages.employees.create');
    }

    public function edit($id)
    {
        // Fetch the employee profile by ID
        $employee = EmployeeProfile::findOrFail($id);

        // Return the view to edit the employee
        return view('admin.pages.employees.edit', compact('employee'));
    }

    public function update(Request $request, $id)
    {
        $employee = EmployeeProfile::findOrFail($id);

        $validated = $request->validate([
            'employee_name' => 'required|string|max:255',
            'employee_phone_number' => 'required',
            'employee_email' => 'required|email',
            'gender' => 'required',
            'marital_status' => 'required',
            'employee_dob' => 'required|date',
            'employee_address' => 'required',
            'employee_state' => 'required',
            'district' => 'required',
            'city' => 'required',
            'pincode' => 'required',
            'entry_time' => 'nullable|date_format:H:i',
            'exit_time' => 'nullable|date_format:H:i',
            'job_title' => 'nullable|string',
            'department' => 'nullable|string',
            'salary' => 'nullable|numeric',
            'joining_date' => 'required|date',
            'employee_status' => 'required',
            'staff_status' => 'required',
            'aadhaar_photo' => 'nullable|image|max:2048',
            'photo' => 'nullable|image|max:2048',
        ]);

        // Handle files
        if ($request->hasFile('aadhaar_photo')) {
            $aadhaarPath = $request->file('aadhaar_photo')->store('aadhaar_photos', 'public');
            $validated['aadhaar_photo'] = $aadhaarPath;
        }

        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('profile_photos', 'public');
            $validated['photo'] = $photoPath;
        }

        $employee->update($validated);

        return redirect()->route('employees.index')->with('success', 'Employee updated successfully!');
    }




    public function store(Request $request)
    {
        // Basic validation except email uniqueness (we'll handle it manually)
        $request->validate([
            'employee_name' => 'required|string|max:255',
            'employee_email' => 'required|email|max:255',
            'employee_phone_number' => 'required|string|max:20',
            'employee_phone_number2' => 'nullable|string|max:20',
            'gender' => 'required|string|in:male,female,other',
            'marital_status' => 'required|string|in:single,married,divorced,widowed',
            'employee_dob' => 'required|date',
            'employee_address' => 'required|string|max:255',
            'employee_state' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'pincode' => 'required|string|max:10',
            'permanent_address' => 'nullable|string|max:255',
            'entry_time' => 'nullable|date_format:H:i',
            'exit_time' => 'nullable|date_format:H:i',
            'job_title' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'salary' => 'nullable|numeric',
            'joining_date' => 'required|date',
            'employee_status' => 'required|string|in:full_day,part_time',
            'staff_status' => 'required|string|in:active,inactive',
            'aadhaar_photo' => 'nullable|image|max:10240',
            'photo' => 'nullable|image|max:10240',
        ]);

     

        // Check if the employee role exists
        $role = Role::firstOrCreate(['role_name' => 'employee'], ['description' => 'Employee Role']);

        // Check if a user with this email already exists
        $user = User::where('email', $request->employee_email)->first();

        if ($user) {
            // Check if EmployeeProfile already exists for this user
            $existingProfile = EmployeeProfile::where('user_id', $user->id)->first();

            if ($existingProfile) {
                return redirect()->back()->with('error', 'This email is already registered and assigned to an employee.');
            }

            // User exists but no profile, proceed to create profile
        } else {
            // Create new user
            $user = User::create([
                'user_id' => $this->generateUserId($request->employee_name),
                'name' => $request->employee_name,
                'email' => $request->employee_email,
                'is_employee' => true,
                'password' => bcrypt($request->employee_dob), // Set DOB as password
                'role_id' => $role->id,
                'user_status' => 'active',
            ]);
        }

        // Handle file uploads
        $aadhaarPhotoPath = $request->hasFile('aadhaar_photo')
            ? $request->file('aadhaar_photo')->store('aadhaar_photos', 'public')
            : null;

        $photoPath = $request->hasFile('photo')
            ? $request->file('photo')->store('profile_photos', 'public')
            : null;

        // Generate Employee ID
        $prefix = strtoupper(substr(preg_replace('/\s+/', '', $request->employee_name), 0, 4));
        $employee_id = $prefix . rand(1000, 9999);

        // Create profile
        EmployeeProfile::create([
            'user_id' => $user->id,
            'employee_id' => $employee_id,
            'employee_name' => $request->employee_name,
            'employee_phone_number' => $request->employee_phone_number,
            'employee_phone_number2' => $request->employee_phone_number2,
            'employee_email' => $request->employee_email,
            'gender' => $request->gender,
            'marital_status' => $request->marital_status,
            'employee_dob' => $request->employee_dob,
            'employee_address' => $request->employee_address,
            'employee_state' => $request->employee_state,
            'district' => $request->district,
            'entry_time' => $request->entry_time,
            'exit_time' => $request->exit_time,
            'city' => $request->city,
            'pincode' => $request->pincode,
            'permanent_address' => $request->permanent_address,
            'job_title' => $request->job_title,
            'department' => $request->department,
            'salary' => $request->salary,
            'joining_date' => $request->joining_date,
            'employee_status' => $request->employee_status,
            'staff_status' => $request->staff_status,
            'aadhaar_photo' => $aadhaarPhotoPath,
            'photo' => $photoPath,
        ]);

        return redirect()->back()->with('success', 'Employee profile saved successfully.');
    }

    /**
     * Generate a user ID based on employee name and a random 4-digit number.
     *
     * @param  string  $employeeName
     * @return string
     */
    private function generateUserId($employeeName)
    {
        $cleanName = preg_replace('/\s+/', '', $employeeName);
        $prefix = strtoupper(substr($cleanName, 0, 4));
        $randomDigits = rand(1000, 9999);

        return $prefix . $randomDigits;
    }



    //Employee Deactivate

    public function updatestatus(Request $request, $id)
    {
        $employee = EmployeeProfile::findOrFail($id);
        $employee->staff_status = $request->input('staff_status');
        $employee->save();

        return redirect()->route('employees.index')->with('success', 'Employee status updated successfully!');
    }
public function saveFingerprint(Request $request)
{
    $request->validate([
        'employee_id' => 'required|exists:employee_profiles,employee_id',
        'fingerprint_hash_1' => 'nullable|string',
        'fingerprint_hash_2' => 'nullable|string',
    ]);

    $employee = EmployeeProfile::where('employee_id', $request->employee_id)->first();

    if (!$employee) {
        return response()->json(['error' => 'Employee not found.'], 404);
    }

    $employee->fingerprint_hash_1 = $request->fingerprint_hash_1;
    $employee->fingerprint_hash_2 = $request->fingerprint_hash_2;
    $employee->save();

    return response()->json([
        'message' => 'Fingerprint hashes saved successfully.',
        'employee_id' => $employee->employee_id,
    ]);
}

 public function getFingerprintHashes(Request $request)
{
    $request->validate([
        'employee_id' => 'required|exists:employee_profiles,employee_id',
    ]);

    $employee = EmployeeProfile::where('employee_id', $request->employee_id)->first();

    if (!$employee) {
        return response()->json(['error' => 'Employee not found.'], 404);
    }

    return response()->json([
        'employee_id' => $employee->employee_id,
        'fingerprint_hash_1' => $employee->fingerprint_hash_1,
        'fingerprint_hash_2' => $employee->fingerprint_hash_2,
    ]);
}

public function punchIn(Request $request)
{
    $request->validate([
        'employee_id' => 'required|exists:users,user_id',
        'punch_in' => 'required|date_format:Y-m-d H:i:s', // Accept exact datetime format
    ]);

    $user = User::where('user_id', $request->employee_id)->first();
    
    $userid = $user->id ?? null;

    $employee = EmployeeProfile::where('user_id', $userid)->first();



    if (!$employee) {
        return response()->json(['error' => 'Employee not found'], 404);
    }

    // Check if already punched in for the same date
    $punchInTime = Carbon::parse($request->punch_in);
    $attendanceDate = $punchInTime->toDateString();

    $alreadyPunched = EmployeeAttendance::where('employee_profile_id', $employee->id)
        ->whereDate('punch_in', $attendanceDate)
        ->first();

    if ($alreadyPunched) {
        return response()->json(['message' => 'Already punched in for this date.'], 200);
    }

    $attendance = new EmployeeAttendance();
    $attendance->user_id = $employee->user->user_id; // Assuming user_id is the unique identifier for the user
    $attendance->employee_profile_id = $employee->id;
    $attendance->punch_in = $punchInTime;    
    $attendance->duration = 'full_time';
    $attendance->verified = false;
    $attendance->attendance_location = $request->get('location', 'Fingerprint');
    $attendance->in_photo = $request->get('in_photo', null);

    $attendance->save();

    return response()->json([
        'message' => 'Punch-in recorded successfully.',
        'punch_in_time' => $attendance->punch_in->toDateTimeString(),
        'employee_name' => $employee->employee_name,
    ]);
}

public function punchOut(Request $request)
{
    $request->validate([
        'employee_id' => 'required|exists:users,user_id',
        'punch_out' => 'required|date_format:Y-m-d H:i:s',
    ]);

    $user = User::where('user_id', $request->employee_id)->first();
    $userid = $user->id ?? null;

    $employee = EmployeeProfile::where('user_id', $userid)->first();

    if (!$employee) {
        return response()->json(['error' => 'Employee not found'], 404);
    }

    $punchOutTime = Carbon::parse($request->punch_out);
    $attendanceDate = $punchOutTime->toDateString();

    $attendance = EmployeeAttendance::where('employee_profile_id', $employee->id)
        ->whereDate('punch_in', $attendanceDate)
        ->first();

    if (!$attendance) {
        return response()->json(['error' => 'No punch-in record found for this date.'], 404);
    }

    if ($attendance->punch_out) {
        return response()->json(['message' => 'Already punched out for this date.'], 200);
    }

    $attendance->punch_out = $punchOutTime;
    $attendance->out_photo = $request->get('out_photo', null);
    $attendance->save();

    return response()->json([
        'message' => 'Punch-out recorded successfully.',
        'punch_out_time' => $attendance->punch_out->toDateTimeString(),
        'employee_name' => $employee->employee_name,
    ]);
}


}
