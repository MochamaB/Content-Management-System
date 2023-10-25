<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeterReading extends Model
{
    use HasFactory;
    protected $table = 'meter_readings';
    protected $fillable = [
        'property_id',
        'unit_id',
        'unitcharge_id',
        'lastreading',
        'currentreading',
        'rate_at_reading',
        'startdate',
        'enddate',
        'recorded_by',
    ];

     ////////// FIELDS FOR CREATE AND EDIT METHOD
     public static $fields = [
        'property_id' => ['label' => 'Property', 'inputType' => 'select', 'required' => true, 'readonly' => true],
        'unit_id' => ['label' => 'Unit', 'inputType' => 'select', 'required' => true, 'readonly' => true],
        'unitcharge_id' => ['label' => 'Charge', 'inputType' => 'select', 'required' => true, 'readonly' => ''],
        'lastreading' => ['label' => 'Last Reading', 'inputType' => 'text', 'required' => true, 'readonly' => true],
        'currentreading' => ['label' => 'Current Reading', 'inputType' => 'text', 'required' => false, 'readonly' =>''],
        'rate_at_reading' => ['label' => 'Rate', 'inputType' => 'text', 'required' => true, 'readonly' => true],
        'startdate' => ['label' => 'Start Date', 'inputType' => 'date', 'required' => true, 'readonly' => ''],
        'enddate' => ['label' => 'End Date', 'inputType' => 'date', 'required' => true, 'readonly' => ''],


        // Add more fields as needed
    ];

    public static $validation = [
        'property_id' => 'required',
        'unit_id' => 'required',
        'unitcharge_id' => 'required',
        'lastreading' => 'required',
        'currentreading' => 'required',
        'rate_at_reading' => 'nullable',
        'startdate' => 'nullable',
        'enddate' => 'required',
        'recorded_by' => 'nullable',
    ];

    public static function getFieldData($field)
    {
        switch ($field) {
            case 'property_id':
                // Retrieve the supervised units' properties
                $properties = Property::pluck('property_name', 'id')->toArray();
                return $properties;
            case 'unit_id':
                // Retrieve the supervised units' properties
                    $units = Property::pluck('property_name', 'id')->toArray();
                return $units;
            case 'user_id':
                $tenants = User::selectRaw('CONCAT(firstname, " ", lastname) as full_name, id')
                    ->pluck('full_name', 'id')
                    ->toArray();
                return  $tenants;
            case 'lease_period':
                return [
                    'monthly' => 'Month to Month',
                    'fixed'=>'fixed'

                ];
            case 'status':
                return [
                    'active' => 'Active',
                    'suspended'=> 'Suspended'

                ];
                // Add more cases for additional filter fields
            default:
                return [];
        }
    }


    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function unitcharge()
    {
        return $this->belongsTo(Unitcharge::class);
    }
 
}
