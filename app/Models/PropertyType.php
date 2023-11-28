<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyType extends Model
{
    use HasFactory;
    protected $table = 'property_types';
    protected $fillable = [
        'property_category', 
        'property_type', 
            
    ];

    public static $fields = [
        'property_category' => ['label' => 'Property Category', 'inputType' => 'select','required' =>true,'readonly' => true],
        'property_type' => ['label' => 'Property Type', 'inputType' => 'text', 'required' =>true,'readonly' => ''],
       
      
        // Add more fields as needed
    ];

    public static function getFieldData($field)
    {
    switch ($field) {  
            case 'property_category':
                return [
                    'Residential' => 'Residential',
                    'Commercial'=> 'Commercial'];
        // Add more cases for additional filter fields
        default:
            return [];
    }
    }

    public function property(){
        return $this->hasMany(Property::class,'property_type');
    }
}
