<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\MediaCollections\MediaScopeInterface;

class MediaAccessScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {

        $user = auth()->user();

        if ($user && $user->id !== 1 /*&& $userRole !== 'Administrator'*/) {
            // Filter units based on the logged-in user's unit_ids
            $builder->where('model_type', 'App\Models\WebsiteSetting');
        }
    }
}
