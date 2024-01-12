<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    protected $table = 'invoices';
    protected $fillable = [
        'property_id',
        'unit_id',
        'unitcharge_id',
        'model_type',
        'model_id',
        'referenceno',
        'invoice_type',
        'totalamount',
        'status',
        'duedate',

    ];

    ////////// FIELDS FOR CREATE AND EDIT METHOD
    public static $fields = [
        'property_id' => ['label' => 'Property', 'inputType' => 'select', 'required' => true, 'readonly' => true],
        'unit_id' => ['label' => 'Unit', 'inputType' => 'select', 'required' => true, 'readonly' => true],
        'model_id' => ['label' => 'Tenant Name', 'inputType' => 'select', 'required' => true, 'readonly' => ''],
        'invoice_type' => ['label' => 'Invoice Type', 'inputType' => 'select', 'required' => true, 'readonly' => ''],
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
                $properties = $invoice->pluck('property.property_name', 'property.id')->toArray();
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
            case 'invoice_type':
                $distinctInvoiceTypes = Invoice::distinct('invoice_type')->pluck('invoice_type');
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
          return $this->belongsTo(Property::class,'property_id');
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
}
