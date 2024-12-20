<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Traits\MediaUpload;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Image\Manipulations;

class Slider extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, MediaUpload, SoftDeletes;
    protected $table = 'sliders';
    protected $fillable = [
        'property_id',
        'slider_title',
        'slider_picture',
        'slider_desc',
      
            
    ];
    
    public static $fields = [
        'slider_title' => ['label' => 'TITLE', 'inputType' => 'text','required' =>true,'readonly' => ''],
        'slider_picture' => ['label' => 'Slider Picture', 'inputType' => 'picture','required' =>true, 'readonly' => ''],
        'slider_desc' => ['label' => 'Description', 'inputType' => 'textarea', 'required' =>true,'readonly' => ''],
        'slider_info' => ['label' => 'Information', 'inputType' => 'textarea', 'required' =>true,'readonly' => ''],
      
       
      
        // Add more fields as needed
    ];

       /////Filter options
       public static $filter = [
        'slider_title' => 'TITLE',
      
        // Add more filter fields as needed
    ];
    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }


    ///Spatie Media conversions
    public function registerMediaConversions(Media $media = null): void
    {
        // Original Image Conversion (High Quality)
        $this->addMediaConversion('original')
            ->fit(Manipulations::FIT_MAX, 3500, 2500)
            ->quality(100)
            ->withResponsiveImages();

        // Slider Display Conversion
        $this->addMediaConversion('slider')
            ->fit(Manipulations::FIT_CROP, 1500, 700)
            ->optimize()
            ->quality(95)
            ->withResponsiveImages();

        // Thumbnail Conversion
        $this->addMediaConversion('thumb')
            ->fit(Manipulations::FIT_CROP, 300, 150)
            ->optimize()
            ->quality(75);
    }

    public function registerMediaCollections(): void
    {
    $this->addMediaCollection('slider');
        //add options
    
        
    }

}
