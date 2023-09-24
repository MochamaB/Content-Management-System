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
     //   $user = auth()->user();

        $loggedInUser = auth()->user(); // Get the logged-in user

       
    }
}
