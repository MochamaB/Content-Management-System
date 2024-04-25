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
        'chartofaccount_id',
        'name',
        'model_type',
        'model_id',
        'referenceno',
        'description',
        'totalamount',
        'status',
        'duedate',

    ];

    public static $validation = [
        'property_id' => 'required',
        'unit_id' => 'nullable',
        'chartofaccount_id' => 'required',
        'name' => 'required',
        'model_type' => 'required',
        'model_id' => 'required',
        'description' => 'nullable',
        'totalamount' => 'required',
        'status' => 'required',
        'duedate' => 'required|date',
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
    public function accounts()
    {
        return $this->belongsTo(Chartofaccount::class, 'chartofaccount_id');
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
}
