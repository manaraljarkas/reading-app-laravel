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
        $superAdmin = User::where('email', 'superadmin@test.com')->first();

        if (!$superAdmin) {
            $this->command->error('Super admin user not found!');
            return;
        }

        $allPermissions = Permission::all();

        $superAdmin->syncPermissions($allPermissions);

        $this->command->info('All permissions assigned to super admin successfully.');

    }
}
