<?php
  
namespace App\Scopes;
  
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
  
class UserAccessScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
 
    $user = auth()->user();
    $userRole =$user->roles->pluck('name');

    if ($user->id !== 1 && $userRole !== 'Administrator') {
        // Filter units based on the logged-in user's unit_ids
        $builder->whereHas('unit', function ($query) use ($user) {
            $query->whereHas('users', function ($subQuery) use ($user) {
                $subQuery->where('user_id', $user->id);
            });
        });
   
    }
    }
}
