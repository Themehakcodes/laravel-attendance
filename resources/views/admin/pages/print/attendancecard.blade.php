@extends('admin.layouts.app')
@section('title', 'Employee Attendance Cards')

@section('admincontent')
<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Active Employee Attendance Cards</h4>
        <button onclick="window.print()" class="btn btn-primary">Print All</button>
    </div>

    <!-- Search Form -->
    <form method="GET" action="{{ route('employee.attendancecards') }}" class="mb-3">
        <div class="row">
            <div class="col-md-4">
                <input type="text" name="search" value="{{ $search ?? '' }}" class="form-control" placeholder="Search by name or ID">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-outline-secondary">Search</button>
            </div>
        </div>
    </form>

    <!-- Table -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle text-center">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Employee ID</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Department</th>
                    <th>Job Title</th>
                    <th>Joining Date</th>
                    <th>Print</th>
                </tr>
            </thead>
            <tbody>
                @forelse($employees as $index => $employee)
                    <tr>
                        <td>{{ $employees->firstItem() + $index }}</td>
                        <td>{{ $employee->user->user_id }}</td>
                        <td>{{ $employee->employee_name }}</td>
                        <td>{{ $employee->employee_phone_number }}</td>
                        <td>{{ $employee->department }}</td>
                        <td>{{ $employee->job_title }}</td>
                        <td>{{ $employee->joining_date->format('d M Y') }}</td>
                        <td>
                            <a href="{{ route('attendancecard.single.preview', $employee->employee_id) }}" target="_blank" class="btn btn-sm btn-success">
                                Print Card
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">No active employees found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-end">
        {!! $employees->appends(['search' => $search])->links('pagination::bootstrap-5') !!}
    </div>
</div>
@endsection
