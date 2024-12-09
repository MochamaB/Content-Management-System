<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Traits\FilterableScope;


class Invoice extends Model
{
    use HasFactory, FilterableScope;
    protected $table = 'invoices';

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
        self::STATUS_ARCHIVED => 'Archived',
    ];
    protected $fillable = [
        'property_id',
        'unit_id',
        'unitcharge_id',
        'model_type',
        'model_id',
        'referenceno',
        'name',
        'totalamount',
        'status',
        'duedate',

    ];

     // Define default values for when the invoice is deleted or not found


    ////////// FIELDS FOR CREATE AND EDIT METHOD
    public static $fields = [
        'property_id' => ['label' => 'Property', 'inputType' => 'select', 'required' => true, 'readonly' => true],
        'unit_id' => ['label' => 'Unit', 'inputType' => 'select', 'required' => true, 'readonly' => true],
        'model_id' => ['label' => 'Tenant Name', 'inputType' => 'select', 'required' => true, 'readonly' => ''],
        'type' => ['label' => 'Invoice Type', 'inputType' => 'select', 'required' => true, 'readonly' => ''],
        'status' => ['label' => 'Status', 'inputType' => 'select', 'required' => false, 'readonly' => ''],
        'startdate' => ['label' => 'Start Date', 'inputType' => 'date', 'required' => true, 'readonly' => ''],
        'enddate' => ['label' => 'End Date', 'inputType' => 'date', 'required' => true, 'readonly' => ''],


        // Add more fields as needed
    ];


    public static function getFieldData($field)
    {
        $invoice = Invoice::with('property', 'unit', 'model')->get();
        switch ($field) {
            case 'property_id':
                // Retrieve the supervised units' properties
                $properties = Property::pluck('property_name')->toArray();
                //  $properties = Property::pluck('property_name')->toArray();
                return $properties;
            case 'unit_id':
                // Retrieve the supervised units' properties
                $units = $invoice->pluck('unit.unit_number', 'unit.id')->toArray();
                return $units;
            case 'model_id':
                //  $modelClass = get_class($invoice->model);
                $tenants = User::selectRaw('CONCAT(firstname, " ", lastname) as full_name, id')
                    ->pluck('full_name', 'id')
                    ->toArray();
                return  $tenants;
            case 'type':
                $distinctInvoiceTypes = Invoice::distinct('type')->pluck('invoice_type');
                return  $distinctInvoiceTypes;
            case 'status':
                return [
                    'paid' => 'Paid',
                    'unpaid' => 'Unpaid',
                    'Over Due' => 'Over Due',
                    'partially_paid' => 'Partially Paid',

                ];
                // Add more cases for additional filter fields
            default:
                return [];
        }
    }
    /// GET THE STATUS OF THE INVOICE
    public function getStatusLabel()
    {
    return self::$statusLabels[$this->status] ?? 'Unknown Status';
    }

    ////// PoLymorphism relationship (Can be Either User, Vendor or Supplier)
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


    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItems::class);
    }

    public function getItems()
    {
        return $this->hasMany(InvoiceItems::class);
    }

    public function transactions()
    {
        return $this->morphMany(Transaction::class, 'transactionable');
    }

    //// Polymorphic Relationship with Payments Model.
    public function payments()
    {
        return $this->morphMany(Payment::class, 'model');
    }

    public function paymentMethod()
    {
        return $this->hasOne(PaymentMethod::class, 'property_id', 'property_id');
    }

  

    //// for the email view
    public function getTransactions()
    {
        $unitchargeId = $this->invoiceItems->pluck('unitcharge_id')->first();
        $sixMonths = now()->subMonths(6);
        return Transaction::where('created_at', '>=', $sixMonths)
            ->where('unit_id', $this->unit_id)
            ->where('unitcharge_id', $unitchargeId)
            ->get();
    }
    public function getGroupedInvoiceItems()
    {
        return $this->getTransactions()->groupBy('unitcharge_id');
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

    /// Creating the Reference Number
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            // Get the last expense ID
            $lastInvoice = Invoice::latest('id')->first();
            $lastId = $lastInvoice ? $lastInvoice->id + 1 : 1; // Increment the last ID or start from 1

            // Determine the length of the invoice ID
            $IdLength = strlen((string) $lastId);

            // Determine how many zeros to pad
            $paddingLength = max(0, 3 - $IdLength);
            $Id = str_repeat('0', $paddingLength) . $lastId;

            // Construct the reference number
            $doc = 'INV';
            $property = Property::find($invoice->property_id);  // Fetch the property record
            // Call the accessor to get the initials
            $propertyInitials = $invoice->initials;
            $unit = Unit::find($invoice->unit_id);
            $unitNumber = $unit ? $unit->unit_number : 'N';

            // Assign the reference number to the expense model
            $invoice->referenceno = $doc . '-' . $propertyInitials . $unitNumber. '-'. $Id ;
        });
    }
}
