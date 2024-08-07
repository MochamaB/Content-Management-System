<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\FilterableScope;

class Deposit extends Model
{
    use HasFactory, FilterableScope;
    protected $table = 'deposits';
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
            $doc = 'DEP-';
            $propertyNumber = 'P' . str_pad($deposit->property_id, 2, '0', STR_PAD_LEFT);
            // Load the unit model using the unit_id
            $unit = Unit::find($deposit->unit_id);
            $unitNumber = $unit ? $unit->unit_number : 'N';

            // Assign the reference number to the expense model
            $deposit->referenceno = $doc . '-' . $Id . '-' . $propertyNumber . $unitNumber;
        });
    }
}
