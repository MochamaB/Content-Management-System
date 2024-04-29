<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\FilterableScope;

class Unitcharge extends Model
{
    use HasFactory;
    use FilterableScope;
    protected $table = 'unitcharges';
    protected $fillable = [
        'property_id',
        'unit_id',
        'chartofaccounts_id',
        'charge_name',
        'charge_cycle',
        'charge_type',
        'rate',
        'parent_id',
        'recurring_charge',
        'startdate',
        'nextdate',

    ];
    public static $fields = [
        'property_id' => ['label' => 'Unit Number', 'inputType' => 'select', 'required' => true, 'readonly' => true],
        'unit_id' => ['label' => 'Unit Property', 'inputType' => 'select', 'required' => true, 'readonly' => ''],
        'chartofaccounts_id' => ['label' => 'Slug', 'inputType' => 'text', 'required' => false, 'readonly' => ''],
        'charge_name' => ['label' => 'Description', 'inputType' => 'text', 'required' => false, 'readonly' => ''],

        // Add more fields as needed
    ];

    public static $validation = [
        'property_id' => 'required',
        'unit_id' => 'required',
        'chartofaccounts_id' => 'required',
        'charge_name' => 'required',
        'charge_cycle' => 'required',
        'charge_type' => 'required',
        'rate' => 'required|numeric',
        'parent_id' => 'nullable',
        'recurring_charge' => 'required',
        'startdate' => 'nullable',
        


    ];


    public static function getFieldData($field)
    {
        switch ($field) {
            case 'unit_id':
                // Retrieve the supervised units' properties

                //  return Property::pluck('property_name','id')->toArray();
            case 'unit_property':
                return [
                    'mainphoto' => 'Main Photo',
                    'photo' => 'Photo',
                    'video' => 'Video',
                    'feature' => 'Feature'
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
    public function chartofaccounts()
    {
        return $this->belongsTo(Chartofaccount::class);
    }

    public function meterReading()
    {
        return $this->hasMany(MeterReading::class, 'unitcharge_id');
    }
//
 //   public function lease()
  //  {
    //    return $this->belongsTo(Lease::class, 'unit_id');
 //   }


    // Accessor to get the chartofaccounts name
    public function getChartOfAccountsNameAttribute()
    {
        return $this->chartofaccounts->account_name;
    }

    public function parentcharge()
    {
        return $this->belongsTo(Unitcharge::class, 'parent_id');
    }

    public function childrencharge()
    {
        return $this->hasMany(Unitcharge::class, 'parent_id');
    }



}
