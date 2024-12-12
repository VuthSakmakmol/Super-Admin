<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Activity;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SuperAdminController extends Controller
{
//     public function index()
// {
//     $users = User::all(); // Fetch all users
//     $roles = Role::all(); // Fetch all roles
//     $activities = Activity::with('user')->latest()->get(); // Fetch all activities

//     return view('super-admin.index', compact('users', 'roles', 'activities'));
// }
public function index(Request $request)
{
    // Create the base query for users
    $query = User::query();

    // Apply filters
    if ($request->filled('name')) {
        $query->where('name', 'like', '%' . $request->name . '%');
    }

    if ($request->filled('id')) {
        $query->where('id', $request->id);
    }

    if ($request->filled('role')) {
        $query->whereHas('roles', function ($q) use ($request) {
            $q->where('name', $request->role);
        });
    }

    if ($request->filled('month')) {
        $query->whereMonth('created_at', $request->month);
    }

    if ($request->filled('year')) {
        $query->whereYear('created_at', $request->year);
    }

    // Fetch filtered users
    $users = $query->with('roles')->get();

    // Fetch all roles
    $roles = Role::all();

    // Fetch activity logs
    $activities = Activity::latest()->get(); // Replace `Activity` with your actual model name

    // Pass data to the view
    return view('super-admin.index', compact('users', 'roles', 'activities'));
}





    public function managePermissions(Request $request)
    {
        $role = Role::findByName($request->role);
        $role->syncPermissions($request->permissions);

        return response()->json(['success' => 'Permissions updated!']);
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

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $user->assignRole($request->role);

        // Log the activity
        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'Created user',
            'target_type' => 'User',
            'target_id' => $user->id,
        ]);

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

        // Log the activity
        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'Deleted user',
            'target_type' => 'User',
            'target_id' => $id,
        ]);

        return redirect()->route('super-admin.index')->with('success', 'User deleted successfully.');
    }

    // Assign a role to a user
    public function assignRole(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|exists:roles,name',
        ]);

        $user = User::findOrFail($id);
        $user->syncRoles([$request->role]);

        // Log the activity
        Activity::create([
            'user_id' => auth()->id(),
            'action' => "Assigned role {$request->role} to user",
            'target_type' => 'User',
            'target_id' => $user->id,
        ]);

        return redirect()->route('super-admin.index')->with('success', 'Role assigned successfully.');
    }

}
