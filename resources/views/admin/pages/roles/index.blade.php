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
                    <li class="breadcrumb-item active" aria-current="page">Roles List</li>
                </ol>
            </nav>
            <h2 class="h4">Roles List</h2>
            <p class="mb-0">Manage Roles</p>
        </div>
        <div class="btn-toolbar mb-2 mb-md-0">
            <button type="button" class="btn btn-sm btn-gray-800" data-bs-toggle="modal" data-bs-target="#roleModal">
                <svg class="icon icon-xs me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6">
                    </path>
                </svg>
                New Role
            </button>
            <div class="btn-group ms-2 ms-lg-3">
                <button type="button" class="btn btn-sm btn-outline-gray-600">Share</button>
                <button type="button" class="btn btn-sm btn-outline-gray-600">Export</button>
            </div>
        </div>
    </div>


    <div class="row mt-4">
        @forelse ($roles as $role)
            <div class="col-md-4 col-xl-4 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">{{ $role->role_name }}</h5>
                        <p class="card-text">
                            <strong>Slug:</strong> {{ $role->description ?? 'N/A' }}<br>
                            <strong>Created:</strong> {{ $role->created_at->diffForHumans() }}
                        </p>
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-sm btn-outline-primary me-2" data-bs-toggle="modal"
                                data-bs-target="#editRoleModal{{ $role->id }}">
                                Edit
                            </button>
                            <a href="#" class="btn btn-sm btn-outline-danger">Delete</a>
                        </div>

                        <!-- Edit Role Modal -->
                        <div class="modal fade" id="editRoleModal{{ $role->id }}" tabindex="-1"
                            aria-labelledby="editRoleModalLabel{{ $role->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-xl">
                                <div class="modal-content">
                                    <form action="{{ route('roles.update', $role->id) }}" method="POST">
                                        @csrf
                                        @method('PUT') <!-- Using PUT for update -->
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editRoleModalLabel{{ $role->id }}">Edit Role
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>

                                        <div class="modal-body">
                                            <div class="row">
                                                <!-- Left Column (Form) -->
                                                <div class="col-md-4">
                                                    <!-- Role Name -->
                                                    <div class="mb-3">
                                                        <label for="role_name" class="form-label">Role Name <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text"
                                                            class="form-control @error('role_name') is-invalid @enderror"
                                                            name="role_name" id="role_name"
                                                            value="{{ old('role_name', $role->role_name) }}" required>
                                                        @error('role_name')
                                                            <div class="text-danger mt-1">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <!-- Role Description -->
                                                    <div class="mb-3">
                                                        <label for="description" class="form-label">Description
                                                            (optional)</label>
                                                        <textarea class="form-control" name="description" id="description" rows="3"
                                                            placeholder="Enter description (optional)">{{ old('description', $role->description) }}</textarea>
                                                    </div>
                                                </div>

                                                <!-- Right Column (Permissions) -->
                                                <div class="col-md-8">
                                                    <div class="form-group">
                                                        <label class="form-label">Permissions</label>
                                                        <div class="permissions"
                                                            style="max-height: 300px; overflow-y: auto;">
                                                            <!-- Iterate over each permission group -->
                                                            @php
                                                                // Group permissions based on their prefix (e.g., User_Management)
                                                                $groupedPermissions = [];
                                                                foreach ($permissions as $permission) {
                                                                    $groupName = explode(
                                                                        '_',
                                                                        $permission->permission_title,
                                                                    )[0];
                                                                    if (!isset($groupedPermissions[$groupName])) {
                                                                        $groupedPermissions[$groupName] = [];
                                                                    }
                                                                    $groupedPermissions[$groupName][] = $permission;
                                                                }
                                                            @endphp

                                                            @foreach ($groupedPermissions as $groupName => $permissionsList)
                                                                <div class="permission-group mb-3">
                                                                    <h5>{{ ucwords(str_replace('_', ' ', $groupName)) }}
                                                                    </h5>

                                                                    <!-- Display Permissions in a Single Row -->
                                                                    <div class="d-flex">
                                                                        @foreach ($permissionsList as $permission)
                                                                            <div class="form-check me-3">
                                                                                <input class="form-check-input"
                                                                                    type="checkbox" name="permissions[]"
                                                                                    value="{{ $permission->id }}"
                                                                                    id="permission_{{ $permission->id }}"
                                                                                    {{ in_array($permission->id, $role->permissions->pluck('id')->toArray()) ? 'checked' : '' }}>
                                                                                <label class="form-check-label"
                                                                                    for="permission_{{ $permission->id }}">
                                                                                    {{ ucwords(str_replace('_', ' ', $permission->permission_title)) }}
                                                                                </label>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-light"
                                                data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary">Update Role</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">No roles found.</div>
            </div>
        @endforelse
    </div>

    <!-- Add Role Modal -->
    <div class="modal fade" id="roleModal" tabindex="-1" aria-labelledby="roleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <form action="{{ route('roles.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="roleModalLabel">Add New Role</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row">
                            <!-- Left Column (Form) -->
                            <div class="col-md-4">
                                <!-- Role Name -->
                                <div class="mb-3">
                                    <label for="role_name" class="form-label">Role Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('role_name') is-invalid @enderror"
                                        name="role_name" id="role_name" placeholder="Enter role name" required>
                                    @error('role_name')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Role Description -->
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description (optional)</label>
                                    <textarea class="form-control" name="description" id="description" rows="3"
                                        placeholder="Enter description (optional)"></textarea>
                                </div>
                            </div>

                            <!-- Right Column (Permissions) -->
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="form-label">Permissions</label>
                                    <div class="permissions" style="max-height: 300px; overflow-y: auto;">
                                        <!-- Iterate over each permission group -->
                                        @php
                                            // Group permissions based on their prefix (e.g., User_Management)
                                            $groupedPermissions = [];
                                            foreach ($permissions as $permission) {
                                                // Extract the base name (e.g., User_Management)
                                                $groupName = explode('_', $permission->permission_title)[0];

                                                // Add the permission to the grouped array
                                                if (!isset($groupedPermissions[$groupName])) {
                                                    $groupedPermissions[$groupName] = [];
                                                }
                                                $groupedPermissions[$groupName][] = $permission;
                                            }
                                        @endphp

                                        @foreach ($groupedPermissions as $groupName => $permissionsList)
                                            <div class="permission-group mb-3">
                                                <h5>{{ ucwords(str_replace('_', ' ', $groupName)) }}</h5>

                                                <!-- Display Permissions in a Single Row -->
                                                <div class="d-flex">
                                                    @foreach ($permissionsList as $permission)
                                                        <div class="form-check me-3">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="permissions[]" value="{{ $permission->id }}"
                                                                id="permission_{{ $permission->id }}">
                                                            <label class="form-check-label"
                                                                for="permission_{{ $permission->id }}">
                                                                {{ ucwords(str_replace('_', ' ', $permission->permission_title)) }}
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>



                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Role</button>
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


@endsection
