<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\FilterableScope;


class Expense extends Model
{
    use HasFactory,FilterableScope;
    protected $table = 'expenses';
    protected $fillable = [
        'property_id',
        'unit_id',
        'model_type',
        'model_id',
        'referenceno',
        'name',
        'totalamount',
        'status',
        'duedate',

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
        return $this->hasMany(ExpenseItems::class);
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

    // Define creating event to generate reference number
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($expense) {
            // Get the last expense ID
            $lastExpense = Expense::latest('id')->first();
            $lastId = $lastExpense ? $lastExpense->id + 1 : 1; // Increment the last ID or start from 1

           // Determine the length of the invoice ID
           $IdLength = strlen((string) $lastId);

           // Determine how many zeros to pad
           $paddingLength = max(0, 3 - $IdLength);
           $Id = str_repeat('0', $paddingLength) . $lastId;

            // Construct the reference number
            $doc = 'EXP-';
            $propertyInitials = $expense->initials;
          //  $propertyNumber = 'P' . str_pad($expense->property_id, 2, '0', STR_PAD_LEFT);
            $unit = Unit::find($expense->unit_id);
            $unitNumber = $unit ? $unit->unit_number : 'N';
            $date = now()->format('ymd');

            // Assign the reference number to the expense model
            $expense->referenceno = $doc . '-' . $propertyInitials . $unitNumber. '-'. $Id ;
        });
    }
}
