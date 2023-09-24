<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

class Utilities extends Model
{
    use HasFactory;
    protected $table = 'utilities';
    protected $fillable = [
        'property_id',
        'chartofaccounts_id',
        'utility_name',
        'utility_type',
        'rate',

    ];

    public static $fields = [
        'property_id' => ['label' => 'Property Name', 'inputType' => 'select', 'required' => true, 'readonly' => true],
        'utility_name' => ['label' => 'Utility Name', 'inputType' => 'text', 'required' => true, 'readonly' => ''],
        'chartofaccounts_id' => ['label' => 'Account', 'inputType' => 'selectgroup', 'required' => true, 'readonly' => ''],
        'utility_type' => ['label' => 'Utility Type', 'inputType' => 'select', 'required' => false, 'readonly' => ''],
        'rate' => ['label' => 'Rate or Amount', 'inputType' => 'text', 'required' => false, 'readonly' => true],


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
                //  return Property::pluck('property_name','id')->toArray();
            case 'chartofaccounts_id':
                $account = Chartofaccounts::all();
                $accounts = $account->groupBy('account_type');
                $data = [];

                if (isset($accounts)) {
                    foreach ($accounts as $accountType => $accounts) {
                        $data[$accountType] = $accounts->pluck('account_name', 'id')->toArray();
                    }
                } else {
                    $data = null; // or an empty array or whatever is appropriate for your use case
                }

            case 'utility_type':
                return [
                    'fixed' => 'Fixed Amount',
                    'units' => 'By Units'
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
}
