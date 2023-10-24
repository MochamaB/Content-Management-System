<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unitcharge extends Model
{
    use HasFactory;
    protected $table = 'unitcharges';
    protected $fillable = [
        'property_id', 
        'unit_id', 
        'chartofaccounts_id', 
        'charge_name',
        'charge_cycle',
        'charge_type',
        'rate',
        'parent_utility',
        'recurring_charge',
        'startdate',
        'nextdate',     
            
    ];
    public static $fields = [
        'property_id' => ['label' => 'Unit Number', 'inputType' => 'select','required' =>true,'readonly' => true],
        'unit_id' => ['label' => 'Unit Property', 'inputType' => 'select','required' =>true, 'readonly' => ''],
        'chartofaccounts_id' => ['label' => 'Slug', 'inputType' => 'text', 'required' =>false,'readonly' => ''],
        'charge_name' => ['label' => 'Description', 'inputType' => 'text', 'required' =>false,'readonly' => ''],
      
        // Add more fields as needed
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
                    'photo', 'Photo',
                    'video' => 'Video',
                    'feature', 'Feature'
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

      // Accessor to get the chartofaccounts name
      public function getChartOfAccountsNameAttribute()
      {
          return $this->chartofaccounts->account_name;
      }
    
}
