<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\FilterableScope;

class Paymentvoucher extends Model
{
    use HasFactory, FilterableScope;
    protected $table = 'paymentvouchers';
    protected $fillable = [
        'property_id',
        'unit_id',
        'payable_type',
        'payable_id',
        'model_type',
        'model_id',
        'referenceno',
        'name',
        'totalamount',
        'status',
        'duedate'

    ];

    public static $validation = [
        'property_id' => 'required',
        'unit_id' => 'nullable',
        'payable_type' => 'nullable',
        'payable_id' => 'nullable',
        'model_type' => 'required',
        'model_id' => 'required',
        'name' => 'required',
        'status' => 'nullable',
        'duedate' => 'nullable|date',
    ];


    public function model()
    {
        return $this->morphTo();
    }
    public function property()
      {
          return $this->belongsTo(Property::class,'property_id');
      }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'model_id')->where('model_type', User::class);
    }

    public function lease()
    {
        return $this->belongsTo(Lease::class, 'unit_id');
    }

    public function paymentvoucherItems()
    {
        return $this->hasMany(PaymentVoucherItems::class);
    }

    public function transactions()
    {
        return $this->morphMany(Transaction::class, 'transactionable');
    }
}
