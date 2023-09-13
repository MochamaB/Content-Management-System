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
        return $this->belongsTo(Chartofaccounts::class);
    }

      // Accessor to get the chartofaccounts name
      public function getChartOfAccountsNameAttribute()
      {
          return $this->chartofaccounts->account_name;
      }
    
}
