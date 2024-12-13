@extends('layouts.app')

@section('title', 'User Dashboard')

@section('content')
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
@endsection
