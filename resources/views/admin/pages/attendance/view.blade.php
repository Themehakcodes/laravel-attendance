@extends('admin.layouts.app')
@section('title', 'Attendance Overview')
@section('admincontent')

<!-- Flatpickr CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

<div class="container-fluid py-4">

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow bg-success text-white text-center">
                <div class="card-body">
                    <i class="bi bi-check-circle-fill fs-1 mb-2"></i>
                    <h6 class="mb-0">Total Present</h6>
                    <h3>{{ $totalPresent ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow bg-warning text-dark text-center">
                <div class="card-body">
                    <i class="bi bi-clock-history fs-1 mb-2"></i>
                    <h6 class="mb-0">Half Day</h6>
                    <h3>{{ $totalHalfDay ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow bg-primary text-white text-center">
                <div class="card-body">
                    <i class="bi bi-door-open-fill fs-1 mb-2"></i>
                    <h6 class="mb-0">On Leave</h6>
                    <h3>{{ $totalLeave ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow bg-danger text-white text-center">
                <div class="card-body">
                    <i class="bi bi-x-circle-fill fs-1 mb-2"></i>
                    <h6 class="mb-0">Absent</h6>
                    <h3>{{ $totalAbsent ?? 0 }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="card shadow rounded-4">
        <div class="card-body">
            <h4 class="card-title mb-4">Employee Attendance</h4>

            {{-- Filter Form --}}
            <form method="GET" class="row g-3 mb-4">
                <div class="col-md-3">
                    <label>Quick Filter</label>
                    <select name="filter" class="form-select" id="filterSelect">
                        <option value="today" {{ $filterType === 'today' ? 'selected' : '' }}>Today</option>
                        <option value="this_month" {{ $filterType === 'this_month' ? 'selected' : '' }}>This Month</option>
                        <option value="last_month" {{ $filterType === 'last_month' ? 'selected' : '' }}>Last Month</option>
                        <option value="custom" {{ $filterType === 'custom' ? 'selected' : '' }}>Custom Range</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>From Date</label>
                    <input type="text" id="fromDate" name="from" class="form-control" placeholder="Select start date"
                           value="{{ request('from') ?? $fromDate->toDateString() }}"
                           {{ $filterType !== 'custom' ? 'disabled' : '' }}>
                </div>
                <div class="col-md-3">
                    <label>To Date</label>
                    <input type="text" id="toDate" name="to" class="form-control" placeholder="Select end date"
                           value="{{ request('to') ?? $toDate->toDateString() }}"
                           {{ $filterType !== 'custom' ? 'disabled' : '' }}>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Apply Filter</button>
                </div>
            </form>

            {{-- Attendance Table --}}
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Employee</th>
                            <th>Email</th>
                            <th>Department</th>
                            <th>Present</th>
                            <th>Half Day</th>
                            <th>Leave</th>
                            <th>Unpaid Expenses</th>
                            <th>Total Salary</th>
                            <th>Earned Salary</th>
                            <th>Total Balance</th>
                            <th>Attendance Records</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($employees as $index => $emp)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $emp['name'] }}</td>
                                <td>{{ $emp['email'] }}</td>
                                <td>{{ $emp['profile']->department ?? '-' }}</td>
                                <td><span class="badge bg-success">{{ $emp['present'] }}</span></td>
                                <td><span class="badge bg-warning text-dark">{{ $emp['half_day'] }}</span></td>
                                <td><span class="badge bg-primary">{{ $emp['leave'] }}</span></td>
                                
                                <!-- Unpaid Expenses -->
                                <td>
                                    ₹{{ number_format($emp['profile']->unpaid_expenses_total ?? 0, 2) }}
                                    @if($emp['profile']->unpaid_expenses_total > 0)
                                        <button class="btn btn-sm btn-outline-secondary ms-1" data-bs-toggle="modal" data-bs-target="#expensesModal{{ $index }}">
                                            View
                                        </button>

                                        <!-- Modal for Expense Details -->
                                        <div class="modal fade" id="expensesModal{{ $index }}" tabindex="-1" aria-labelledby="expensesLabel{{ $index }}" aria-hidden="true">
                                            <div class="modal-dialog modal-md modal-dialog-scrollable">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Unpaid Expenses - {{ $emp['name'] }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        @foreach ($emp['profile']->expenses->where('is_paid', false) as $expense)
                                                            <div class="mb-2 border-bottom pb-1">
                                                                <strong>{{ $expense->type }}</strong> — ₹{{ number_format($expense->amount, 2) }}
                                                                <br>
                                                                <small class="text-muted">{{ $expense->description }}</small>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </td>

                                <!-- Total Salary -->
                                <td>₹{{ number_format($emp['profile']->salary ?? 0, 2) }}</td>

                                <!-- Earned Salary -->
                                <td>₹{{ number_format($emp['earned_salary'] ?? 0, 2) }}</td>


                                  <td>₹{{ number_format($emp['total_balance'], 2) }}</td>

                                <!-- Attendance Modal -->
                                <td>
                                    @if (count($emp['attendances']))
                                        <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#attendanceModal{{ $index }}">
                                            View ({{ count($emp['attendances']) }})
                                        </button>

                                        <div class="modal fade" id="attendanceModal{{ $index }}" tabindex="-1" aria-labelledby="modalLabel{{ $index }}" aria-hidden="true">
                                            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="modalLabel{{ $index }}">Attendance - {{ $emp['name'] }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        @foreach ($emp['attendances'] as $att)
                                                            <div class="mb-2 border-bottom pb-2">
                                                                <strong>{{ $att->punch_in->format('d M, Y') }}</strong><br>
                                                                <small>
                                                                    In: {{ $att->punch_in->format('H:i') }},
                                                                    Out: {{ $att->punch_out ? $att->punch_out->format('H:i') : '—' }},
                                                                    Duration: {{ ucfirst($att->duration) }}
                                                                </small>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted">No records</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="11" class="text-center">No employees found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Flatpickr JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        flatpickr("#fromDate", { dateFormat: "Y-m-d" });
        flatpickr("#toDate", { dateFormat: "Y-m-d" });

        const filterSelect = document.querySelector('#filterSelect');
        const fromInput = document.querySelector('#fromDate');
        const toInput = document.querySelector('#toDate');

        filterSelect.addEventListener('change', function () {
            const isCustom = this.value === 'custom';
            fromInput.disabled = !isCustom;
            toInput.disabled = !isCustom;
        });
    });

    @if (session('success'))
        Swal.fire({ icon: 'success', title: 'Success', text: '{{ session('success') }}' });
    @endif
    @if (session('error'))
        Swal.fire({ icon: 'error', title: 'Error', text: '{{ session('error') }}' });
    @endif
</script>
@endsection
