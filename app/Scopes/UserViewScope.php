<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class UserViewScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $user = auth()->user();

        if ($user && $user->id !== 1 && stripos($user->roles->first()->name, 'admin') === false) {

            $loggedInUserRoles = $user->roles;
            $loggedInUserPermissions = $loggedInUserRoles->flatMap(function ($role) {
                return $role->permissions;
            });

            // Apply user access logic
         //   $builder->userAccess(); // Make sure this is spelled correctly (Access, not Acess)

            $builder->where('id', '!=', 1) // Exclude users with id 1
                ->with('roles.permissions')
                ->whereHas('roles', function ($roleQuery) use ($loggedInUserPermissions) {
                    $roleQuery->whereHas('permissions', function ($permissionQuery) use ($loggedInUserPermissions) {
                        $permissionQuery->groupBy('permissions.id')
                            ->havingRaw('COUNT(*) < ?', [$loggedInUserPermissions->count()]);
                    });
                });
        }
    }
}
