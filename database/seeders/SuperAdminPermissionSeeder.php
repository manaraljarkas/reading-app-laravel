<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Permission;

class SuperAdminPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdmins = User::role('super_admin')->get();

        if ($superAdmins->isEmpty()) {
            $this->command->error('No super admin users found!');
            return;
        }

        $allPermissions = Permission::all();

        foreach ($superAdmins as $superAdmin) {
            $superAdmin->syncPermissions($allPermissions);
            $this->command->info("All permissions assigned to {$superAdmin->email} successfully.");
        }
    }
}
