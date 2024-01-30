<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Vendor extends Model
{
    use HasFactory;
    use HasFactory, InteractsWithMedia;
    protected $table = 'vendors';
    protected $fillable = [
        'property_id',
        'vendorcategory_id',
        'name',
        'email',
        'phonenumber',
        'password',

    ];
    ////////// FIELDS FOR CREATE AND EDIT METHOD
    public static $fields = [
        'property_id' => ['label' => 'Property', 'inputType' => 'select', 'required' => true, 'readonly' => ''],
        'vendorcategory_id' => ['label' => 'Vendor Category', 'inputType' => 'selectgroup', 'required' => true, 'readonly' => ''],
        'name' => ['label' => 'Name of Vendor', 'inputType' => 'text', 'required' => true, 'readonly' => ''],
        'email' => ['label' => 'Email', 'inputType' => 'email', 'required' => true, 'readonly' => ''],
        'phonenumber' => ['label' => 'Phonenumber', 'inputType' => 'tel', 'required' => true, 'readonly' => ''],



        // Add more fields as needed
    ];

    public static $validation = [
        'property_id' => 'required',
        'vendorcategory_id' => 'required',
        'name' => 'required',
        'email' => 'required',
        'phonenumber' => 'required',


    ];

    /////Filter options
    public static $filter = [
        'vendorcategory_id' => 'Type',
        'property_streetname' => 'Location',
        'property_status' => 'Status',
        // Add more filter fields as needed
    ];

    public static function getFieldData($field)
    {
        switch ($field) {
            case 'property_id':
                return Property::pluck('property_name', 'id')->toArray();
            case 'vendorcategory_id':
                $vendorcategory = VendorCategory::all();
                $vendorcategories = $vendorcategory->groupBy('vendor_category');
                $data = []; // Initialize $data as an empty array
                foreach ($vendorcategories as $category => $propertytype) {
                    $data[$category] = $propertytype->pluck('vendor_type', 'id')->toArray();
                }
                return $data;
                // Add more cases for additional filter fields
            default:
                return [];
        }
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function vendorCategory()
    {
        return $this->belongsTo(VendorCategory::class);
    }

    public function vendorSubscription()
    {
        return $this->hasOne(VendorSubscription::class);
    }

}
