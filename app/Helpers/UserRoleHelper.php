<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Collection;
use Spatie\Permission\Models\Role;

class UserRoleHelper
{
    public static function getDistinctRolesFromUsers($users): Collection
    {
        return $users->flatMap(function ($user) {
            return $user->roles;
        })->unique('id')->values();
    }
}