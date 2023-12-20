<?php
  
namespace App\Scopes;
  
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
  
class UnitAccessScope implements Scope
{
    protected static $scopeDisabled = false;
    public function apply(Builder $builder, Model $model)
    {
        // Get the authenticated user
        $user = auth()->user();
        $userRole =$user->roles->pluck('name');
        if ($user->id !== 1 && $userRole !== 'Administrator') {
            // Filter units based on the logged-in user's unit_ids
            $builder->whereHas('users', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            });
       
        }
    }
    public static function disableScope()
    {
        static::$scopeDisabled = true;
    }

    public static function enableScope()
    {
        static::$scopeDisabled = false;
    }
}
