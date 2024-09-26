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
        'description',
        'related_model_type',
        'related_model_condition',
        'additional_condition'
    ];

    protected $casts = [
        'related_model_condition' => 'array',
        'additional_condition' => 'array',
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

    // Existing relationships and methods...
    // Check if the tax is applicable when this is called
    public function isApplicable($taxableEntity)
    {
        if (get_class($taxableEntity) !== $this->taxable_type) {
            return false;
        }

        if ($this->related_model_type) {
          //  $relatedEntity = $taxableEntity->{$this->getRelatedModelRelationName()};
            $relatedEntity = $taxableEntity->model;
            

            if (!$relatedEntity || get_class($relatedEntity) !== $this->related_model_type) {
                return false;
            }

            if ($this->related_model_condition) {
                foreach ($this->related_model_condition as $attribute => $value) {
                    if (strcasecmp($relatedEntity->{$attribute}, $value) !== 0) {
                        return false;
                    }
                }
            }
        }

        if ($this->additional_condition) {
            foreach ($this->additional_condition as $attribute => $value) {
                if ($taxableEntity->{$attribute} != $value) {
                    return false;
                }
            }
        }

        return true;
    }
    private function getRelatedModelRelationName()
    {
        // Convert "App\Models\Invoice" to "invoice"
        return lcfirst(class_basename($this->related_model_type));
    }

    public static function findApplicableTaxes($taxableEntity)
    {
        return self::where('taxable_type', get_class($taxableEntity))
            ->where('status', 'active')
            ->get()
            ->filter(function ($tax) use ($taxableEntity) {
                return $tax->isApplicable($taxableEntity);
            });
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
