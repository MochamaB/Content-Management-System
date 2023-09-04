<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;


class Unit extends Model
{
    use HasFactory;
    protected $table = 'units';
    protected $fillable = [
            'property_id',
            'unit_number',
            'unit_type',
            'rent',
            'security_deposit',
            'size',
            'bathrooms',
            'bedrooms',
            'description',
            'selling_price',
    ];

    public static $fields = [
        'property_id' => ['label' => 'Property Name', 'inputType' => 'select','required' =>true,'readonly' => true],
        'unit_type' => ['label' => 'Type', 'inputType' => 'select','required' =>true, 'readonly' => ''],
        'unit_number' => ['label' => 'Unit Number', 'inputType' => 'text', 'required' =>true,'readonly' => ''],
        'rent' => ['label' => 'Market Rent', 'inputType' => 'number', 'required' =>false,'readonly' => ''],
        'security_deposit' => ['label' => 'Security Deposit', 'inputType' => 'text','required' =>false,'readonly' => true],
        'size' => ['label' => 'Size', 'inputType' => 'text','required' =>false, 'readonly' => ''],
        'bathrooms' => ['label' => 'No of Bathrooms', 'inputType' => 'number', 'required' =>true,'readonly' => ''],
        'bedrooms' => ['label' => 'No of Bedrooms', 'inputType' => 'number', 'required' =>true,'readonly' => ''],
        'description' => ['label' => 'Description', 'inputType' => 'textarea', 'required' =>false,'readonly' => ''],
        'selling_price' => ['label' => 'Selling Price', 'inputType' => 'number', 'required' =>false,'readonly' => ''],
      
        // Add more fields as needed
    ];
    public static function getFieldData($field)
    {
    switch ($field) {
        case 'property_id':
            // Retrieve the supervised units' properties
      
                if (Gate::allows('view-all', auth()->user())) {
                    $properties = Property::pluck('property_name','id')->toArray();
                   
                } else {
                    $properties = auth()->user()->supervisedUnits->pluck('property.property_name', 'property.id')->toArray();
                }         
                return $properties;
          //  return Property::pluck('property_name','id')->toArray();
            case 'unit_type':
                return [
                    'rent' => 'For Rent',
                    'sale', 'For Sale'];
        // Add more cases for additional filter fields
        default:
            return [];
    }
    }


    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function unitSupervisors()
    {
        return $this->belongsToMany(User::class, 'user_unit', 'unit_id', 'user_id')
        ->withTimestamps();
    }

    ////////// view all units and properties for superadmin
    public static function viewallunits()
    {
        $units = static::with('property')->get();

        return $units->groupBy('property_id')->map(function ($propertyUnits) {
            return $propertyUnits;
        });
    }

    public function unitdetails()
    {
        return $this->hasMany(UnitDetail::class);
    }

}
