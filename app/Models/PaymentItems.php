<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentItems extends Model
{
    use HasFactory;
    protected $table = 'paymentvouchers';
    protected $fillable = [
        'payment_id',
        'unitcharge_id',
        'chartofaccount_id',
        'charge_name',
        'description',
        'amount',
    ];
}
