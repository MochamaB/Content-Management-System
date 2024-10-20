<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsTopup extends Model
{
    use HasFactory;
    protected $table = 'sms_topups';
    protected $fillable = [
        'user_id',
        'transaction_code',
        'amount',
        'received_credits',
    ];
}
