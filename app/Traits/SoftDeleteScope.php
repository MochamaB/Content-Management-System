<?php

namespace App\Traits;

use Carbon\Carbon;

trait SoftDeleteScope
{
    public function scopeShowSoftDeleted($query)
    {
        $user = auth()->user();

        if ($user && $user->id === 1 || stripos($user->roles->first()->name, 'admin') !== false) {
            $query->withTrashed();
        } else {
            $query->withoutTrashed();
        }
    }


}