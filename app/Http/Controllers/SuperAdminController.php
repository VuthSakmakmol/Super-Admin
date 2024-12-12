<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class SuperAdminController extends Controller
{
    public function index()
    {
        $users = User::with('roles', 'permissions')->get();
        $roles = Role::all();
        return view('super-admin.index', compact('users', 'roles'));
    }

    public function managePermissions(Request $request){
        $role = Role::findByName($request->role);
        $role->syncPermissions($request->permissions);
        return back()->with('success', 'Permissions updated!');
    }

    // Manage users page
    public function manageUsers()
    {
        $users = User::all(); // Fetch all users
        $roles = Role::all(); // Fetch all roles
        return view('super-admin.users', compact('users', 'roles'));
    }

    // Create a new user
    public function createUser(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'role' => 'required|exists:roles,name',
        ]);

        if ($request->role === 'super admin') {
            // Check if there is already a Super Admin
            $existingSuperAdmin = User::role('super admin')->first();
            if ($existingSuperAdmin) {
                return redirect()->back()->with('error', 'There can only be one Super Admin.');
            }
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $user->assignRole($request->role);

        return redirect()->route('super-admin.index')->with('success', 'User created successfully.');
    }


    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->update($request->only('name', 'email'));

        return redirect()->route('super-admin.index')->with('success', 'User updated successfully.');
    }

    // Delete a user
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('super-admin.index')->with('success', 'User deleted successfully.');
    }

    // Assign a role to a user
    public function assignRole(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|exists:roles,name',
        ]);

        if ($request->role === 'super admin') {
            // Check if there is already a Super Admin
            $existingSuperAdmin = User::role('super admin')->first();
            if ($existingSuperAdmin && $existingSuperAdmin->id !== $id) {
                return redirect()->back()->with('error', 'There can only be one Super Admin.');
            }
        }

        $user = User::findOrFail($id);
        $user->syncRoles([$request->role]);

        return redirect()->back()->with('success', 'Role assigned successfully.');
    }

}
