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
use App\Traits\MediaUpload;

class Property extends Model implements HasMedia, Auditable
{
    use HasFactory, InteractsWithMedia, FilterableScope, SoftDeletes, SoftDeleteScope, AuditableTrait, MediaUpload;
    protected $table = 'properties';
    protected $fillable = [
        'user_id',
        'property_name',
        'property_slogan',
        'property_type',
        'property_location',
        'property_streetname',
        'property_description',
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
        'property_slogan' => ['label' => 'Slogan', 'inputType' => 'text', 'required' => false, 'readonly' => ''],
        'user_id' => ['label' => 'Property Agent / Manager', 'inputType' => 'select', 'required' => false, 'readonly' => ''],
        'property_description' => ['label' => 'Description', 'inputType' => 'textarea', 'required' => false, 'readonly' => ''],



        // Add more fields as needed
    ];

    public static $validation = [
        'property_type' => 'required',
        'property_name' => 'required',
        'property_location' => 'required',
        'property_streetname' => 'required',
        'user_id' => 'nullable',
        'property_slogan' => 'nullable',
        'property_description' => 'nullable',

    ];

    protected $auditInclude = [
        'property_name',
        'property_type',
        'property_location',
        'property_streetname',
        'property_status',
        'user_id',
        'property_slogan',
        'property_description',
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

    public function getSloganAttribute($value)
    {
        return $value ?? 'Spacious units available';
    }

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
            case 'user_id':
                // Retrieve the supervised units' properties
                $users = User::with('units', 'roles')
                    ->visibleToUser()
                    ->excludeTenants()
                    ->get()
                    ->mapWithKeys(function ($user) {
                        return [$user->id => $user->firstname . ' ' . $user->lastname];
                    });
                return $users;
            case 'property_slogan':
                $slogan = 'Where modern style meets comfort.';
                return $slogan;
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

    // Define the inverse relationship of audit
    public function audit()
    {
        return $this->morphMany(Audit::class, 'auditable');
    }
    public function sliders()
    {
        return $this->hasMany(Slider::class, 'property_id');
    }

    public function vendors()
    {
        return $this->hasMany(Vendor::class);
    }
    public function getIdentifier()
    {
        return $this->property_name;
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
            ->width(150)
            ->height(150)
            ->sharpen(10)
            ->performOnCollections('images');

        // Document conversions (for PDFs)
        $this->addMediaConversion('thumb')
            ->performOnCollections('documents')
            ->width(150)
            ->height(150)
            ->format('jpg')
            ->pdfPageNumber(1);

        // Video conversions
        $this->addMediaConversion('thumb')
            ->performOnCollections('videos')
            ->extractVideoFrameAtSecond(1)
            ->width(150)
            ->height(150);
    }
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images');
        $this->addMediaCollection('videos');
        $this->addMediaCollection('documents');

        //add options


    }
}
