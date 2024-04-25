<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentItems extends Model
{
    use HasFactory;
    protected $table = 'payment_items';
    protected $fillable = [
        'payment_id',
        'unitcharge_id',
        'chartofaccount_id',
        'charge_name',
        'description',
        'amount',
    ];

    public function payments()
    {
        return $this->belongsTo(Payment::class);
    }

    public function accounts()
    {
        return $this->belongsTo(Chartofaccount::class, 'chartofaccount_id');
    }
}
