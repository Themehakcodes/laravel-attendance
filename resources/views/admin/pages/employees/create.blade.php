@extends('admin.layouts.app')
@section('title', 'Employee Profile')

@section('admincontent')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center py-4">
        <div class="card card-body border-0 shadow mb-4">
            <h2 class="h5 mb-4">Employee Information</h2>
            <form action="" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <!-- Employee ID -->
                    <div class="col-md-6 mb-3">
                        <label for="employee_id">Employee ID</label>
                        <input class="form-control" id="employee_id" name="employee_id" type="text" placeholder="Enter Employee ID" required>
                    </div>
                    <!-- Employee Name -->
                    <div class="col-md-6 mb-3">
                        <label for="employee_name">Employee Name</label>
                        <input class="form-control" id="employee_name" name="employee_name" type="text" placeholder="Enter Employee Name" required>
                    </div>
                </div>

                <!-- Contact Info -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="employee_phone_number">Phone Number</label>
                        <input class="form-control" id="employee_phone_number" name="employee_phone_number" type="text" placeholder="Enter Phone Number" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="employee_phone_number2">Secondary Phone Number</label>
                        <input class="form-control" id="employee_phone_number2" name="employee_phone_number2" type="text" placeholder="Enter Secondary Phone Number (Optional)">
                    </div>
                </div>

                <!-- Gender & Marital Status -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="gender">Gender</label>
                        <select class="form-select" id="gender" name="gender">
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="marital_status">Marital Status</label>
                        <select class="form-select" id="marital_status" name="marital_status">
                            <option value="single">Single</option>
                            <option value="married">Married</option>
                            <option value="divorced">Divorced</option>
                            <option value="widowed">Widowed</option>
                        </select>
                    </div>
                </div>

                <!-- DOB -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="employee_dob">Date of Birth</label>
                        <input class="form-control" id="employee_dob" name="employee_dob" type="date" required>
                    </div>
                </div>

                <!-- Address Information -->
                <h2 class="h5 my-4">Location</h2>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="employee_address">Address</label>
                        <input class="form-control" id="employee_address" name="employee_address" type="text" placeholder="Enter your home address" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="employee_state">State</label>
                        <input class="form-control" id="employee_state" name="employee_state" type="text" placeholder="Enter State" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="district">District</label>
                        <input class="form-control" id="district" name="district" type="text" placeholder="Enter District" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="city">City</label>
                        <input class="form-control" id="city" name="city" type="text" placeholder="Enter City" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="pincode">Pincode</label>
                        <input class="form-control" id="pincode" name="pincode" type="text" placeholder="Enter Pincode" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="permanent_address">Permanent Address</label>
                        <input class="form-control" id="permanent_address" name="permanent_address" type="text" placeholder="Permanent Address (Optional)">
                    </div>
                </div>

                <!-- Job Information -->
                <h2 class="h5 my-4">Job Information</h2>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="job_title">Job Title</label>
                        <input class="form-control" id="job_title" name="job_title" type="text" placeholder="Enter Job Title">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="department">Department</label>
                        <input class="form-control" id="department" name="department" type="text" placeholder="Enter Department">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="salary">Salary</label>
                        <input class="form-control" id="salary" name="salary" type="number" placeholder="Enter Salary">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="joining_date">Joining Date</label>
                        <input class="form-control" id="joining_date" name="joining_date" type="date" required>
                    </div>
                </div>

                <!-- Employee Status -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="employee_status">Employee Status</label>
                        <select class="form-select" id="employee_status" name="employee_status">
                            <option value="full_day">Full Day</option>
                            <option value="part_time">Part Time</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="staff_status">Staff Status</label>
                        <select class="form-select" id="staff_status" name="staff_status">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>

                <!-- Documents -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="aadhaar_photo">Aadhaar Photo</label>
                        <input class="form-control" id="aadhaar_photo" name="aadhaar_photo" type="file">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="photo">Profile Photo</label>
                        <input class="form-control" id="photo" name="photo" type="file">
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="mt-3">
                    <button class="btn btn-gray-800 mt-2 animate-up-2" type="submit">Save all</button>
                </div>
            </form>
        </div>
    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: '{{ session('success') }}',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            });
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{{ session('error') }}',
                confirmButtonColor: '#d33',
                confirmButtonText: 'OK'
            });
        @endif
    </script>

@endsection
