@extends('admin.layouts.app')
@section('title', 'Employee Profile')

@section('admincontent')
    <div class="container-fluid mt-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center py-4">
            <form method="GET" class="mb-4">
                <div class="row g-2 align-items-end">
                    <div class="col-auto">
                        <label for="filter" class="form-label">View</label>
                        <select name="filter" id="filter" class="form-select" onchange="toggleDateInputs(this.value)">
                            <option value="monthly" {{ $filter == 'monthly' ? 'selected' : '' }}>Current Month</option>
                            <option value="custom" {{ $filter == 'custom' ? 'selected' : '' }}>Custom Range</option>
                        </select>
                    </div>

                    <div class="col">
                        <label for="from" class="form-label">From</label>
                        <input type="date" name="from" id="from" value="{{ $from }}"
                            class="form-control flatpickr" placeholder="YYYY-MM-DD">
                    </div>

                    <div class="col">
                        <label for="to" class="form-label">To</label>
                        <input type="date" name="to" id="to" value="{{ $to }}"
                            class="form-control flatpickr" placeholder="YYYY-MM-DD">
                    </div>

                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary">Apply</button>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('employee.profile.show', ['employee_id' => $employee->employee_id]) }}"
                            class="btn btn-secondary">Clear</a>
                    </div>
                </div>
            </form>
        </div>

        <div class="card shadow p-4">
            <div class="row align-items-start">
                <!-- Left: Profile Picture -->
                <div class="col-md-3 text-center mb-3">
                    @php
                        $photoPath = storage_path('app/public/' . $employee->photo);
                    @endphp

                    @if (!empty($employee->photo) && file_exists($photoPath))
                        <img src="{{ asset('storage/' . $employee->photo) }}" alt="Profile Picture"
                            class="img-fluid rounded-circle shadow border"
                            style="width: 180px; height: 180px; object-fit: cover;">
                    @else
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($employee->employee_name) }}&background=random&size=180&rounded=true"
                            alt="No Profile Photo" class="img-fluid rounded-circle shadow border"
                            style="width: 180px; height: 180px; object-fit: cover;">
                    @endif

                    <h5 class="mt-3">{{ $employee->employee_name }}</h5>
                </div>

                <!-- Right: Table + Cards -->
                <div class="col-md-9">
                    <div class="row">
                        <!-- Table -->
                        <div class="col-lg-7 mb-3">
                            <table class="table table-bordered mb-0">
                                <tbody>
                                    <tr>
                                        <th>Email</th>
                                        <td>{{ $employee->employee_email }}</td>
                                    </tr>
                                    <tr>
                                        <th>Phone 1</th>
                                        <td>{{ $employee->employee_phone_number }}</td>
                                    </tr>
                                    <tr>
                                        <th>Gender</th>
                                        <td>{{ ucfirst($employee->gender) }}</td>
                                    </tr>
                                    <tr>
                                        <th>DOB</th>
                                        <td>{{ optional($employee->employee_dob)->format('d M Y') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Job Title</th>
                                        <td>{{ $employee->job_title }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Attendance Cards -->
                        <div class="col-lg-5">
                            <div class="row g-3">
                                <div class="col-12 col-sm-6 mb-3">
                                    <div class="card bg-success text-white shadow text-center p-3 h-100">
                                        <i class="fas fa-user-check fa-lg mb-2"></i>
                                        <h6>Present</h6>
                                        <h5>{{ $present }}</h5>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 mb-3">
                                    <div class="card bg-warning text-white shadow text-center p-3 h-100">
                                        <i class="fas fa-user-clock fa-lg mb-2"></i>
                                        <h6>Half Day</h6>
                                        <h5>{{ $halfTime }}</h5>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 mb-3">
                                    <div class="card bg-info text-white shadow text-center p-3 h-100">
                                        <i class="fas fa-user-minus fa-lg mb-2"></i>
                                        <h6>Leave</h6>
                                        <h5>{{ $leave }}</h5>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 mb-3">
                                    <div class="card bg-danger text-white shadow text-center p-3 h-100">
                                        <i class="fas fa-user-times fa-lg mb-2"></i>
                                        <h6>Absent</h6>
                                        <h5>{{ $absent }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
            <!-- Calendar View -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0">Attendance Calendar</h6>
                </div>
                <div class="card-body">
                    <div id="attendance-calendar"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Required Libraries -->
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Flatpickr -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <!-- FullCalendar v5 (exposes FullCalendar globally) -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Calendar Init
            const calendarEl = document.getElementById('attendance-calendar');
            if (calendarEl) {
                const calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    height: 500,
                    events: @json($calendarEvents),
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: ''
                    },
                });
                calendar.render();
            }

            // Flatpickr Init
            flatpickr(".flatpickr", {
                dateFormat: "Y-m-d"
            });

            // Toggle Date Filter Inputs
            const filterSelect = document.getElementById('filter');
            const fromInput = document.getElementById('from');
            const toInput = document.getElementById('to');

            function toggleDateInputs(value) {
                const isCustom = value === 'custom';
                fromInput.disabled = !isCustom;
                toInput.disabled = !isCustom;
            }

            toggleDateInputs(filterSelect.value);
            filterSelect.addEventListener('change', function() {
                toggleDateInputs(this.value);
            });
        });
    </script>
@endsection
