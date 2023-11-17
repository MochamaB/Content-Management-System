<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class YourModel extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

     public function registerMediaConversions(Media $media = null): void
     {
         $this->addMediaConversion('thumb')
             ->width(368)
             ->height(232)
             ->sharpen(10);
     }
}
