<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Activity;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SuperAdminController extends Controller
{



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

        // Count roles
        $superAdminCount = User::whereHas('roles', function ($query) {
            $query->where('name', 'super admin');
        })->count();

        $adminCount = User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })->count();

        $userCount = User::whereHas('roles', function ($query) {
            $query->where('name', 'user');
        })->count();

        // Fetch filtered users with roles
        $users = $query->with('roles')->get();

        // Fetch all roles
        $roles = Role::all();

        // Fetch all permissions
        $permissions = Permission::all();

        // Fetch activity logs
        $activities = Activity::latest()->get(); // Replace `Activity` with your actual model name

        // Pass data to the view
        return view('super-admin.index', compact(
            'users',
            'roles',
            'permissions',
            'activities',
            'superAdminCount',
            'adminCount',
            'userCount'
        ));
    }



    public function managePermissions()
    {
        $roles = Role::all(); // Fetch all roles
        $permissions = Permission::all(); // Fetch all permissions

        return view('super-admin.permissions', compact('roles', 'permissions'));
    }

    

    public function updatePermissions(Request $request, Role $role)
    {
        // Validate the incoming data
        $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        // Sync permissions
        $role->syncPermissions($request->permissions);

        // Get updated permissions
        $updatedPermissions = $role->permissions->pluck('name')->toArray();

        return response()->json([
            'success' => true,
            'updated_permissions' => $updatedPermissions,
        ]);
    }




    // Manage users page
    public function manageUsers()
    {
        $users = User::all(); // Fetch all users
        $roles = Role::all(); // Fetch all roles
        return view('super-admin.users', compact('users', 'roles'));
    }

    //Activity log
    public function activityLog()
    {
        $activities = Activity::latest()->get(); // Replace `Activity` with your actual model for activity logs

        return view('super-admin.activity-log', compact('activities'));
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

    public function assignDefaultRoles()
    {
        $usersWithoutRole = User::doesntHave('roles')->get();

        foreach ($usersWithoutRole as $user) {
            $user->assignRole('user');
        }

        return back()->with('success', 'Roles assigned to users without roles.');
    }


        public function storeUser(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        // Create the user
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => bcrypt($validatedData['password']),
        ]);

        return redirect()->back()->with('success', 'User created successfully.');
    }

    public function run()
    {
        // Create Super Admin Role
        $superAdminRole = Role::firstOrCreate(['name' => 'super admin']);

        // Assign Super Admin Role to a specific user
        $user = User::find(1); // Replace with the Super Admin user ID
        if ($user) {
            $user->assignRole($superAdminRole);
        }
    }

        
        public function changeRole(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Prevent assigning the 'super admin' role
        if ($request->role === 'super admin') {
            return back()->with('error', 'Cannot assign Super Admin role.');
        }

        // Assign the new role
        $user->syncRoles($request->role);

        return back()->with('success', 'Role updated successfully.');
    }

    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Create user
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => bcrypt($validatedData['password']),
        ]);

        // Assign default role
        $user->assignRole('user');

        return redirect()->route('login')->with('success', 'Registration successful!');
    }




}
