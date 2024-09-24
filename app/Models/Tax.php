<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\FilterableScope;
use App\Traits\SoftDeleteScope;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tax extends Model
{
    use HasFactory, FilterableScope, SoftDeletes, SoftDeleteScope;
    protected $table = 'taxes';
    protected $fillable = [
        'property_type_id',
        'name',
        'taxable_type',
        'taxable_id',
        'rate',
        'status',
        'description'
    ];

    public static $fields = [
        'property_type_id' => ['label' => 'Property Category', 'inputType' => 'selectgroup', 'required' => true, 'readonly' => ''],
        'name' => ['label' => 'Tax Name', 'inputType' => 'text', 'required' => true, 'readonly' => ''],
        'taxable_type' => ['label' => 'Apply To', 'inputType' => 'select', 'required' => true, 'readonly' => ''],
        'rate' => ['label' => 'Tax Rate', 'inputType' => 'number', 'required' => true, 'readonly' => ''],
        'status' => ['label' => 'Status', 'inputType' => 'select', 'required' => true, 'readonly' => ''],
        'description' => ['label' => 'Description', 'inputType' => 'textarea', 'required' => false, 'readonly' => ''],
       
        // Add more fields as needed
    ];

    public static $validation = [
        'property_type_id' => 'required',
        'name' => 'required',
        'taxable_type' => 'required',
        'taxable_id' => 'nullable',
        'rate' => 'required|numeric',
        'status' => 'required',
        'description' => 'nullable',
    ];

    public static function getFieldData($field)
    {
        switch ($field) {
            case 'property_type_id':
                // Retrieve the property Types properties
                $propertytype = PropertyType::all();
                $propertytypes = $propertytype->groupBy('property_category');
                $data = []; // Initialize $data as an empty array
                foreach ($propertytypes as $category => $propertytype) {
                    $data[$category] = $propertytype->pluck('property_type', 'id')->toArray();
                }
                return $data;
                //  return Property::pluck('property_name','id')->toArray();
            case 'taxable_type':
                return [
                    'App\\Models\\Invoice' => 'Invoices',
                    'App\\Models\\Payment' => 'Payments',
                    'App\\Models\\Deposit' => 'Deposits',
                    'App\\Models\\Expense' => 'Expenses'
                ];
                case 'status':
                    return [
                        'active' => 'Active',
                        'Inactive' => 'In Active',
                    ];
                // Add more cases for additional filter fields
            default:
                return [];
        }
    }

    public function propertyType()
{
    return $this->belongsTo(PropertyType::class);
}

    public function taxable()
    {
        return $this->morphTo();
    }
}
