<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;
    protected $table = 'properties';
    protected $fillable = [
        'property_name',
        'property_type',
        'property_location',
        'property_streetname',
        'property_manager',
        'property_status',
            
    ];
    ////////// FIELDS FOR CREATE AND EDIT METHOD
    public static $fields = [
        'property_type' => ['label' => 'Property Type', 'inputType' => 'selectgroup','required' =>true,'readonly' => true],
        'property_name' => ['label' => 'Property Name', 'inputType' => 'text','required' =>true, 'readonly' => true],
        'property_location' => ['label' => 'Location', 'inputType' => 'text', 'required' =>true,'readonly' => ''],
        'property_streetname' => ['label' => 'Street Address', 'inputType' => 'text', 'required' =>true,'readonly' => ''],
        'property_manager' => ['label' => 'Property Manager', 'inputType' => 'select', 'required' =>false,'readonly' => ''],
        'property_status' => ['label' => 'Property Status', 'inputType' => 'select', 'required' =>true,'readonly' => ''],
       
      
        // Add more fields as needed
    ];

     /////Filter options
     public static $filter = [
        'property_location' => 'Type',
        'property_streetname' => 'Location', 
        'property_status' => 'Status',
        // Add more filter fields as needed
    ];

    public static $headingAfterField = 'property_name';
    public static $additionalHeading = 'New Property';

    public static function getFieldData($field)
    {
    switch ($field) {
       
            case 'property_type':
                $propertytype = PropertyType::all();
                $propertytypes = $propertytype->groupBy('property_category');
                foreach ($propertytypes as $category => $propertytype) {
                    $data[$category] = $propertytype->pluck('property_type','id')->toArray();
                }
            return $data;
        case 'property_manager':
            return  ['Notset', 'Set'];
        case 'property_status':
            return  ['Active', 'InActive'];
        // Add more cases for additional filter fields
        default:
            return [];
    }
    }

    public function amenities(){
        return $this->belongsToMany(Amenity::class, 'properties_amenities');

    }

    public function utilities()
    {
        return $this->hasMany(Utilities::class);
    }

    


}
