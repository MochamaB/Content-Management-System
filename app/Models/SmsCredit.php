<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\FilterableScope;
use App\Traits\SoftDeleteScope;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class SmsCredit extends Model implements Auditable
{
    use HasFactory, FilterableScope, SoftDeleteScope, SoftDeletes, AuditableTrait;
    const TYPE_PROPERTY= 1;
    const TYPE_USER = 2;
    const TYPE_INSTANCE = 3;
    
    public static $statusLabels = [
        self::TYPE_PROPERTY => 'Per Property',
        self::TYPE_USER => 'Per User',
        self::TYPE_INSTANCE => 'Per Instance',
    ];

    protected $fillable = [
        'credit_type',
        'property_id',
        'user_id',
        'tariff',
        'available_credits',
        'blocked_credits',
        'used_credits',
    ];
    public static $validation = [
        'credit_type' => 'required|in:' . \App\Models\SmsCredit::TYPE_PROPERTY . ',' . \App\Models\SmsCredit::TYPE_USER. ',' . \App\Models\SmsCredit::TYPE_INSTANCE,
        'property_id' => 'required_if:credit_type,' . \App\Models\SmsCredit::TYPE_PROPERTY . '|nullable|exists:properties,id|unique:sms_credits,property_id,NULL,id,credit_type,' . \App\Models\SmsCredit::TYPE_PROPERTY,
        'user_id'     => 'required_if:credit_type,' . \App\Models\SmsCredit::TYPE_USER . '|nullable|exists:users,id|unique:sms_credits,user_id,NULL,id,credit_type,' . \App\Models\SmsCredit::TYPE_USER,
        'tariff'      => 'required|numeric',
        'available_credits' => 'required',
        'blocked_credits' => 'required',
        'used_credits' => 'required',

    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function users()
    {
        return $this->belongsTo(User::class);
    }
}
