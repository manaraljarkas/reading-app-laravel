<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

class PermissionService
{
    public function getUserPermissionMap(User $user)
    {
        $allPermissions = Cache::remember('all_permissions', 3600, function () {
            return collect(config('admin_permissions.permissions'))
                ->flatMap(function ($actions, $model) {
                    return collect($actions)->map(function ($action) use ($model) {
                        return "$action $model";
                    });
                });
        });

        $userPermissions = $user->getPermissionNames();

        return $allPermissions->mapWithKeys(function ($permission) use ($userPermissions) {
            return [$permission => $userPermissions->contains($permission)];
        });
    }
}
