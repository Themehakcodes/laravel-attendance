<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeProfile;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
  {
    // Fetch all employees from the database
    $employees = EmployeeProfile::all();

    // Return the view with the employees data
    return view('admin.pages.employees.index', compact('employees'));
  }

    public function create()
    {
        // Return the view to create a new employee
        return view('admin.pages.employees.create');
    }



    public function store(Request $request)
    {
        // Validate the incoming request data for employee profile and user
        $request->validate([
            'employee_id' => 'required|string|max:255',
            'employee_name' => 'required|string|max:255',
            'employee_email' => 'required|email|max:255|unique:users,email',
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
            'job_title' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'salary' => 'nullable|numeric',
            'joining_date' => 'required|date',
            'employee_status' => 'required|string|in:full_day,part_time',
            'staff_status' => 'required|string|in:active,inactive',
            'aadhaar_photo' => 'nullable|image|max:10240', // Max file size 10MB
            'photo' => 'nullable|image|max:10240', // Max file size 10MB
        ]);

        // Check if the "employee" role exists; if not, create it
        $role = Role::firstOrCreate(['role_name' => 'employee'], ['description' => 'Employee Role']);

        // Create the User (using dob as the password)
        $user = User::create([
            'user_id' => $this->generateUserId($request->employee_name),
            'name' => $request->employee_name,
            'email' => $request->employee_email, // Assuming email is employee_name@example.com
            'password' => bcrypt($request->employee_name), // Password set as employee DOB
            'role_id' => $role->id, // Assign the "employee" role
            'user_status' => 'active', // Assuming all new users are active
        ]);

        // Handle file uploads for Aadhaar photo and profile photo
        $aadhaarPhotoPath = $request->hasFile('aadhaar_photo') ? $request->file('aadhaar_photo')->store('aadhaar_photos', 'public') : null;
        $photoPath = $request->hasFile('photo') ? $request->file('photo')->store('profile_photos', 'public') : null;

        // Clean up the name and get the first 4 characters in uppercase
        $cleanName = preg_replace('/\s+/', '', $request->employee_name);
        $prefix = strtoupper(substr($cleanName, 0, 4));

        // Generate a random 4-digit number for employee_id
        $randomDigits = rand(1000, 9999);
        $employee_id = $prefix . $randomDigits;

        // Create the EmployeeProfile entry
        $employeeProfile = EmployeeProfile::create([
            'user_id' => $user->id, // Link the newly created user
            'employee_id' => $employee_id,
            'employee_name' => $request->employee_name,
            'employee_phone_number' => $request->employee_phone_number,
            'employee_phone_number2' => $request->employee_phone_number2,
            'gender' => $request->gender,
            'marital_status' => $request->marital_status,
            'employee_dob' => $request->employee_dob,
            'employee_address' => $request->employee_address,
            'employee_state' => $request->employee_state,
            'district' => $request->district,
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

        // Redirect back with success message
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



}
