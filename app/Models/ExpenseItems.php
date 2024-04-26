<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseItems extends Model
{
    use HasFactory;
    protected $table = 'expense_items';
    protected $fillable = [
        'expense_id',
        'unitcharge_id',
        'chartofaccount_id',
        'description',
        'amount',

    ];

    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }
    public function accounts()
    {
        return $this->belongsTo(Chartofaccount::class, 'chartofaccount_id');
    }
}
