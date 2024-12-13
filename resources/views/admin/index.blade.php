@extends('layouts.app')

@section('title', 'Admin Dashboard')
@push('styles')
    <link href="{{ asset('css/super-admin-index.css') }}" rel="stylesheet">
@endpush
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">Welcome to Your Dashboard</h3>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">Hello, <strong>{{ auth()->user()->name }}</strong>!</p>
                        <p class="mb-0">You are logged in as a <strong>{{ auth()->user()->roles->pluck('name')->first() ?? 'User' }}</strong>.</p>
                        <p class="text-muted mt-2">
                            Use the navigation menu to access the features available to your role.
                        </p>
                    </div>
                </div>
    
                <!-- Role-Specific Actions -->
                @if(auth()->user()->hasRole('super admin'))
                <!-- Super Admin Actions -->
                <div class="card mb-4">
                    <div class="card-header bg-dark text-white">
                        <h4>Super Admin Panel</h4>
                    </div>
                    <div class="card-body">
                        <a href="{{ route('super-admin.index') }}" class="btn btn-primary">Manage Users</a>
                        <a href="{{ route('super-admin.permissions') }}" class="btn btn-secondary">Manage Permissions</a>
                        <a href="{{ route('super-admin.activity-log') }}" class="btn btn-info">View Activity Log</a>
                    </div>
                </div>
                @endif
    
                @if(auth()->user()->hasRole('admin'))
                    <!-- Admin Actions -->
                    <div class="card mb-4">
                        <div class="card-header bg-dark text-white">
                            <h4>Admin Panel</h4>
                        </div>
                        <div class="card-body">
                            <!-- Manage Users Button -->
                            <a href="{{ route('admin.users') }}" class="btn btn-primary mb-2">
                                <i class="fas fa-users"></i> Manage Users
                            </a>
    
                            <!-- View Reports Button -->
                            <a href="{{ route('reports') }}" class="btn btn-secondary mb-2">
                                <i class="fas fa-file-alt"></i> View Reports
                            </a>
                        </div>
                    </div>
                @endif
    
    
                @if(auth()->user()->hasRole('user'))
                <!-- User Actions -->
                <div class="card mb-4">
                    <div class="card-header bg-dark text-white">
                        <h4>User Dashboard</h4>
                    </div>
                    <div class="card-body">
                        <p>Welcome, {{ auth()->user()->name }}! Explore the features available to you.</p>
                        <a href="{{ route('user.profile') }}" class="btn btn-primary">View Profile</a>
                        <a href="{{ route('user.settings') }}" class="btn btn-secondary">Account Settings</a>
                    </div>
                </div>
                @endif
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        
    </div>
@endsection
