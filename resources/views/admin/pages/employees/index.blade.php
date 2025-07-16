@extends('admin.layouts.app')
@section('title', 'Permissions')

@section('admincontent')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center py-4">
        <div class="d-block mb-4 mb-md-0">
            <nav aria-label="breadcrumb" class="d-none d-md-inline-block">
                <ol class="breadcrumb breadcrumb-dark breadcrumb-transparent">
                    <li class="breadcrumb-item">
                        <a href="#">
                            <svg class="icon icon-xxs" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                </path>
                            </svg>
                        </a>
                    </li>
                    <li class="breadcrumb-item"><a href="#">Admin</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Employees List</li>
                </ol>
            </nav>

            <h2 class="h4">Employees List</h2>
            <p class="mb-0">Manage Employees</p>

            <br>
            <form method="GET" action="{{ route('employees.index') }}" class="row g-2 align-items-center mb-3">
                <div class="col-md-4">
                    <input type="text" name="search" value="{{ $search ?? '' }}" class="form-control"
                        placeholder="Search by name or ID">
                </div>

                <div class="col-md-2">
                    <select name="staff_status" class="form-select" onchange="this.form.submit()">
                        <option value="all" {{ request('staff_status') == 'all' ? 'selected' : '' }}>All</option>
                        <option value="active" {{ request('staff_status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('staff_status') == 'inactive' ? 'selected' : '' }}>Inactive
                        </option>
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="per_page" class="form-select" onchange="this.form.submit()">
                        <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Apply</button>
                </div>

                <div class="col-md-2">
                    <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary w-100">Clear</a>
                </div>
            </form>



        </div>
        <div class="btn-toolbar mb-2 mb-md-0">


            <div class="btn-group me-2">
                <a href="{{ route('employees.create') }}" class="btn btn-sm btn-gray-800">Add Employee</a>
            </div>
            <div class="btn-group ms-2 ms-lg-3">
                <button type="button" class="btn btn-sm btn-outline-gray-600">Share</button>
                <button type="button" class="btn btn-sm btn-outline-gray-600">Export</button>
            </div>
        </div>
    </div>


    <div class="card border-0 shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-items-center table-nowrap mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th scope="col">#ID</th>
                            <th scope="col">Employee Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Phone</th>
                            <th scope="col">Gender</th>
                            <th scope="col">Department</th>
                            <th scope="col">Status</th>
                            <th scope="col">Photo</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $index => $employee)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $employee->employee_name }}</td>
                                <td>{{ $employee->employee_email }}</td>
                                <td>{{ $employee->employee_phone_number }}</td>
                                <td>{{ ucfirst($employee->gender) }}</td>
                                <td>{{ $employee->department ?? 'N/A' }}</td>
                                <td>
                                    @if ($employee->staff_status == 'active')
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($employee->photo)
                                        <img src="{{ asset('storage/' . $employee->photo) }}" alt="Profile"
                                            class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                    @else
                                        <span class="text-muted">No Photo</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('employees.edit', $employee->id) }}"
                                        class="btn btn-sm btn-outline-primary">Edit</a>

                                    <form id="status-form-{{ $employee->id }}"
                                        action="{{ route('employees.updatestatus', $employee->id) }}" method="POST"
                                        style="display:inline-block;">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="staff_status"
                                            value="{{ $employee->staff_status === 'active' ? 'inactive' : 'active' }}">

                                        <button type="button"
                                            class="btn btn-sm btn-outline-{{ $employee->staff_status === 'active' ? 'danger' : 'success' }}"
                                            onclick="confirmStatusChange({{ $employee->id }}, '{{ $employee->staff_status }}')">
                                            {{ $employee->staff_status === 'active' ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>
                                </td>


                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">No employees found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <br>
                <!-- Pagination Links -->
                <div class="container-fluid mt-4">
                    <div class="d-flex justify-content-center w-100">
                        <nav aria-label="Employee page navigation" class="w-100">
                            <ul class="pagination justify-content-end flex-wrap">
                                {{-- Previous --}}
                                @if ($employees->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link">Previous</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $employees->previousPageUrl() }}">Previous</a>
                                    </li>
                                @endif

                                {{-- Page Numbers --}}
                                @foreach ($employees->getUrlRange(1, $employees->lastPage()) as $page => $url)
                                    <li class="page-item {{ $employees->currentPage() === $page ? 'active' : '' }}">
                                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                    </li>
                                @endforeach

                                {{-- Next --}}
                                @if ($employees->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $employees->nextPageUrl() }}">Next</a>
                                    </li>
                                @else
                                    <li class="page-item disabled">
                                        <span class="page-link">Next</span>
                                    </li>
                                @endif
                            </ul>
                        </nav>
                    </div>
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



    <script>
        function confirmStatusChange(employeeId, currentStatus) {
            const nextStatus = currentStatus === 'active' ? 'deactivate' : 'activate';

            Swal.fire({
                title: `Are you sure you want to ${nextStatus} this employee?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: `Yes, ${nextStatus}!`
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`status-form-${employeeId}`).submit();
                }
            });
        }
    </script>


@endsection
