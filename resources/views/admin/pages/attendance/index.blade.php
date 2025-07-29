@extends('admin.layouts.app')
@section('title', 'Attendance Overview')

@section('admincontent')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center py-3">
            <h2 class="mb-0">🗓️ Attendance for <strong>{{ $today }}</strong> ({{ $dayName }})</h2>
            <form method="GET" action="{{ route('attendance.calendar') }}">
                <input type="date" name="date" class="form-control" value="{{ request('date', $today) }}"
                    onchange="this.form.submit()">
            </form>
        </div>

        <div class="card shadow-sm mt-4">
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>👤 Employee Name</th>
                                <th>📋 Status</th>
                                <th>✅ Mark</th>
                                <th>Timing</th>
                                <th>🔁 Punch</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($employees as $index => $emp)
                                <tr>
                                    <td>{{ $emp['name'] }}</td>

                                    <td>
                                        @if ($emp['attendance_marked'])
                                            @php
                                                $duration = $emp['attendance_details']?->duration;
                                                $label = match ($duration) {
                                                    'full_time' => ['P', 'success', 'Present'],
                                                    'half_time' => ['H', 'warning', 'Half Time'],
                                                    'absent' => ['A', 'danger', 'Absent'],
                                                    default => ['?', 'secondary', 'Unknown'],
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $label[1] }}"
                                                title="{{ $label[2] }}">{{ $label[0] }}</span>
                                        @else
                                            <span class="badge bg-danger">❌ Not Marked</span>
                                        @endif
                                    </td>

                                    <td>
                                        <form action="{{ route('attendance.mark') }}" method="POST" class="d-flex gap-1">
                                            @csrf
                                            <input type="hidden" name="user_id" value="{{ $emp['user_id'] }}">
                                            <input type="hidden" name="employee_profile_id"
                                                value="{{ $emp['employee_profile']->id }}">
                                            <input type="hidden" name="date" value="{{ $today }}">

                                            <button name="duration" value="full_time" class="btn btn-success btn-sm"
                                                title="Present" @if ($emp['attendance_details']?->duration === 'full_time') disabled @endif>P</button>
                                            <button name="duration" value="half_time" class="btn btn-warning btn-sm"
                                                title="Half Time"
                                                @if ($emp['attendance_details']?->duration === 'half_time') disabled @endif>H</button>
                                            <button name="duration" value="absent" class="btn btn-danger btn-sm"
                                                title="Absent" @if ($emp['attendance_details']?->duration === 'absent') disabled @endif>A</button>
                                        </form>
                                    </td>

                                    <td>
                                        @if ($emp['punch_in'])
                                            <span class="badge bg-success me-1">
                                                <i class="bi bi-box-arrow-in-right"></i>
                                                {{ \Carbon\Carbon::parse($emp['punch_in'])->format('h:i A') }}
                                            </span>
                                        @else
                                            <span class="badge bg-danger me-1">
                                                <i class="bi bi-x-circle"></i> No In
                                            </span>
                                        @endif

                                        @if ($emp['punch_out'])
                                            <span class="badge bg-primary">
                                                <i class="bi bi-box-arrow-left"></i>
                                                {{ \Carbon\Carbon::parse($emp['punch_out'])->format('h:i A') }}
                                            </span>
                                        @else
                                            <span class="badge bg-warning text-dark">
                                                <i class="bi bi-hourglass-split"></i> No Out
                                            </span>
                                        @endif
                                    </td>

                                    <td>
                                        <!-- Punch In Button -->
                                        <button class="btn btn-outline-success btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#punchInModal{{ $index }}">
                                            IN
                                        </button>

                                        <!-- Punch Out Button -->
                                        <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#punchOutModal{{ $index }}">
                                            OUT
                                        </button>
                                    </td>
                                </tr>

                              <!-- Punch In Modal -->
<div class="modal fade" id="punchInModal{{ $index }}" tabindex="-1" aria-labelledby="punchInLabel{{ $index }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('attendance.punchin') }}">
            @csrf
            <input type="hidden" name="user_id" value="{{ $emp['user_id'] }}">
            <input type="hidden" name="employee_profile_id" value="{{ $emp['employee_profile']->id }}">
            <input type="hidden" name="date" value="{{ request('date', $today) }}">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="punchInLabel{{ $index }}">Punch In - {{ $emp['name'] }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="punch_in_time{{ $index }}" class="form-label"><strong>Punch In Time:</strong></label>
                        <input type="time" class="form-control" name="time" id="punch_in_time{{ $index }}" required value="{{ now()->format('H:i') }}">
                    </div>
                    <p><strong>Email:</strong> {{ $emp['employee_profile']->employee_email }}</p>
                    <p><strong>Phone:</strong> {{ $emp['employee_profile']->employee_phone_number }}</p>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Confirm In</button>
                </div>
            </div>
        </form>
    </div>
</div>



                                <!-- Punch Out Modal -->
                               <!-- Punch Out Modal -->
<div class="modal fade" id="punchOutModal{{ $index }}" tabindex="-1" aria-labelledby="punchOutLabel{{ $index }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('attendance.punchout') }}">
            @csrf
            <input type="hidden" name="user_id" value="{{ $emp['user_id'] }}">
            <input type="hidden" name="employee_profile_id" value="{{ $emp['employee_profile']->id }}">
            <input type="hidden" name="date" value="{{ request('date', $today) }}">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="punchOutLabel{{ $index }}">Punch Out - {{ $emp['name'] }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="punch_out_time{{ $index }}" class="form-label"><strong>Punch Out Time:</strong></label>
                        <input type="time" class="form-control" name="time" id="punch_out_time{{ $index }}" required value="{{ now()->format('H:i') }}">
                    </div>
                    <p><strong>Email:</strong> {{ $emp['employee_profile']->employee_email }}</p>
                    <p><strong>Phone:</strong> {{ $emp['employee_profile']->employee_phone_number }}</p>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger">Confirm Out</button>
                </div>
            </div>
        </form>
    </div>
</div>



                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">No employee data available.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
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
