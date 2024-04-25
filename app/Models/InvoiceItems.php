<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItems extends Model
{
    use HasFactory;
    protected $table = 'invoice_items';
    protected $fillable = [
        'invoice_id',
        'unitcharge_id',
        'chartofaccount_id',
        'charge_name',
        'description',
        'amount',

    ];

    public function invoices()
    {
        return $this->belongsTo(Invoice::class);
    }
    public function unitcharge ()
    {
        return $this->belongsTo(Unitcharge::class);
    }
    public function accounts()
    {
        return $this->belongsTo(Chartofaccount::class, 'chartofaccount_id');
    }
    public function meterReadings()
{
    return $this->hasMany('App\Models\MeterReading', 'unitcharge_id', 'unitcharge_id');
}
}
