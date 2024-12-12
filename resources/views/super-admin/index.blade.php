@extends('layouts.app')

@section('title', 'Super Admin Dashboard')
@push('styles')
    <link href="{{ asset('css/super-admin-index.css') }}" rel="stylesheet">
@endpush
@section('content')
    <div class="container">
        <h1>Super Admin Dashboard</h1>
        <p>Welcome, {{ auth()->user()->name }}! Manage roles and permissions here.</p>

        <!-- Button to create a new user -->
        <a href="#" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createUserModal">
            Create New User
        </a>

        <!-- Button to view users -->
        <a href="#" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#usersModal">
            View Users
        </a>

        <!-- Button to view activity log -->
        <a href="#" class="btn btn-secondary mb-3 float-end" data-bs-toggle="modal" data-bs-target="#activityLogModal">
            View Activity Log
        </a>

        <!-- Users Modal -->
        <div class="modal fade" id="usersModal" tabindex="-1" aria-labelledby="usersModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-fullscreen">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="usersModalLabel">Manage Users</h5>
                        <!-- Filter Section -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5>Filter Users</h5>
                            </div>
                            <div class="card-body">
                                <form method="GET" action="{{ route('super-admin.index') }}">
                                    <div class="row">
                                        <!-- Filter by Name -->
                                        <div class="col-md-2 mb-3">
                                            <label for="filter-name" class="form-label">Name</label>
                                            <input type="text" name="name" id="filter-name" class="form-control" placeholder="Name" value="{{ request()->get('name') }}">
                                        </div>

                                        <!-- Filter by ID -->
                                        <div class="col-md-2 mb-3">
                                            <label for="filter-id" class="form-label">ID</label>
                                            <input type="text" name="id" id="filter-id" class="form-control" placeholder="ID" value="{{ request()->get('id') }}">
                                        </div>

                                        <!-- Filter by Role -->
                                        <div class="col-md-2 mb-3">
                                            <label for="filter-role" class="form-label">Role</label>
                                            <select name="role" id="filter-role" class="form-select">
                                                <option value="">Select Role</option>
                                                @foreach($roles as $role)
                                                    <option value="{{ $role->name }}" {{ request()->get('role') == $role->name ? 'selected' : '' }}>
                                                        {{ ucfirst($role->name) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Filter by Month -->
                                        <div class="col-md-2 mb-3">
                                            <label for="filter-month" class="form-label">Month</label>
                                            <select name="month" id="filter-month" class="form-select">
                                                <option value="">Select Month</option>
                                                @for ($i = 1; $i <= 12; $i++)
                                                    <option value="{{ $i }}" {{ request()->get('month') == $i ? 'selected' : '' }}>
                                                        {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                                    </option>
                                                @endfor
                                            </select>
                                        </div>

                                        <!-- Filter by Year -->
                                        <div class="col-md-2 mb-3">
                                            <label for="filter-year" class="form-label">Year</label>
                                            <select name="year" id="filter-year" class="form-select">
                                                <option value="">Select Year</option>
                                                @for ($year = now()->year; $year >= 2000; $year--)
                                                    <option value="{{ $year }}" {{ request()->get('year') == $year ? 'selected' : '' }}>
                                                        {{ $year }}
                                                    </option>
                                                @endfor
                                            </select>
                                        </div>

                                        <!-- Filter Buttons -->
                                        <div class="col-md-2 d-flex align-items-end">
                                            <div>
                                                <button type="submit" class="btn btn-primary">Filter</button>
                                                <a href="{{ route('super-admin.index') }}" class="btn btn-secondary">Reset</a>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div> 
        
        
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Permissions</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                        <tr>
                                            <td>{{ $user->id }}</td>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ $user->roles->pluck('name')->first() ?? 'None' }}</td>
                                            <td>{{ implode(', ', $user->getAllPermissions()->pluck('name')->toArray()) }}</td>
                                            <td>
                                                <!-- Edit Button -->
                                                <a href="#" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editUserModal-{{ $user->id }}" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            
                                                <!-- Delete Button -->
                                                <form action="{{ route('super-admin.delete-user', $user->id) }}" method="POST" style="display:inline;" title="Delete">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            
                                                <!-- Assign Role -->
                                                <form action="{{ route('super-admin.assign-role', $user->id) }}" method="POST" style="display:inline;" title="Assign Role">
                                                    @csrf
                                                    <select name="role" onchange="this.form.submit()" class="form-select form-select-sm mt-2">
                                                        <option value="" disabled selected><i class="fas fa-user-tag"></i>Role</option>
                                                        @foreach($roles as $role)
                                                            @if($role->name !== 'super admin' || ($user->roles->first() && $user->roles->first()->name === 'super admin'))
                                                                <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </form>
                                            </td>                                            
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        

        <!-- Users Modal -->
        <div class="modal fade" id="usersModal" tabindex="-1" aria-labelledby="usersModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-fullscreen custom-fullscreen-modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="usersModalLabel">Manage Users</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Permissions</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td>{{ $user->id }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->roles->pluck('name')->first() ?? 'None' }}</td>
                                        <td>{{ implode(', ', $user->getAllPermissions()->pluck('name')->toArray()) }}</td>
                                        <td>
                                            <!-- Edit Button -->
                                            <a href="#" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editUserModal-{{ $user->id }}">Edit</a>

                                            <!-- Delete Button -->
                                            <form action="{{ route('super-admin.delete-user', $user->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                            </form>

                                            <!-- Assign Role -->
                                            <form action="{{ route('super-admin.assign-role', $user->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <select name="role" onchange="this.form.submit()" class="form-select form-select-sm">
                                                    <option value="" disabled selected>Assign Role</option>
                                                    @foreach($roles as $role)
                                                        @if($role->name !== 'super admin' || ($user->roles->first() && $user->roles->first()->name === 'super admin'))
                                                            <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>


        <!-- Activity Log Modal -->
        <div class="modal fade" id="activityLogModal" tabindex="-1" aria-labelledby="activityLogModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="activityLogModalLabel">Activity Log</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Timestamp</th>
                                    <th>Performed By</th>
                                    <th>Action</th>
                                    <th>Target</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($activities as $activity)
                                    <tr>
                                        <td>{{ $activity->created_at->format('Y-m-d H:i:s') }}</td>
                                        <td>{{ $activity->user->name ?? 'System' }}</td>
                                        <td>{{ $activity->action }}</td>
                                        <td>{{ $activity->target_type }} (ID: {{ $activity->target_id }})</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
