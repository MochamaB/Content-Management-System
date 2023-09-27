<?php
  
namespace App\Scopes;
  
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
  
class UserAccessScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        // Get the authenticated user
        $user = auth()->user();
    //    $userRole =$user->roles->pluck('name');

        if ($user  && $user->id) {
         // Get the IDs of units assigned to the logged-in user
        $unitIds = $user->units->pluck('id')->toArray();

        // Apply the scope to filter users who have the same units
        $builder->whereHas('units', function ($query) use ($unitIds) {
            $query->whereIn('unit_id', $unitIds);
        });
        }
    }
}
