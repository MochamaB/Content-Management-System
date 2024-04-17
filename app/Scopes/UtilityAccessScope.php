<?php
  
namespace App\Scopes;
  
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
  
class UtilityAccessScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $user = auth()->user();
        if ($user && $user->id !== 1 && stripos($user->roles->first()->name, 'admin') === false) {
            $userUnits = $user->units;
            $propertyIds = $userUnits->pluck('pivot.property_id')->unique();
    
            // Retrieve properties based on the extracted property_ids
            $builder->whereIn('property_id', $propertyIds);
       
        }

       
    }
}
