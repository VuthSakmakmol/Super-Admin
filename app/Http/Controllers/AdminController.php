<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    public function index()
    {
        // Retrieve all users except super admin
        $users = User::whereDoesntHave('roles', function ($query) {
            $query->where('name', 'super admin');
        })->get();

        // Pass the users and roles to the view
        $roles = Role::all();
        return view('admin.index', compact('users', 'roles'));
    }

    public function manageUsers(Request $request)
    {
        $user = User::find($request->user_id);
        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
        ]);

        $user->update($request->only(['name', 'email']));
        return redirect()->back()->with('success', 'User updated successfully.');
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->back()->with('success', 'User deleted successfully.');
    }

    public function changeRole(Request $request, $id)
{
    $request->validate([
        'role' => 'required|exists:roles,name',
    ]);

    // Prevent assigning the "super admin" role
    if ($request->role === 'super admin') {
        return redirect()->back()->with('error', 'You cannot assign the Super Admin role.');
    }

    $user = User::findOrFail($id);
    $user->syncRoles([$request->role]);

    return redirect()->back()->with('success', 'Role updated successfully.');
}

}
