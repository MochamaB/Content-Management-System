<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

class Lease extends Model
{
    use HasFactory;
    protected $table = 'leases';
    protected $fillable = [
        'property_id',
        'unit_id',
        'user_id',
        'lease_period',
        'status',
        'startdate',
        'enddate',

    ];
    ////////// FIELDS FOR CREATE AND EDIT METHOD
    public static $fields = [
        'property_id' => ['label' => 'Property', 'inputType' => 'select', 'required' => true, 'readonly' => true],
        'unit_id' => ['label' => 'Unit', 'inputType' => 'select', 'required' => true, 'readonly' => true],
        'user_id' => ['label' => 'Tenant Name', 'inputType' => 'select', 'required' => true, 'readonly' => ''],
        'lease_period' => ['label' => 'Lease Type', 'inputType' => 'select', 'required' => true, 'readonly' => ''],
        'status' => ['label' => 'Status', 'inputType' => 'select', 'required' => false, 'readonly' => ''],
        'startdate' => ['label' => 'Start Date', 'inputType' => 'date', 'required' => true, 'readonly' => ''],
        'enddate' => ['label' => 'End Date', 'inputType' => 'date', 'required' => true, 'readonly' => ''],


        // Add more fields as needed
    ];
    public static function getFieldData($field)
    {
        switch ($field) {
            case 'property_id':
                // Retrieve the supervised units' properties

                if (Gate::allows('view-all', auth()->user())) {
                    $properties = Property::pluck('property_name', 'id')->toArray();
                } else {
                    $properties = auth()->user()->supervisedUnits->pluck('property.property_name', 'property.id')->toArray();
                }
                return $properties;
            case 'unit_id':
                // Retrieve the supervised units' properties

                if (Gate::allows('view-all', auth()->user())) {
                    $units = Unit::pluck('unit_number', 'id')->toArray();
                } else {
                    $units = auth()->user()->supervisedUnits->pluck('unit_number', 'id')->toArray();
                }
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

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
    
}
