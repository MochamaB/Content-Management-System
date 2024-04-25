<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepositItems extends Model
{
    use HasFactory;
    protected $table = 'deposit_items';
    protected $fillable = [
        'deposit_id',
        'unitcharge_id',
        'chartofaccount_id',
        'charge_name',
        'description',
        'amount',

    ];

    public function deposit()
    {
        return $this->belongsTo(Deposit::class);
    }
}
