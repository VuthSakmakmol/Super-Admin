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

    public function manageUsers()
    {
        $users = User::with('roles')->get(); // Fetch all users with their roles
        $roles = Role::where('name', '!=', 'super admin')->get(); // Fetch all roles except 'super admin'

        return view('admin.users', compact('users', 'roles'));
    }


    public function viewReports()
    {
        // Logic for viewing reports
        return view('admin.reports');
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
        $user->delete(); // Delete user
        return back()->with('success', 'User deleted successfully.');
    }

    // Method to handle role changes
    public function changeRole(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Prevent assigning 'super admin'
        if (!in_array($request->role, ['user', 'admin'])) {
            return back()->with('error', 'Invalid role selected.');
        }

        // Assign the new role
        $user->syncRoles($request->role);

        return back()->with('success', 'Role updated successfully.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:user,admin', // Ensure only 'user' or 'admin' can be assigned
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        // Assign the selected role
        $user->assignRole($request->role);

        return redirect()->route('admin.users')->with('success', 'User created successfully.');
    }


}
