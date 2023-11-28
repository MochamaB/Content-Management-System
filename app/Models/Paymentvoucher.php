<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paymentvoucher extends Model
{
    use HasFactory;
    protected $table = 'paymentvouchers';
    protected $fillable = [
        'property_id',
        'unit_id',
        'model_type',
        'model_id',
        'referenceno',
        'voucher_type',
        'totalamount',
        'status',
        'duedate'

    ];

    public function model()
    {
        return $this->morphTo();
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
