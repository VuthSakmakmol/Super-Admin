@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Manage Users</h1>
    <table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
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
                <td>
                    @if($user->roles->pluck('name')->first() !== 'super admin')
                        <!-- Change Role -->
                        <form action="{{ route('admin.change-role', $user->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <select name="role" onchange="this.form.submit()" class="form-select form-select-sm">
                                <option value="" disabled selected>Change Role</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                                @endforeach
                            </select>
                        </form>
                    @else
                        <span class="text-muted">Role cannot be changed</span>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

</div>
@endsection
