<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions only if they don't already exist
        if (!Permission::where('name', 'manage users')->exists()) {
            Permission::create(['name' => 'manage users']);
        }
        if (!Permission::where('name', 'view dashboard')->exists()) {
            Permission::create(['name' => 'view dashboard']);
        }
        if (!Permission::where('name', 'edit articles')->exists()) {
            Permission::create(['name' => 'edit articles']);
        }

        // Create roles only if they don't already exist
        if (!Role::where('name', 'super admin')->exists()) {
            $superAdminRole = Role::create(['name' => 'super admin']);
            $superAdminRole->givePermissionTo(Permission::all());
        }

        if (!Role::where('name', 'admin')->exists()) {
            $adminRole = Role::create(['name' => 'admin']);
            $adminRole->givePermissionTo(['manage users', 'view dashboard']);
        }

        if (!Role::where('name', 'user')->exists()) {
            $userRole = Role::create(['name' => 'user']);
            $userRole->givePermissionTo('view dashboard');
        }

        // Create a default super admin user if it doesn't exist
        if (!User::where('email', 'superadmin@example.com')->exists()) {
            $superAdmin = User::create([
                'name' => 'Super Admin',
                'email' => 'superadmin@example.com',
                'password' => bcrypt('12345678'), // Change to a secure password
            ]);
            $superAdmin->assignRole('super admin');
        }
    }
}

