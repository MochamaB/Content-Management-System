<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\FilterableScope;

class Payment extends Model
{
    use HasFactory, FilterableScope;
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

    /////Polymorphic Relationship (Payment can belong to an Invoice or Voucher or Charge)
    public function model()
    {
        return $this->morphTo();
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
}
