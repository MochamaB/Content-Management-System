<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Traits\FilterableScope;


class Invoice extends Model
{
    use HasFactory,FilterableScope;
    protected $table = 'invoices';
    protected $fillable = [
        'property_id',
        'unit_id',
        'unitcharge_id',
        'model_type',
        'model_id',
        'referenceno',
        'type',
        'totalamount',
        'status',
        'duedate',

    ];

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

    public function scopeApplyFilters($query, $filters)
    {
        
        foreach ($filters as $column => $value) {
            if (!empty($value)) {
                if (!empty($filters['from_date']) && !empty($filters['to_date'])) {
                    $query->whereBetween('created_at', [$filters['from_date'], $filters['to_date']]);
                } else {
                    // Use where on the other columns
                    $query->where($column, $value);
                }
            }
        }
       // Add default filter for the last two months
       if (empty($filters['from_date']) && empty($filters['to_date'])) {
        $query->where("created_at", ">", Carbon::now()->subMonths(4));
    }

        return $query;
    }
}
