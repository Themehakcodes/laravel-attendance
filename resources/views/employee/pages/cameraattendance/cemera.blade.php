@extends('employee.layouts.cemera')
@section('title', 'Dashboard')

@section('admincontent')
<div class="py-4 d-flex flex-column align-items-center" style="height: 100vh; background-color: #f8f9fa;">
    <div class="camera-container" style="flex-grow: 1; display: flex; justify-content: center; align-items: center; width: 100%;">
        <video id="camera" autoplay playsinline style="width: 100%; height: 100%; max-width: 100%; max-height: 100%; border: 1px solid #ccc; border-radius: 10px; object-fit: cover;"></video>
        <canvas id="snapshotCanvas" style="display: none;"></canvas>
    </div>

    <form method="POST" action="{{ route('employee.attendance.punchIn') }}">
        @csrf
        <input type="hidden" name="attendance_location" id="attendance_location">
        <input type="hidden" name="in_photo" id="in_photo_data">
        <input type="hidden" name="punch_in_time" id="punch_in_time" value="{{ now() }}">
        <div class="d-flex mt-3">
            <button type="submit"  class="btn btn-primary d-flex align-items-center justify-content-center"
                style="border-radius: 50%; width: 70px; height: 70px; padding: 0; font-size: 24px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); margin-right: 10px;">
                <i class="fas fa-camera"></i>
            </button>
        </div>
    </form>
</div>

<!-- SweetAlert CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        @if ($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                html: '{!! implode('<br>', $errors->all()) !!}',
            });
        @endif
    });

    @if (session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: '{{ session('success') }}',
        });
    @endif
</script>

@endsection
