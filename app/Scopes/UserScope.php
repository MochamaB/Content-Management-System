<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class UserScope implements Scope
{
   
    
    public function apply(Builder $builder, Model $model)
    {
        $user = auth()->user();
    
        if ($user && $user->id !== 1 /*&& $userRole !== 'Administrator'*/) {
            $unitIds = $user->units->pluck('id')->toArray(); // Retrieve unit IDs outside of the query builder
    
            $builder->where('id', '<>', 1) // Exclude user with ID 1
                ->where('id', '<>', $user->id) // Exclude logged in user
                ->whereHas('units', function ($query) use ($unitIds) {
                    $query->whereIn('unit_id', $unitIds);
                });
        }
    }
    
}
