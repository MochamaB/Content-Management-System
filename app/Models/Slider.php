<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Traits\MediaUpload;
use Illuminate\Database\Eloquent\SoftDeletes;


class Slider extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, MediaUpload, SoftDeletes;
    protected $table = 'sliders';
    protected $fillable = [
        'slider_title',
        'slider_picture',
        'slider_desc',
        'slider_info',
      
            
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

    public static function getFieldData($field)
    {
    switch ($field) {
        case 'slider_title':

            return Slider::pluck('slider_title')->toArray();
        case 'property_manager':
            return  ['Notset', 'Set'];
        case 'property_status':
            return  ['Active', 'InActive'];
        // Add more cases for additional filter fields
        default:
            return [];
    }
    }

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
    $this->addMediaCollection('slider');
        //add options
    
        
    }

}
