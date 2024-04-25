<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LedgerEntry extends Model
{
    use HasFactory;
    protected $table = 'ledger_entries';
    protected $fillable = [
        'property_id',
        'unit_id',
        'chartofaccount_id',
        'transaction_id',
        'amount',
        'entry_type',
    ];
}
