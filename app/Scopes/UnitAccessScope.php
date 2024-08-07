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
     //   $userRole =$user->roles->pluck('name');
        if ($user && $user->id !== 1 && stripos($user->roles->first()->name, 'admin') === false) {
            // Filter units based on the logged-in user's unit_ids
            $builder->whereHas('users', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            });
       
        }
    }
   
}
