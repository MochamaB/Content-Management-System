<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Traits\FilterableScope;
use App\Traits\SoftDeleteScope;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;


class Payment extends Model implements HasMedia, Auditable
{
    use HasFactory, InteractsWithMedia, FilterableScope, SoftDeletes, SoftDeleteScope, AuditableTrait;
    protected $table = 'payments';
    protected $fillable = [
        'property_id',
        'unit_id',
        'model_type',
        'model_id',
        'referenceno',
        'payment_method_id',
        'payment_code',
        'totalamount',
        'taxamount',
        'received_by',
        'reviewed_by',
        'invoicedate'

    ];

    

    public static $validation = [
        'property_id' => 'nullable',
        'unit_id' => 'nullable',
        'model_type' => 'nullable',
        'model_id' => 'nullable',
        'referenceno' => 'nullable',
        'payment_method_id' => 'required',
        'payment_code' => 'nullable|unique:payments',
        'totalamount' => 'nullable',
      
    ];

    protected $auditInclude = [
        'property_id',
        'unit_id',
        'model_type',
        'model_id',
        'referenceno',
        'payment_method_id',
        'payment_code',
        'totalamount',
        'taxamount',
        'received_by',
        'reviewed_by',
        'invoicedate'

        // Add other attributes you want to audit here.
    ];

    protected $auditThreshold = 30;

    public function transformAudit(array $data): array
    {
        $data['property_id'] = $this->property_id;
        $data['unit_id'] = $this->unit_id;
    
        return $data;
    }

    public function getValidationRules()
    {
        // Get base validation rules
        $rules = self::$validation;

        // Check if the model exists (update scenario) or is new (create scenario)
        if ($this->exists) {
            // If the model exists, apply the 'unique' rule while ignoring the current record ID
            $rules['payment_code'] = [
                'nullable',
                Rule::unique('payments')->ignore($this->id)
            ];
        } else {
            // If it's a create scenario, apply the regular unique rule
            $rules['payment_code'] = 'nullable|unique:payments';
        }

        return $rules;
    }

    // Validation method to be called in controller
    public function validate($data)
    {
        // Call Laravel validator with the rules
        return validator($data, $this->getValidationRules())->validate();
    }

    /////Polymorphic Relationship (Payment can belong to an Invoice or Voucher or Charge)
    public function model()
    {
        return $this->morphTo()->withDefault(function ($model) {
            // Create a default deleted model instance
            $model->name = 'Deleted ' . class_basename($this->model_type);
            $model->is_deleted = true;
        });
    }

    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }


    public function lease()
    {
        return $this->belongsTo(Lease::class, 'unit_id');
    }

    public function PaymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    ///////Polymorphic Relationship with Transactions
    public function transactions()
    {
        return $this->morphMany(Transaction::class, 'transactionable');
    }
    public function taxes()
    {
        return $this->morphMany(Tax::class, 'taxable');
    }
    public function audit()
    {
        return $this->morphMany(Audit::class, 'auditable');
    }

    public function getIdentifier()
    {
        return $this->referenceno;
    }

}
