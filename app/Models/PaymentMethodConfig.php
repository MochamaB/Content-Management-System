<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethodConfig extends Model
{
    use HasFactory;
    protected $table = 'payment_method_configs';
    protected $fillable = [
        'payment_method_id',
        'account_number',
        'bank_name',
        'branch_name',
        'mpesa_shortcode',
        'mpesa_account_number',
        'consumer_key',
        'consumer_secret',
        'passkey',
        'environment',
    ];

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }
}
