<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    
    public function run(): void
    {
        // Ensure the 'user' role exists
        if (!Role::where('name', 'user')->exists()) {
            Role::create(['name' => 'user']);
        }
        // Add seeders here, e.g.:
        $this->call([
            RolesAndPermissionsSeeder::class,
        ]);
    }
    

}
