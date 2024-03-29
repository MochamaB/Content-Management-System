<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\MediaCollections\Models\Media as BaseMedia;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Media extends BaseMedia implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    public function units()
    {
        return $this->belongsTo(Unit::class ,'unit_id');
    }

    public function modeltype()
    {
        return $this->morphTo();
    }
}
