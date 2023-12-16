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
        'model_type',
        'model_id',
        'referenceno',
        'invoice_type',
        'totalamount',
        'status',
        'duedate',

    ];

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
