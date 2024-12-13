@extends('layouts.app')

@section('title', 'Super Admin Dashboard')

@push('styles')
    <link href="{{ asset('css/super-admin-index.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container">
        <h1>Super Admin Dashboard</h1>
        <p>Welcome, {{ auth()->user()->name }}! Manage roles and permissions here.</p>

        <!-- Total Users and Roles -->
        <div class="card mb-3">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">User Statistics</h4>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3 col-6 mb-3">
                        <div class="bg-light p-3 rounded shadow-sm">
                            <h5 class="text-primary">Total Users</h5>
                            <h4 class="fw-bold text-dark">{{ $users->count() }}</h4>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="bg-light p-3 rounded shadow-sm">
                            <h5 class="text-primary">Super Admins</h5>
                            <h4 class="fw-bold text-dark">{{ $superAdminCount }}</h4>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="bg-light p-3 rounded shadow-sm">
                            <h5 class="text-primary">Admins</h5>
                            <h4 class="fw-bold text-dark">{{ $adminCount }}</h4>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="bg-light p-3 rounded shadow-sm">
                            <h5 class="text-primary">Regular Users</h5>
                            <h4 class="fw-bold text-dark">{{ $userCount }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Anchor to Open the Activity Log Modal -->
    
        <a href="#" class="btn btn-secondary mb-3" data-bs-toggle="modal" data-bs-target="#activityLogModal">
            View Activity Log
        </a>
        <!-- Button to create a new user -->
        <a href="#" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createUserModal">
            Create New User
        </a>
       

        
            

        <!-- Activity Log Modal -->
        <div class="modal fade" id="activityLogModal" tabindex="-1" aria-labelledby="activityLogModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="activityLogModalLabel">Activity Log</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="table-dark">
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
                                        <td>
                                            {{ $activity->target_type }}
                                            @if($activity->target_id)
                                                (ID: {{ $activity->target_id }})
                                            @endif
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


        {{-- Create New User --}}
        <div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createUserModalLabel">Create New User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="{{ route('super-admin.store-user') }}">
                            @csrf
                            <!-- Name -->
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" name="name" id="name" class="form-control" required>
                            </div>
                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" name="email" id="email" class="form-control" required>
                            </div>
                            <!-- Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" name="password" id="password" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Create User</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        
        

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
                            <input type="text" name="name" class="form-control" placeholder="Search by Name" value="{{ request()->get('name') }}">
                        </div>

                        <!-- Filter by ID -->
                        <div class="col-md-2 mb-3">
                            <input type="text" name="id" class="form-control" placeholder="Search by ID" value="{{ request()->get('id') }}">
                        </div>

                        <!-- Filter by Role -->
                        <div class="col-md-2 mb-3">
                            <select name="role" class="form-select">
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
                            <select name="month" class="form-select">
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
                            <select name="year" class="form-select">
                                <option value="">Select Year</option>
                                @for ($year = now()->year; $year >= 2000; $year--)
                                    <option value="{{ $year }}" {{ request()->get('year') == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        <!-- Filter and Reset Icons -->
                        <div class="col-md-2 d-flex align-items-end">
                            <div>
                                <!-- Filter Button -->
                                <button type="submit" class="btn btn-primary mb-3">
                                    <i class="fas fa-filter"></i> <!-- Font Awesome Filter Icon -->
                                </button>

                                <!-- Reset Button -->
                                <a href="{{ route('super-admin.index') }}" class="btn btn-secondary mb-3">
                                    <i class="fas fa-redo-alt"></i> <!-- Font Awesome Reset Icon -->
                                </a>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>

        <div class="container">            
        
            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Current Role</th>
                            <th>Change Role</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                
                                <!-- Display Current Role -->
                                <td>
                                    {{ $user->roles->pluck('name')->first() ?? 'None' }}
                                </td>
                                
                                <!-- Conditional Dropdown for Changing Role -->
                                <td>
                                    @if($user->roles->pluck('name')->first() === 'super admin' && $user->id === auth()->id())
                                        <span class="text-muted">Cannot change own role</span>
                                    @elseif(auth()->user()->hasRole('super admin'))
                                        <form action="{{ route('admin.change-role', $user->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <select name="role" onchange="this.form.submit()" class="form-select form-select-sm">
                                                <option value="" disabled selected>Change Role</option>
                                                @foreach($roles as $role)
                                                    @if($role->name !== 'super admin') <!-- Exclude Super Admin -->
                                                        <option value="{{ $role->name }}" {{ $user->roles->pluck('name')->first() == $role->name ? 'selected' : '' }}>
                                                            {{ ucfirst($role->name) }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </form>
                                    @else
                                        <span class="text-muted">Not allowed</span>
                                    @endif
                                </td>
                                
                                <td>{{ $user->created_at->format('Y-m-d') }}</td>
                                
                                <!-- Actions -->
                                <td>
                                    <!-- Edit Icon -->
                                    <a href="#" class="text-warning me-3" title="Edit" style="font-size: 1.5rem; margin-left: 10px; margin-bottom: 4px;" data-bs-toggle="modal" data-bs-target="#editUserModal-{{ $user->id }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
            
                                    <!-- Delete Icon -->
                                    <form action="{{ route('super-admin.delete-user', $user->id) }}" method="POST" style="display:inline;" onsubmit="return confirmDelete(event);">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-link text-danger p-0" title="Delete" style="font-size: 1.5rem; margin-left: 10px; margin-bottom: 13px;">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
            
                            <!-- Edit User Modal -->
                            <div class="modal fade" id="editUserModal-{{ $user->id }}" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <form method="POST" action="{{ route('admin.update-user', $user->id) }}">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label for="name" class="form-label">Name</label>
                                                    <input type="text" class="form-control" name="name" value="{{ $user->name }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="email" class="form-label">Email</label>
                                                    <input type="email" class="form-control" name="email" value="{{ $user->email }}" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary">Save changes</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            
        </div>
        
                
    </div>
@endsection
