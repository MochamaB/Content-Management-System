<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    use HasFactory;
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

}
