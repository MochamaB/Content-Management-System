<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Traits\MediaUpload;

class Website extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, MediaUpload;
    protected $table = 'website_settings';
    protected $fillable = [
            'company_name',
            'site_name',
            'company_telephone',
            'company_email',
            'company_location',
            'company_googlemaps',
            'company_aboutus',
            'site_currency',
            'banner_desc',
            
    ];

    ///Spatie Media conversions
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(150)
            ->height(150)
            ->sharpen(10);
    }
    public function registerMediaCollections(): void
    {
    $this->addMediaCollection('logos');
        //add options
    
        
    }

    public function getInitialsAttribute() {
        $words = explode(' ', $this->company_name);
        $initials = '';
        foreach ($words as $word) {
            $initials .= strtoupper($word[0]);  // Get the first letter of each word
        }
        return $initials;
    }

    
}
