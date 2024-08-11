<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TicketAccessScope implements Scope
{
   public function apply(Builder $builder, Model $model)
    {
        $user = auth()->user();

        if (!$user || $user->id === 1 || stripos($user->roles->first()->name, 'admin') !== false) {
            // Admin or user with 'admin' in role name can see all tickets
            return;
        }

        $builder->where(function ($query) use ($user) {
            $query->whereHas('unit', function ($subQuery) use ($user) {
                $subQuery->whereHas('users', function ($unitQuery) use ($user) {
                    $unitQuery->where('user_id', $user->id);
                });
            })
            ->orWhere('user_id', $user->id)
            ->orWhere(function ($subQuery) use ($user) {
                $subQuery->where('assigned_type', 'App\Models\User')
                         ->where('assigned_id', $user->id);
            });
        });
    }
}
