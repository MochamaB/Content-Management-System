<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $table = 'transactions';
    protected $fillable = [
        'property_id',
        'unit_id',
        'charge_id',
        'transactionable_id',
        'transactionable_type',
        'description',
        'debitaccount_id',
        'creditaccount_id',
        'amount',
    ];

    public function transactionable()
    {
        return $this->morphTo();
    }
}
