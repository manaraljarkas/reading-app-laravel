<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Role::firstOrCreate(['name' => 'reader']);
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin = Role::firstOrCreate(['name' => 'super_admin']);
    }

    
}
