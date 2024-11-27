<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Traits\MediaUpload;
use App\Traits\FilterableScope;
use App\Traits\SoftDeleteScope;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Deposit extends Model implements HasMedia, Auditable
{
    use HasFactory, InteractsWithMedia, MediaUpload,FilterableScope, SoftDeleteScope, SoftDeletes, AuditableTrait;
    protected $table = 'deposits';

    const STATUS_PAID = 1;
    const STATUS_UNPAID = 2;
    const STATUS_PARTIALLY_PAID= 3;
    const STATUS_OVER_PAID = 4;
    const STATUS_VOID = 5;
    const STATUS_ARCHIVED = 6;

    public static $statusLabels = [
        self::STATUS_PAID => 'Paid',
        self::STATUS_UNPAID => 'Unpaid',
        self::STATUS_PARTIALLY_PAID => 'Partially Paid',
        self::STATUS_OVER_PAID => 'Over Paid',
        self::STATUS_VOID => 'Void',
        self::STATUS_VOID => 'Archived',
    ];
    protected $fillable = [
        'property_id',
        'unit_id',
        'model_type',
        'model_id',
        'referenceno',
        'name',
        'totalamount',
        'status',
        'duedate'

    ];

    public static $validation = [
        'property_id' => 'required',
        'unit_id' => 'nullable',
        'model_type' => 'required',
        'model_id' => 'required',
        'name' => 'required',
        'status' => 'nullable',
        'duedate' => 'nullable|date',
        'chartofaccount_id' => 'required',
        'description' => 'required',
        'amount' => 'required',
    ];

    protected $auditInclude = [
        'property_id',
        'unit_id',
        'model_type',
        'model_id',
        'referenceno',
        'name',
        'totalamount',
        'status',
        'duedate'
        // Add other attributes you want to audit here.
    ];
    protected $auditThreshold = 20;

    public function transformAudit(array $data): array
    {
        $data['property_id'] = $this->property_id;
        $data['unit_id'] = $this->unit_id;
    
        return $data;
    }

    public function model()
    {
        return $this->morphTo();
    }
    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'model_id')->where('model_type', User::class);
    }

    public function lease()
    {
        return $this->belongsTo(Lease::class, 'unit_id');
    }

    public function transactions()
    {
        return $this->morphMany(Transaction::class, 'transactionable');
    }
    public function payments()
    {
        return $this->morphMany(Payment::class, 'model');
    }

    public function getItems()
    {
        return $this->hasMany(DepositItems::class);
    }

    // Define the inverse relationship of audit
    public function audit()
    {
        return $this->morphMany(Audit::class, 'auditable');
    }

    public function getIdentifier()
    {
        return $this->referenceno;
    }

    public function getInitialsAttribute() {
        if ($this->property) { // Check if the relationship exists
            $words = explode(' ', $this->property->property_name);
            $initials = '';
            foreach ($words as $word) {
                $initials .= strtoupper($word[0]);  // Get the first letter of each word
            }
            return $initials;
        }
    
        return '';  // Return an empty string or some default value if property is null
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($deposit) {
            // Get the last expense ID
            $lastDeposit = Deposit::latest('id')->first();
            $lastId = $lastDeposit ? $lastDeposit->id + 1 : 1; // Increment the last ID or start from 1

           // Determine the length of the invoice ID
           $IdLength = strlen((string) $lastId);

           // Determine how many zeros to pad
           $paddingLength = max(0, 3 - $IdLength);
           $Id = str_repeat('0', $paddingLength) . $lastId;

            // Construct the reference number
            $doc = 'DEP';
            $propertyInitials = $deposit->initials;
         //   $propertyNumber = 'P' . str_pad($deposit->property_id, 2, '0', STR_PAD_LEFT);
            // Load the unit model using the unit_id
            $unit = Unit::find($deposit->unit_id);
            $unitNumber = $unit ? $unit->unit_number : 'N';

            // Assign the reference number to the expense model
            $deposit->referenceno = $doc . '-' . $propertyInitials . $unitNumber. '-'. $Id ;
        });
    }
}
