<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $table = 'paymentvouchers';
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
}
