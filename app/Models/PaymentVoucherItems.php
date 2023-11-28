<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentVoucherItems extends Model
{
    use HasFactory;
    protected $table = 'paymentvoucher_items';
    protected $fillable = [
        'paymentvoucher_id',
        'unitcharge_id',
        'chartofaccount_id',
        'charge_name',
        'description',
        'payment_method',
        'amount',

    ];

    public function paymentvoucher()
    {
        return $this->belongsTo(Paymentvoucher::class);
    }
}
