<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use App\Traits\FilterableScope;
use App\Traits\SoftDeleteScope;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;


class Utility extends Model implements Auditable
{
    use HasFactory, FilterableScope, SoftDeletes, SoftDeleteScope, AuditableTrait;
    protected $table = 'utilities';
    protected $fillable = [
        'property_id',
        'chartofaccounts_id',
        'utility_name',
        'utility_type',
        'default_rate',
        'default_charge_cycle',
        'is_recurring_by_default',

    ];

    public static $fields = [
        'property_id' => ['label' => 'Property Name', 'inputType' => 'select', 'required' => true, 'readonly' => true],
        'utility_name' => ['label' => 'Utility Name', 'inputType' => 'text', 'required' => true, 'readonly' => ''],
        'chartofaccounts_id' => ['label' => 'Account', 'inputType' => 'selectgroup', 'required' => true, 'readonly' => ''],
        'utility_type' => ['label' => 'Utility Type', 'inputType' => 'select', 'required' => true, 'readonly' => ''],
        'default_charge_cycle' => ['label' => 'Cycle', 'inputType' => 'select', 'required' => true, 'readonly' =>''],
        'default_rate' => ['label' => 'Rate or Amount', 'inputType' => 'text', 'required' => true, 'readonly' =>''],
        


        // Add more fields as needed
    ];

    public static $validation = [
        'property_id' => 'required',
        'utility_name' => 'required',
        'chartofaccounts_id' => 'required',
        'utility_type' => 'required',
        'default_charge_cycle' => 'required',
        'default_rate' => 'required',
    ];
    protected $auditInclude = [
        'property_id' => 'required',
        'utility_name' => 'required',
        'chartofaccounts_id' => 'required',
        'utility_type' => 'required',
        'default_charge_cycle' => 'required',
        'default_rate' => 'required',
        // Add other attributes you want to audit here.
    ];

    protected $auditThreshold = 20;
    public function transformAudit(array $data): array
    {
        $data['property_id'] = $this->property_id;
        $data['unit_id'] = $this->unit_id; // Assuming you want to store the unit's own ID
    
        return $data;
    }

    public static function getFieldData($field)
    {
        switch ($field) {
            case 'property_id':
                // Retrieve the supervised units' properties
                    $properties = Property::pluck('property_name', 'id')->toArray();
                return $properties;
                //  return Property::pluck('property_name','id')->toArray();
            case 'chartofaccounts_id':
                $account = Chartofaccount::where('account_type','Income')->get();
                $accounts = $account->groupBy('account_type');
                $data = []; // Initialize $data as an empty array
           
                    foreach ($accounts as $accountType => $accounts) {
                        $data[$accountType] = $accounts->pluck('account_name', 'id')->toArray();
                    }

                    return $data;// or an empty array or whatever is appropriate for your use case
                

            case 'utility_type':
                return [
                    'fixed' => 'Fixed Amount',
                    'units' => 'By Units'
                ];
            case 'default_charge_cycle':
                return [
                    'Monthly' => 'Monthly',
                    'Twomonths' => '2 Months',
                    'Quaterly' => 'Quaterly',
                    'Halfyear' => 'Half Year',
                    'Year' => '1 Year',
                  
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

    public function settings()
    {
        return $this->morphMany(Setting::class, 'settingable');
    }
    public function accounts()
    {
        return $this->belongsTo(Chartofaccount::class, 'chartofaccounts_id');
    }
}
