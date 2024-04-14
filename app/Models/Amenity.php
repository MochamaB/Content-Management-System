<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\FilterableScope;


class Amenity extends Model
{
    use HasFactory, FilterableScope;
    protected $table = 'amenities';
    protected $fillable = [
        'amenity_name',
    ];

     ////////// FIELDS FOR CREATE AND EDIT METHOD
     public static $fields = [
        'amenity_name' => ['label' => 'Enter name of Amenity', 'inputType' => 'text','required' =>true,'readonly' => ''],
      
        // Add more fields as needed
    ];

      public static $filter = [
        'amenity_name' => 'Amenities',
        
        // Add more filter fields as needed
    ];



    public static function getFieldData($field)
    {
    switch ($field) {

       
        // Add more cases for additional filter fields
        default:
            return [];
    }
    }
/**
     * The properties that belong to the amenities.
     */

    public function properties()
    {
        return $this->belongsToMany(Property::class, 'properties_amenities');
    }
}
