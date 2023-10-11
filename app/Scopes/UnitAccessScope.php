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
        $userRole =$user->roles->pluck('name');
        if ($user->id !== 1) {
            // Get the IDs of units assigned to the logged in user
            $userUnits = $user->units;
            
            $unitIds = $userUnits->pluck('unit_id')->toArray();

            // Apply the filter to the query. Return units with id that has the same $unitIds that logged in user has
            $builder->whereIn('id', $unitIds);
         //   $builder->whereIn('id', $unitIds)
          //  ->orWhereIn('unit_id', $unitIds);
        }
    }
}
