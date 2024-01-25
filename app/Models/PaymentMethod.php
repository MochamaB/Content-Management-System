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
        'account_number',
        'account_name',
        'provider'
        
    ];

    public static $fields = [
        'property_id' => ['label' => 'Property', 'inputType' => 'select', 'required' => true, 'readonly' =>''],
        'name' => ['label' => 'Payment Name', 'inputType' => 'text', 'required' => true, 'readonly' =>''],
        'account_number' => ['label' => 'Account Number', 'inputType' => 'text', 'required' => true, 'readonly' => ''],
        'account_name' => ['label' => 'Account Name', 'inputType' => 'text', 'required' => true, 'readonly' => ''],
        'provider' => ['label' => 'Provider', 'inputType' => 'text', 'required' => false, 'readonly' => ''],
        // Add more fields as needed
    ];
    public static $validation = [
        'property_id' => 'required',
        'name' => 'required',
        'account_number' => 'required',
        'account_name' => 'required',
        'provider' => 'required',
        
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
}
