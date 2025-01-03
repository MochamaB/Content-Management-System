<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Traits\FilterableScope;
use App\Traits\SoftDeleteScope;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;




class Unit extends Model implements HasMedia, Auditable
{
    use HasFactory, InteractsWithMedia, FilterableScope, SoftDeletes, SoftDeleteScope, AuditableTrait;
    protected $table = 'units';
    protected $fillable = [
        'property_id',
        'unit_number',
        'unit_type',
        'rent',
        'security_deposit',
        'bathrooms',
        'bedrooms',
        'selling_price',
    ];
  



    public static $fields = [
        'property_id' => ['label' => 'Property Name', 'inputType' => 'select', 'required' => true, 'readonly' => true],
        'unit_type' => ['label' => 'Type', 'inputType' => 'select', 'required' => true, 'readonly' => ''],
        'unit_number' => ['label' => 'Unit Number', 'inputType' => 'text', 'required' => true, 'readonly' => ''],
        'rent' => ['label' => 'Market Rent', 'inputType' => 'money', 'required' => false, 'readonly' => ''],
        'security_deposit' => ['label' => 'Security Deposit', 'inputType' => 'money', 'required' => false, 'readonly' => ''],
        'bathrooms' => ['label' => 'No of Bathrooms', 'inputType' => 'number', 'required' => true, 'readonly' => ''],
        'bedrooms' => ['label' => 'No of Bedrooms', 'inputType' => 'number', 'required' => true, 'readonly' => ''],
        'selling_price' => ['label' => 'Selling Price', 'inputType' => 'money', 'required' => false, 'readonly' => ''],

        // Add more fields as needed
    ];
    public static $validation = [
        'property_id' => 'required',
        'unit_type' => 'required',
        'unit_number' => 'required',
        'rent' => 'nullable|numeric',
        'security_deposit' => 'nullable|numeric',
        'bathrooms' => 'required|numeric',
        'bedrooms' => 'required|numeric',
        'selling_price' => 'nullable|numeric',


    ];

    protected $auditInclude = [
        'property_id',
        'unit_number',
        'unit_type',
        'rent',
        'security_deposit',
        'bathrooms',
        'bedrooms',
        'selling_price',
        // Add other attributes you want to audit here.
    ];

    protected $auditThreshold = 10;
    public function transformAudit(array $data): array
    {
        $data['property_id'] = $this->property_id;
        $data['unit_id'] = $this->id; // Assuming you want to store the unit's own ID
    
        return $data;
    }

    public static function getFieldData($field)
    {
        switch ($field) {
            case 'property_id':
                // Retrieve the supervised units' properties
                $properties = Property::pluck('property_name', 'id')->toArray();
                return $properties;
                //  return Property::pluck('property_name','id')->toArray();
            case 'unit_type':
                return [
                    'rent' => 'For Rent',
                    'sale' => 'For Sale'
                ];
                // Add more cases for additional filter fields
            default:
                return [];
        }
    }

    ///// Data for populating cards
    /////Card options
    public static $card = [
        'All units' => 'information',
        'Units Leased' => 'progress',
        'No of Tenants' => 'detail',
        'Vacant Units' => 'information',
        // Add more cards as needed
    ];

    public static function getCardData($card, $modeldata = null)
    {
        switch ($card) {

            case 'All units':
                //  $modelCount =  $modeldata ? $modeldata->count() : Unit::count();
                $unitCount =  $modeldata ? $modeldata->count() : Unit::count();
                return $unitCount;
            case 'Units Leased':
                $data = [];
                $unitCount = $modeldata ? $modeldata->count() : Unit::count();
                $leaseCount = Lease::count();
                $percentage = ($unitCount > 0) ? round(($leaseCount / $unitCount) * 100) : 0;
                // Define the data structure for 'lease' card
                $data = [
                    'modelCount' => $unitCount,
                    'modeltwoCount' => $leaseCount,
                    'percentage' => $percentage,
                ];
                //   $data = compact('unitCount', 'leaseCount', 'percentage');
                return $data;
            case 'No of Tenants':
                $users = User::with('roles')->get();
                $tenantUsers = $users->filter(function ($user) {
                    return $user->hasRole('Tenant');
                });
                $tenantCount = $tenantUsers->count();
                return $tenantCount;
            case 'Vacant Units':
                $unitsWithoutLeasesCount = Unit::doesntHave('lease')->count();
                return $unitsWithoutLeasesCount;
        }
    }



    public function property()
    {
        return $this->belongsTo(Property::class);
    }


    public function lease()
    {
        return $this->hasOne(Lease::class, 'unit_id');
    }
    /**
     * The users that belong to the unit.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'unit_user')
            ->withPivot('property_id')
            ->withTimestamps();
    }

    public function settings()
    {
        return $this->morphMany(Setting::class, 'settingable');
    }

    public function unitSupervisors()
    {
        return $this->belongsToMany(User::class, 'unit_user', 'unit_id', 'user_id')
            ->withTimestamps();
    }

    ////////// view all units and properties for superadmin
    public static function viewallunits()
    {
        $units = static::with('property', 'lease')->get();

        return $units->groupBy('property_id')->map(function ($propertyUnits) {
            return $propertyUnits;
        });
    }

    public function unitdetails()
    {
        return $this->hasMany(UnitDetail::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function unitcharges()
    {
        return $this->hasMany(Unitcharge::class);
    }

    public function meterReadings()
    {
        return $this->hasMany(MeterReading::class);
    }
    public function deposits()
    {
        return $this->hasMany(Deposit::class);
    }
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function audit()
    {
        return $this->morphMany(Audit::class, 'auditable');
    }

    public function getIdentifier()
    {
        return $this->unit_number;
    }


    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(150)
            ->height(150)
            ->sharpen(10)
            ->performOnCollections('unit-photo');
    }
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('unit-photo')
        ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif']);
        //add options
    
        
    }
}
