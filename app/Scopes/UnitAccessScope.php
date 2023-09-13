<?php
  
namespace App\Scopes;
  
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
  
class UnitAccessScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        // Get the authenticated user
        $user = auth()->user();

        if ($user  && $user->id !== 1) {
            // Get the IDs of units assigned to the user
            $unitIds = $user->units->pluck('id')->toArray();

            // Apply the filter to the query
            $builder->whereIn('unit_id', $unitIds);
        }
    }
}
