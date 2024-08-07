<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;
    protected $table = 'payment_methods';
    protected $fillable = [
        'property_id',
        'name',
        'type',
        'is_active',
        
    ];

    public static $fields = [
        'property_id' => ['label' => 'Property', 'inputType' => 'select', 'required' => true, 'readonly' =>''],
        'name' => ['label' => 'Payment Name', 'inputType' => 'text', 'required' => true, 'readonly' =>''],
        'type' => ['label' => 'Type', 'inputType' => 'text', 'required' => true, 'readonly' => ''],
        'is_active' => ['label' => 'Account Name', 'inputType' => 'text', 'required' => true, 'readonly' => ''],
    
        // Add more fields as needed
    ];
    public static $validation = [
        'property_id' => 'required',
        'name' => 'required',
        'type' => 'required',
        
    ];
    public static function getFieldData($field)
    {
        switch ($field) {
            case 'property_id':
                // Retrieve the supervised units' properties
                $properties = Property::pluck('property_name', 'id')->toArray();
                return $properties;
          
                // Add more cases for additional filter fields
            default:
                return [];
        }
    }
    public function property()
    {
        return $this->belongsTo(Property::class);
    }
    public function config()
    {
        return $this->hasOne(PaymentMethodConfig::class);
    }
}
