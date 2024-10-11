<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Traits\FilterableScope;
use App\Traits\SoftDeleteScope;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Property extends Model implements HasMedia, Auditable
{
    use HasFactory, InteractsWithMedia, FilterableScope, SoftDeletes, SoftDeleteScope, AuditableTrait;
    protected $table = 'properties';
    protected $fillable = [
        'property_name',
        'property_type',
        'property_location',
        'property_streetname',
        'property_status',

    ];
    protected $relationships = [
        'units',
        'leases',
        'utilities',
        'payments',
        'paymentMethods',
        'tickets',
        'expenses',
        'deposits'
    ];
    ////////// FIELDS FOR CREATE AND EDIT METHOD
    public static $fields = [
        'property_type' => ['label' => 'Property Type', 'inputType' => 'selectgroup', 'required' => true, 'readonly' => true],
        'property_name' => ['label' => 'Property Name', 'inputType' => 'text', 'required' => true, 'readonly' => ''],
        'property_location' => ['label' => 'Location', 'inputType' => 'text', 'required' => true, 'readonly' => ''],
        'property_streetname' => ['label' => 'Street Address', 'inputType' => 'text', 'required' => true, 'readonly' => ''],



        // Add more fields as needed
    ];

    public static $validation = [
        'property_type' => 'required',
        'property_name' => 'required',
        'property_location' => 'required',
        'property_streetname' => 'required',

    ];

    protected $auditInclude = [
        'property_name',
        'property_type',
        'property_location',
        'property_streetname',
        'property_status',
        // Add other attributes you want to audit here.
    ];
    protected $auditThreshold = 10;
    public function transformAudit(array $data): array
    {
        $data['property_id'] = $this->id;
    
        return $data;
    }



    public static $headingAfterField = 'property_name';
    public static $additionalHeading = 'New Property';

    public static function getFieldData($field)
    {
        switch ($field) {

            case 'property_type':
                $propertytype = PropertyType::all();
                $propertytypes = $propertytype->groupBy('property_category');
                $data = []; // Initialize $data as an empty array
                foreach ($propertytypes as $category => $propertytype) {
                    $data[$category] = $propertytype->pluck('property_type', 'id')->toArray();
                }
                return $data;
        }
    }

    ///// Data for populating cards
    /////Card options
 

    public function getRelationships()
    {
        return $this->relationships;
    }
    
    /**
     * The amenities that belong to the property.
     */

     
    public function propertyType()
    {
        return $this->belongsTo(PropertyType::class, 'property_type');
    }

    public function amenities()
    {
        return $this->belongsToMany(Amenity::class, 'properties_amenities');
    }

    public function utilities()
    {
        return $this->hasMany(Utility::class);
    }

    public function events()
    {
        return $this->hasMany(Audit::class);
    }

    public function units()
    {
        return $this->hasMany(Unit::class);
    }

    public function leases()
    {
        return $this->hasMany(Lease::class);
    }
    public function users()
    {
        return $this->belongsToMany(User::class, 'unit_user', 'property_id', 'user_id');
    }
    

    public function settings()
    {
        return $this->morphMany(Setting::class, 'model');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
    public function paymentMethods()
    {
        return $this->hasMany(PaymentMethod::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function deposits()
    {
        return $this->hasMany(Deposit::class);
    }


    public function meterReadings()
    {
        return $this->hasMany(MeterReading::class);
    }


    /// scope showing properties with units
    public function scopeWithUnitUser($query)
    {
        $user = auth()->user();
        if ($user->id !== 1) {
            $userUnits = $user->units;
            $propertyIds = $userUnits->pluck('pivot.property_id')->unique();

            // Retrieve properties based on the extracted property_ids
            return $query->whereIn('id', $propertyIds);
        }
    }

    ///Spatie Media conversions
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(368)
            ->height(232)
            ->sharpen(10);
    }
}
