<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Traits\FilterableScope;

class Property extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, FilterableScope;
    protected $table = 'properties';
    protected $fillable = [
        'property_name',
        'property_type',
        'property_location',
        'property_streetname',
        'property_status',

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

    /////Filter options
    public static $filter = [
        'property_location' => 'Type',
        'property_streetname' => 'Location',
        'property_status' => 'Status',
        // Add more filter fields as needed
    ];



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
    public static $card = [
        'All Properties' => 'information',
        'Total Units' => 'detail',
        'All Utilities' => 'information',
        'No of Tenants' => 'detail',
        // Add more cards as needed
    ];

    public static function getCardData($card)
    {
        switch ($card) {

            case 'All Properties':
                $propertyCount = Property::count();
                return $propertyCount;
            case 'Total Units':
                $unitCount = Unit::count();
                return $unitCount;
            case 'All Utilities':
                $utilityCount = Utility::count();
                return $utilityCount;
            case 'No of Tenants':
                $users = User::with('roles')->get();
                $tenantUsers = $users->filter(function ($user) {
                    return $user->hasRole('Tenant');
                });
                $tenantCount = $tenantUsers->count();
                return $tenantCount;
        }
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
