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
    
    public static $statusLabels = [
        self::TYPE_PROPERTY => 'Per Property',
        self::TYPE_USER => 'Per User',
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
        'credit_type' => 'required',
        'property_id' => 'nullable',
        'user_id' => 'nullable',
        'tariff' => 'required',
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
