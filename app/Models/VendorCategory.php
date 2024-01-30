<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorCategory extends Model
{
    use HasFactory;
    protected $table = 'vendor_categories';
    protected $fillable = [
        'vendor_category', 
        'vendor_type', 
            
    ];

    public static $fields = [
        'vendor_category' => ['label' => 'vendor Category', 'inputType' => 'select','required' =>true,'readonly' => true],
        'vendor_type' => ['label' => 'vendor Type', 'inputType' => 'text', 'required' =>true,'readonly' => ''],
       
      
        // Add more fields as needed
    ];

    public static function getFieldData($field)
    {
    switch ($field) {  
            case 'vendor_category':
                return [
                    'Contractors' => 'Contractors',
                    'Professionals'=> 'Professionals',
                    'Suppliers'=> 'Suppliers',
                    'Utilities'=> 'Utilities',
                ];
        // Add more cases for additional filter fields
        default:
            return [];
    }
    }

    public function vendors()
    {
        return $this->hasMany(Vendor::class);
    }

}
