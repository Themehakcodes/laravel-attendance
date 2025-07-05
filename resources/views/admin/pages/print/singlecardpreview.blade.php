@extends('admin.layouts.app')
@section('title', 'Print Attendance Card')

@section('admincontent')
<div class="container mt-5">
    <div class="card shadow p-4">
        <h4 class="mb-3">Attendance Card</h4>

        <p><strong>Employee ID:</strong> {{ $employee->employee_id }}</p>
        <p><strong>Name:</strong> {{ $employee->employee_name }}</p>
        <p><strong>Phone:</strong> {{ $employee->employee_phone_number }}</p>
        <p><strong>Department:</strong> {{ $employee->department }}</p>
        <p><strong>Job Title:</strong> {{ $employee->job_title }}</p>
        <p><strong>Joining Date:</strong> {{ $employee->joining_date->format('d M Y') }}</p>

        {{-- Add more details if needed --}}
    </div>

   <div class="mt-3">
        <a href="{{ route('attendancecard.single', ['employee_id' => $employee->employee_id]) }}" class="btn btn-primary">
            Print
        </a>
    </div>
</div>
@endsection
