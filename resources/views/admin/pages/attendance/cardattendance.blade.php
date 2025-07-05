@extends('admin.layouts.attendance')
@section('title', 'Attendance Overview')

@section('admincontent')
<div class="container-fluid">

<div class="mb-3">
    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> Back to Admin Panel
    </a>
</div>


    <!-- Attendance Barcode Form -->
    <form id="barcode-form" action="{{ route('attendance.mark.cards') }}" method="POST" class="mb-4">
        @csrf
        <input
            type="text"
            name="barcode"
            id="barcode-input"
            placeholder="Scan or enter barcode..."
            autofocus
            class="form-control form-control-lg border border-primary shadow-sm w-100 max-w-md"
        />
    </form>

@if(session('employee'))
    <div class="card p-3 shadow-sm border-left-success mb-3">
        <div class="d-flex align-items-center">
            <img src="{{ asset('uploads/employee/' . session('employee')->photo) }}" alt="Photo" width="70" height="70"
                class="rounded-circle me-3 border shadow-sm object-cover"
                onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode(session('employee')->employee_name) }}&background=0D8ABC&color=fff&size=60';" />
            <div>
                <h5 class="mb-0">{{ session('employee')->employee_name }}</h5>
                <p class="mb-1 text-muted">{{ session('employee')->job_title }}</p>
                <small class="d-block text-success">Punch In: {{ session('time') }}</small>
                
                @if(session('punch_out'))
                    <small class="d-block text-primary">Punch Out: {{ session('punch_out') }}</small>
                @endif

                @if(session('duration'))
                    <small class="d-block text-dark">Marked As: <strong>{{ strtoupper(session('duration')) }}</strong></small>
                @endif
            </div>
        </div>
    </div>
@endif


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

    // Auto-submit form on Enter key
    document.addEventListener("DOMContentLoaded", function () {
        const input = document.getElementById('barcode-input');
        input.focus();

        input.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                if (input.value.trim() !== '') {
                    document.getElementById('barcode-form').submit();
                }
            }
        });
    });
</script>
@endsection
