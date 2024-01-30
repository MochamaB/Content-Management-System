<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorSubscription extends Model
{
    use HasFactory;
    protected $table = 'vendor_subscriptions';
    protected $fillable = [
        'vendor_id',
        'cycle',
        'price',
        'subscription_status',
        'start_date',
        'end-date',

    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
