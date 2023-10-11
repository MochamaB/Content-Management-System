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
        $userRole =$user->roles->pluck('name');
        if ($user->id !== 1 && $userRole !== 'Administrator') {
            $userUnits = $user->units;
            $propertyIds = $userUnits->pluck('pivot.property_id')->unique();
            
            // Retrieve properties based on the extracted property_ids
            $builder->whereIn('id', $propertyIds);
       
        }

       
    }
}
