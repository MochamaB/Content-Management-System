<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    protected $table = 'invoices';
    protected $fillable = [
        'property_id',
        'unit_id',
        'user_id',
        'referenceno',
        'invoice_type',
        'totalamount',
        'status',
        'duedate',

    ];

    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItems::class);
    }

    public function transactions()
    {
        return $this->morphMany(Transaction::class, 'transactionable');
    }
}
