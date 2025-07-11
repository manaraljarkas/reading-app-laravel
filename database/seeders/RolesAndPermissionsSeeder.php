<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::firstOrCreate(['name' => 'reader'], ['guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'admin'],  ['guard_name' => 'web']);
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin'],  ['guard_name' => 'web']);

        $permissionsConfig = config('admin_permissions.permissions');

        if (!is_array($permissionsConfig)) {
            throw new \Exception("'admin_permissions.permissions' config is missing or invalid.");
        }

        foreach ($permissionsConfig as $model => $actions) {
            foreach ($actions as $action) {
                $permission = "$action $model";

                Permission::firstOrCreate([
                    'name' => $permission,
                ]);
            }
        }

        $allPermissions = Permission::all();
        $superAdmin->syncPermissions($allPermissions);
    }
}
