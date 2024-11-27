<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Traits\MediaUpload;
use App\Traits\FilterableScope;
use App\Traits\SoftDeleteScope;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;



class Lease extends Model implements HasMedia, Auditable
{
    use HasFactory, Notifiable, InteractsWithMedia, MediaUpload, FilterableScope, SoftDeleteScope, SoftDeletes, AuditableTrait;
    protected $table = 'leases';
    const STATUS_ACTIVE = 1;
    const STATUS_TERMINATED = 2;
    const STATUS_EXPIRED = 3;
    const STATUS_PENDING = 4;
    const STATUS_NOTICE = 5;

    public static $statusLabels = [
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_TERMINATED => 'Terminated',
        self::STATUS_EXPIRED => 'Expired',
        self::STATUS_PENDING => 'Pending',
        self::STATUS_NOTICE => 'Notice Given',
    ];
    protected $fillable = [
        'property_id',
        'unit_id',
        'user_id',
        'lease_period',
        'status',
        'startdate',
        'enddate',

    ];
    ////////// FIELDS FOR CREATE AND EDIT METHOD
    public static $fields = [
        'property_id' => ['label' => 'Property', 'inputType' => 'select', 'required' => true, 'readonly' => true],
        'unit_id' => ['label' => 'Unit', 'inputType' => 'select', 'required' => true, 'readonly' => true],
        'user_id' => ['label' => 'Tenant Name', 'inputType' => 'select', 'required' => true, 'readonly' => ''],
        'lease_period' => ['label' => 'Lease Type', 'inputType' => 'select', 'required' => true, 'readonly' => ''],
        'status' => ['label' => 'Status', 'inputType' => 'select', 'required' => false, 'readonly' => ''],
        'startdate' => ['label' => 'Start Date', 'inputType' => 'date', 'required' => true, 'readonly' => ''],
        'enddate' => ['label' => 'End Date', 'inputType' => 'date', 'required' => true, 'readonly' => ''],


        // Add more fields as needed
    ];

    public static $validation = [
        'property_id' => 'required',
        'unit_id' => 'required|numeric',
        'user_id' => 'required',
        'lease_period' => 'required',
        'status' => 'nullable',
        'startdate' => 'required|date',
        'enddate' => 'nullable|date',
    ];

    protected $auditInclude = [
        'property_id',
        'unit_id',
        'user_id',
        'lease_period',
        'status',
        'startdate',
        'enddate',
        // Add other attributes you want to audit here.
    ];
    protected $auditThreshold = 20;

    public function transformAudit(array $data): array
    {
        $data['property_id'] = $this->property_id;
        $data['unit_id'] = $this->unit_id;

        return $data;
    }

    public function getStatusLabel()
    {
    return self::$statusLabels[$this->status] ?? 'Unknown Status';
    }

    public static function getFieldData($field)
    {
        $leases = Lease::with('property', 'unit', 'user')->get();
        switch ($field) {
            case 'property_id':
                // Retrieve the supervised units' properties
                $properties = $leases->pluck('property.property_name', 'property.id')->toArray();
                return $properties;
            case 'unit_id':
                // Retrieve the supervised units' properties
                $units = $leases->pluck('unit.unit_number', 'unit.id')->toArray();
                return $units;
            case 'user_id':
                $tenants = User::selectRaw('CONCAT(firstname, " ", lastname) as full_name, id')
                    ->pluck('full_name', 'id')
                    ->toArray();
                return  $tenants;
            case 'lease_period':
                return [
                    'monthly' => 'Month to Month',
                    'fixed' => 'fixed'

                ];
            case 'status':
                return [
                    'active' => 'Active',
                    'suspended' => 'Suspended'

                ];
                // Add more cases for additional filter fields
            default:
                return [];
        }
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
        return $this->belongsTo(User::class, 'user_id');
    }

    public function settings()
    {
        return $this->morphMany(Setting::class, 'model');
    }

    public function invoice()
    {
        return $this->hasMany(Invoice::class, 'unit_id');
    }

    public function deposit()
    {
        return $this->hasMany(Deposit::class, 'unit_id');
    }

    public function unitcharges()
    {
        return $this->hasMany(Unitcharge::class, 'unit_id');
    }


    public function audit()
    {
        return $this->morphMany(Audit::class, 'auditable');
    }

    public function getIdentifier()
    {
        return $this->id;
    }

    public function scopeUserUnits($query)
    {
        // Get the authenticated user
        $user = auth()->user();

        if ($user) {
            // Get the IDs of units assigned to the user
            $unitIds = $user->units->pluck('id')->toArray();

            // Apply the filter to the query
            $query->whereIn('unit_id', $unitIds);
        }
    }
    ///Spatie Media conversions
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(150)
            ->height(150)
            ->sharpen(10);
    }
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('Lease-Agreement');
        //add options


    }
}
