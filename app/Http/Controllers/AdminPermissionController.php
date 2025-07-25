<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\PermissionService;

class AdminPermissionController extends Controller
{
    protected $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    public function show(User $admin)
    {
        $permissions = $this->permissionService->getUserPermissionMap($admin);

        return response()->json([
            'message' => 'Permissions retrieved successfully.',
            'permissions' => $permissions,
        ]);
    }

    public function update(Request $request, User $admin)
    {
        $allPermissions = collect(config('admin_permissions.permissions'))
            ->flatMap(function ($actions, $model) {
                return collect($actions)->map(function ($action) use ($model) {
                    return "$action $model";
                });
            })->toArray();

        $validated = $request->validate([
            'permissions' => ['required', 'array'],
        ]);

        $requestedPermissions = $validated['permissions'];

        $validKeys = array_intersect(array_keys($requestedPermissions), $allPermissions);

        foreach ($validKeys as $permissionName) {
            $isEnabled = filter_var($requestedPermissions[$permissionName], FILTER_VALIDATE_BOOLEAN);

            if ($isEnabled) {
                $admin->givePermissionTo($permissionName);
            } else {
                $admin->revokePermissionTo($permissionName);
            }
        }

        return response()->json([
            'message' => 'Permissions updated successfully.',
        ]);
    }
}
