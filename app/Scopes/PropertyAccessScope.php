<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class PropertyAccessScope implements Scope
{

    public function apply(Builder $builder, Model $model)
    {
        $user = auth()->user();
     //   $userRole = $user->roles->pluck('name');
        if ($user && $user->id !== 1 /*&& stripos($userRole, 'admin') === false*/) {
            /// returns all units loggedinuser should access
            $userUnits = $user->units;
            //// returns all the property ids in pivot table that loggedinuser has
            $propertyIds = $userUnits->pluck('pivot.property_id')->unique();

            // Retrieve properties based on the whether the propertyids the loggedinuser has that
            $builder->whereIn('id', $propertyIds);
        }
    }

}
