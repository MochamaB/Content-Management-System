<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $table = 'payments';
    protected $fillable = [
        'property_id',
        'unit_id',
        'model_type',
        'model_id',
        'referenceno',
        'payment_type_id',
        'payment_code',
        'totalamount',
        'received_by',
        'reviewed_by'

    ];

    public static $validation = [
        'payment_type_id' => 'required',
        'payment_code' => 'nullable|unique:payments',
        'amount' => 'required',
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

    public function paymentType()
    {
        return $this->belongsTo(PaymentType::class);
    }

    public function paymentItems()
    {
        return $this->hasMany(PaymentItems::class);
    }

    ///////Polymorphic Relationship with Transactions
    public function transactions()
    {
        return $this->morphMany(Transaction::class, 'transactionable');
    }
}
