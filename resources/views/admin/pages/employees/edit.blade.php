@extends('admin.layouts.app')
@section('title', 'Edit Employee')

@section('admincontent')

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center py-4">
    <div class="card card-body border-0 shadow mb-4">

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Whoops! Something went wrong:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Session Success Message --}}
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- Session Error Message --}}
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <h2 class="h5 mb-4">Edit Employee Information</h2>
        <form action="{{ route('employees.update', $employee->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
                <!-- Employee Name -->
                <div class="col-md-6 mb-3">
                    <label for="employee_name">Employee Name</label>
                    <input class="form-control" id="employee_name" name="employee_name" type="text" value="{{ old('employee_name', $employee->employee_name) }}" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="employee_phone_number2">Secondary Phone Number</label>
                    <input class="form-control" id="employee_phone_number2" name="employee_phone_number2" type="text" value="{{ old('employee_phone_number2', $employee->employee_phone_number2) }}">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="employee_phone_number">Phone Number</label>
                    <input class="form-control" id="employee_phone_number" name="employee_phone_number" type="text" value="{{ old('employee_phone_number', $employee->employee_phone_number) }}" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="employee_email">Email</label>
                    <input class="form-control" id="employee_email" name="employee_email" type="email" value="{{ old('employee_email', $employee->employee_email) }}" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="gender">Gender</label>
                    <select class="form-select" id="gender" name="gender">
                        <option value="male" {{ old('gender', $employee->gender) == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender', $employee->gender) == 'female' ? 'selected' : '' }}>Female</option>
                        <option value="other" {{ old('gender', $employee->gender) == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="marital_status">Marital Status</label>
                    <select class="form-select" id="marital_status" name="marital_status">
                        <option value="single" {{ old('marital_status', $employee->marital_status) == 'single' ? 'selected' : '' }}>Single</option>
                        <option value="married" {{ old('marital_status', $employee->marital_status) == 'married' ? 'selected' : '' }}>Married</option>
                        <option value="divorced" {{ old('marital_status', $employee->marital_status) == 'divorced' ? 'selected' : '' }}>Divorced</option>
                        <option value="widowed" {{ old('marital_status', $employee->marital_status) == 'widowed' ? 'selected' : '' }}>Widowed</option>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="employee_dob">Date of Birth</label>
                    <input class="form-control" id="employee_dob" name="employee_dob" type="date" value="{{ old('employee_dob', $employee->employee_dob) }}" required>
                </div>
            </div>

            <h2 class="h5 my-4">Location</h2>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="employee_address">Address</label>
                    <input class="form-control" id="employee_address" name="employee_address" type="text" value="{{ old('employee_address', $employee->employee_address) }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="employee_state">State</label>
                    <input class="form-control" id="employee_state" name="employee_state" type="text" value="{{ old('employee_state', $employee->employee_state) }}" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="district">District</label>
                    <input class="form-control" id="district" name="district" type="text" value="{{ old('district', $employee->district) }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="city">City</label>
                    <input class="form-control" id="city" name="city" type="text" value="{{ old('city', $employee->city) }}" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="pincode">Pincode</label>
                    <input class="form-control" id="pincode" name="pincode" type="text" value="{{ old('pincode', $employee->pincode) }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="permanent_address">Permanent Address</label>
                    <input class="form-control" id="permanent_address" name="permanent_address" type="text" value="{{ old('permanent_address', $employee->permanent_address) }}">
                </div>
            </div>

            <h2 class="h5 my-4">Job Information</h2>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="job_title">Job Title</label>
                    <input class="form-control" id="job_title" name="job_title" type="text" value="{{ old('job_title', $employee->job_title) }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="department">Department</label>
                    <input class="form-control" id="department" name="department" type="text" value="{{ old('department', $employee->department) }}">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="salary">Salary</label>
                    <input class="form-control" id="salary" name="salary" type="number" value="{{ old('salary', $employee->salary) }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="joining_date">Joining Date</label>
                    <input class="form-control" id="joining_date" name="joining_date" type="date" value="{{ old('joining_date', $employee->joining_date) }}" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="employee_status">Employee Status</label>
                    <select class="form-select" id="employee_status" name="employee_status">
                        <option value="full_day" {{ old('employee_status', $employee->employee_status) == 'full_day' ? 'selected' : '' }}>Full Day</option>
                        <option value="part_time" {{ old('employee_status', $employee->employee_status) == 'part_time' ? 'selected' : '' }}>Part Time</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="staff_status">Staff Status</label>
                    <select class="form-select" id="staff_status" name="staff_status">
                        <option value="active" {{ old('staff_status', $employee->staff_status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('staff_status', $employee->staff_status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="aadhaar_photo">Aadhaar Photo</label>
                    <input class="form-control" id="aadhaar_photo" name="aadhaar_photo" type="file" accept="image/*" onchange="previewImage(this, 'aadhaar_preview', 'aadhaar_clear_btn')">
                    @if($employee->aadhaar_photo)
                        <div class="mt-2 position-relative" id="aadhaar_preview_wrapper">
                            <img id="aadhaar_preview" src="{{ asset('storage/'.$employee->aadhaar_photo) }}" alt="Aadhaar Preview" class="img-thumbnail" style="max-height: 150px;">
                            <button type="button" class="btn-close position-absolute top-0 end-0" onclick="clearImage('aadhaar_photo', 'aadhaar_preview_wrapper')"></button>
                        </div>
                    @endif
                </div>

                <div class="col-md-6 mb-3">
                    <label for="photo">Profile Photo</label>
                    <input class="form-control" id="photo" name="photo" type="file" accept="image/*" onchange="previewImage(this, 'profile_preview', 'profile_clear_btn')">
                    @if($employee->photo)
                        <div class="mt-2 position-relative" id="profile_preview_wrapper">
                            <img id="profile_preview" src="{{ asset('storage/'.$employee->photo) }}" alt="Profile Preview" class="img-thumbnail" style="max-height: 150px;">
                            <button type="button" class="btn-close position-absolute top-0 end-0" onclick="clearImage('photo', 'profile_preview_wrapper')"></button>
                        </div>
                    @endif
                </div>
            </div>

            <div class="mt-3">
                <button class="btn btn-primary mt-2 animate-up-2" type="submit">Update Employee</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    @if (session('success'))
        Swal.fire({ icon: 'success', title: 'Success', text: '{{ session('success') }}' });
    @endif

    @if (session('error'))
        Swal.fire({ icon: 'error', title: 'Error', text: '{{ session('error') }}' });
    @endif

    function previewImage(input, previewId, clearBtnId) {
        const preview = document.getElementById(previewId);
        const wrapper = preview.closest('.position-relative');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                wrapper.classList.remove('d-none');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function clearImage(inputId, wrapperId) {
        const input = document.getElementById(inputId);
        const wrapper = document.getElementById(wrapperId);
        input.value = '';
        wrapper.classList.add('d-none');
    }
</script>

@endsection
