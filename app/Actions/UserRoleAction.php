<?php

// App/Actions/UserRoleAction.php

namespace App\Actions;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Lorisleiva\Actions\Concerns\AsAction;

class UserRoleAction
{
    use AsAction;

    public function assignRole(User $user, string $role)
    {
        $role = Role::find($role);
        if ($role) {
            $user->assignRole($role);
        }
    }

    public function removeRole(User $user, Role $role)
    {
        $user->removeRole($role);
    }
}
