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
                    <div class="row g-4">

                        <!-- Employee Info Card -->
                        <div class="col-lg-7">
                            <div class="card shadow rounded-4 h-100">
                                <div class="card-header bg-primary text-white fw-bold">
                                    Employee Information {{ $employee->id }}
                                </div>
                                <div class="card-body p-3">
                                    <table class="table table-borderless mb-0">
                                        <tbody>
                                            <tr>
                                                <th scope="row">Email</th>
                                                <td>{{ $employee->employee_email }}</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Phone</th>
                                                <td>{{ $employee->employee_phone_number }}</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Gender</th>
                                                <td>{{ ucfirst($employee->gender) }}</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">DOB</th>
                                                <td>{{ optional($employee->employee_dob)->format('d M Y') }}</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Job Title</th>
                                                <td>{{ $employee->job_title }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Attendance Cards -->
                        <div class="col-lg-5">
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="card bg-success text-white shadow text-center p-3 rounded-4">
                                        <i class="fas fa-user-check fa-2x mb-2"></i>
                                        <h6 class="mb-1">Present</h6>
                                        <h4>{{ $present }}</h4>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="card bg-warning text-white shadow text-center p-3 rounded-4">
                                        <i class="fas fa-user-clock fa-2x mb-2"></i>
                                        <h6 class="mb-1">Half Day</h6>
                                        <h4>{{ $halfTime }}</h4>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="card bg-info text-white shadow text-center p-3 rounded-4">
                                        <i class="fas fa-user-minus fa-2x mb-2"></i>
                                        <h6 class="mb-1">Leave</h6>
                                        <h4>{{ $leave }}</h4>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="card bg-danger text-white shadow text-center p-3 rounded-4">
                                        <i class="fas fa-user-times fa-2x mb-2"></i>
                                        <h6 class="mb-1">Absent</h6>
                                        <h4>{{ $absent }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Salary & Expenses -->
                        <div class="col-12">
                            <div class="card shadow rounded-4">
                                <div
                                    class="card-header bg-primary text-white fw-bold d-flex justify-content-between align-items-center">
                                    <span>Salary & Expense Summary</span>

                                    @php
                                        $isCurrent30DayFilter =
                                            $filter === 'custom' && $from === $defaultFrom && $to === $defaultTo;
                                    @endphp

                                    @if ($isCurrent30DayFilter)
                                        {{-- Show Clear Button --}}
                                        <a href="{{ route('employee.profile.show', ['employee_id' => $employee->employee_id]) }}"
                                            class="btn btn-primary btn-sm">
                                            Clear Filter
                                        </a>
                                    @else
                                        {{-- Show Apply 30-Day Filter Button --}}
                                          <form method="GET"
                                            action="{{ route('employee.profile.show', ['employee_id' => $employee->employee_id]) }}"
                                            class="mb-0">
                                            <input type="hidden" name="filter" value="custom">
                                            <input type="hidden" name="from" value="{{ $defaultFrom }}">
                                            <input type="hidden" name="to" value="{{ $defaultTo }}">
                                            <button type="submit" class="btn btn-primary btn-sm">Calculate Sallery</button>
                                        </form>
                                    @endif
                                </div>

                                <div class="card-body p-3">
                                    <table class="table table-borderless mb-0">
                                        <tbody>
                                            <tr>
                                                <th scope="row">Employee Salary</th>
                                                <td>₹ {{ number_format($employee->salary, 2) }}</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">This Month Expense</th>
                                                <td>₹ {{ number_format($thisMonthExpenses, 2) }}</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Previous Expenses</th>
                                                <td>₹ {{ number_format($previousexpenses, 2) }}</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Final Salary</th>
                                                <td class="fw-bold text-success">
                                                    ₹ {{ number_format($employee->calculateFinalSalary($present), 2) }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
            <!-- Calendar View -->
            <!-- Attendance Calendar Card -->
            <div class="card mt-4 shadow rounded-4 border-0">
                <div class="card-header bg-primary text-white rounded-top-4">
                    <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i> Attendance Calendar</h5>
                </div>
                <div class="card-body bg-light">
                    <div id="attendance-calendar" class="bg-white rounded-3 p-2 shadow-sm"></div>
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
            // Initialize FullCalendar
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
                    eventDisplay: 'block',
                    dayMaxEventRows: 2,
                    selectable: false,
                });
                calendar.render();
            }

            // Initialize Flatpickr
            flatpickr(".flatpickr", {
                dateFormat: "Y-m-d",
                theme: "material_blue" // Optional: install theme if needed
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

            if (filterSelect) {
                toggleDateInputs(filterSelect.value);
                filterSelect.addEventListener('change', function() {
                    toggleDateInputs(this.value);
                });
            }
        });
    </script>

@endsection
