<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Models\Audit as BaseAudit;

class Audit extends BaseAudit
{
    use HasFactory;
    protected $fillable = [
        'user_type',
        'user_id',
        'event',
        'auditable_type',
        'auditable_id',
        'old_values',
        'new_values',
        'url',
        'ip_address',
        'user_agent',
        'tags',
        // Add your custom fields here
        'property_id',
        'unit_id',
    ];

    public function transformAudit(array $data): array
    {
        $data = parent::transformAudit($data); // Call the parent method

        // Ensure your custom fields (if not already added)
        $data['property_id'] = $this->auditable->property_id ?? null;
        $data['unit_id'] = $this->auditable->unit_id ?? null;

        return $data;
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
    public function user()
    {
        return $this->morphTo();
    }
    // Define the polymorphic relationship
    public function auditable()
    {
        return $this->morphTo();
    }
     // Accessor to safely access `auditable` with null checking
     public function getAuditableOrNullAttribute()
     {
         return $this->auditable ?? null;
     }
}
