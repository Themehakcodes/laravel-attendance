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
                    <li class="breadcrumb-item active" aria-current="page">Permissions List</li>
                </ol>
            </nav>
            <h2 class="h4">Permissions List</h2>
            <p class="mb-0">Manage system-level permissions.</p>
        </div>
        <div class="btn-toolbar mb-2 mb-md-0">
            <button type="button" class="btn btn-sm btn-gray-800" data-bs-toggle="modal" data-bs-target="#permissionModal">
                <svg class="icon icon-xs me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6">
                    </path>
                </svg>
                New Permission
            </button>
            <div class="btn-group ms-2 ms-lg-3">
                <button type="button" class="btn btn-sm btn-outline-gray-600">Share</button>
                <button type="button" class="btn btn-sm btn-outline-gray-600">Export</button>
            </div>
        </div>
    </div>

    <div class="table-settings mb-4">
        <div class="row justify-content-between align-items-center">
            <div class="col-9 col-lg-8 d-md-flex">
                <div class="input-group me-2 me-lg-3 fmxw-300">
                    <span class="input-group-text">
                        <svg class="icon icon-xs" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                            aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                clip-rule="evenodd" />
                        </svg>
                    </span>
                    <input type="text" class="form-control" placeholder="Search permissions">
                </div>
                <select class="form-select fmxw-200 d-none d-md-inline" aria-label="Status filter">
                    <option selected>All</option>
                    <option value="1">Active</option>
                    <option value="2">Inactive</option>
                </select>
            </div>
        </div>
    </div>

    <div class="card card-body shadow border-0 table-wrapper table-responsive">
        <table class="table user-table table-hover align-items-center">
            <thead>
                <tr>
                    <th class="border-bottom">
                        <div class="form-check dashboard-check">
                            <input class="form-check-input" type="checkbox" id="permAllCheck">
                            <label class="form-check-label" for="permAllCheck"></label>
                        </div>
                    </th>
                    <th class="border-bottom">Permission Title</th>
                    <th class="border-bottom">Date Created</th>
                    <th class="border-bottom">Verified</th>
                    <th class="border-bottom">Status</th>
                    <th class="border-bottom">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($permissions as $permission)
                    <tr>
                        <td>
                            <div class="form-check dashboard-check">
                                <input class="form-check-input" type="checkbox" value="{{ $permission->id }}"
                                    id="permCheck{{ $permission->id }}">
                                <label class="form-check-label" for="permCheck{{ $permission->id }}"></label>
                            </div>
                        </td>
                        <td>{{ $permission->permission_title }}</td>
                        <td>{{ $permission->created_at->format('d M Y') }}</td>
                        <td>
                            <span class="badge bg-success">Yes</span>
                        </td>
                        <td>
                            <span class="fw-bold text-success">Active</span>
                        </td>
                        <td>
                            <div class="btn-group">
                                <!-- Edit Button - Triggers the Modal and Passes the Permission ID -->
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#editPermissionModal{{ $permission->id }}">
                                    Edit
                                </button>
                                <a href="#" class="btn btn-sm btn-danger">Delete</a>
                            </div>
                        </td>
                    </tr>
                @endforeach

            </tbody>
        </table>

        <div class="card-footer px-3 border-0 d-flex flex-column flex-lg-row align-items-center justify-content-between">

            <div class="fw-normal small mt-4 mt-lg-0">
                Showing <b>{{ $permissions->count() }}</b> of <b>{{ $permissions->total() }}</b> entries
            </div>
        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="permissionModal" tabindex="-1" aria-labelledby="permissionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="permissionModalLabel">Create New Permission</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('permissions.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <!-- Permission Title -->
                        <div class="mb-3">
                            <label for="permission_title" class="form-label">Permission Title</label>
                            <input type="text" class="form-control" id="permission_title" name="permission_title"
                                placeholder="Enter Permission Title" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Permission</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Edit Permission Modal (Dynamically Generated for Each Permission) -->
    @foreach ($permissions as $permission)
        <div class="modal fade" id="editPermissionModal{{ $permission->id }}" tabindex="-1"
            aria-labelledby="editPermissionModalLabel{{ $permission->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editPermissionModalLabel{{ $permission->id }}">Edit Permission</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('permissions.update', $permission->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <!-- Permission Title -->
                            <div class="mb-3">
                                <label for="edit_permission_title" class="form-label">Permission Title</label>
                                <input type="text" class="form-control"
                                    id="edit_permission_title{{ $permission->id }}" name="permission_title"
                                    value="{{ $permission->permission_title }}" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
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
    @endforeach

@endsection
