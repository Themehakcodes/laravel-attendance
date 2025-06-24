@extends('admin.layouts.app')
@section('title', 'Attendance Overview')

@section('admincontent')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center py-3">
        <h2 class="mb-0">🗓️ Attendance for <strong>{{ $today }}</strong> ({{ $dayName }})</h2>
        <form method="GET" action="{{ route('attendance.calendar') }}">
            <input type="date" name="date" class="form-control" value="{{ request('date', $today) }}" onchange="this.form.submit()">
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
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($employees as $emp)
                            <tr>
                                <td>{{ $emp['name'] }}</td>
                               
                                <td>
    @if ($emp['attendance_marked'])
        @php
            $duration = $emp['attendance_details']?->duration;
            $label = match($duration) {
                'full_time' => ['P', 'success', 'Present'],
                'half_time' => ['H', 'warning', 'Half Time'],
                'leave' => ['A', 'danger', 'Absent'],
                default => ['?', 'secondary', 'Unknown'],
            };
        @endphp
        <span class="badge bg-{{ $label[1] }}" title="{{ $label[2] }}">{{ $label[0] }}</span>
    @else
        <span class="badge bg-danger">❌ Not Marked</span>
    @endif
</td>

                              
                                <td>
                                    <form action="{{ route('attendance.mark') }}" method="POST" class="d-flex gap-1">
                                        @csrf
                                        <input type="hidden" name="user_id" value="{{ $emp['user_id'] }}">
                                        <input type="hidden" name="employee_profile_id" value="{{ $emp['employee_profile']->id }}">
                                        <input type="hidden" name="date" value="{{ $today }}">

                                        <button 
                                            name="duration" 
                                            value="full_time" 
                                            class="btn btn-success btn-sm" 
                                            title="Present"
                                            @if($emp['attendance_details']?->duration === 'full_time') disabled @endif
                                        >P</button>
                                        <button 
                                            name="duration" 
                                            value="half_time" 
                                            class="btn btn-warning btn-sm" 
                                            title="Half Time"
                                            @if($emp['attendance_details']?->duration === 'half_time') disabled @endif
                                        >H</button>
                                        <button 
                                            name="duration" 
                                            value="leave" 
                                            class="btn btn-danger btn-sm" 
                                            title="Absent"
                                            @if($emp['attendance_details']?->duration === 'leave') disabled @endif
                                        >A</button>
                                    </form>
                                </td>
                            </tr>
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
