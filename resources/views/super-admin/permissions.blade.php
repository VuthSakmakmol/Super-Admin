@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Manage Permissions</h1>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Role</th>
                <th>Permissions</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($roles as $role)
            <tr>
                <td>{{ $role->name }}</td>
                <td>
                    {{ $role->permissions->pluck('name')->join(', ') }}
                </td>
                <td>
                    <!-- Edit Permissions Button -->
                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editPermissionsModal-{{ $role->id }}">
                        Edit Permissions
                    </button>
                </td>
            </tr>

            <!-- Modal for Editing Permissions -->
            <div class="modal fade" id="editPermissionsModal-{{ $role->id }}" tabindex="-1" aria-labelledby="editPermissionsModalLabel-{{ $role->id }}" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="{{ route('super-admin.update-permissions', $role->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-header">
                                <h5 class="modal-title" id="editPermissionsModalLabel-{{ $role->id }}">Edit Permissions for {{ $role->name }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                @foreach($permissions as $permission)
                                <div class="form-check">
                                    <input
                                        type="checkbox"
                                        name="permissions[]"
                                        value="{{ $permission->name }}"
                                        class="form-check-input"
                                        id="permission-{{ $role->id }}-{{ $permission->id }}"
                                        {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}
                                    >
                                    <label class="form-check-label" for="permission-{{ $role->id }}-{{ $permission->id }}">
                                        {{ ucfirst($permission->name) }}
                                    </label>
                                </div>
                                @endforeach
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Include the JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const forms = document.querySelectorAll('form[action*="update-permissions"]');

    forms.forEach(form => {
        form.addEventListener('submit', function (event) {
            event.preventDefault();

            const formData = new FormData(this);

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Permissions updated successfully!');
                } else {
                    alert('An error occurred while updating permissions.');
                }
                location.reload(); // Reload page to reflect changes
            })
            .catch(error => console.error('Error:', error));
        });
    });
});
</script>
@endsection
