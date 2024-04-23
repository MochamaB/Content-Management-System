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
        'chartofaccount_id',
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
        'chartofaccount_id' => 'required',
        'model_type' => 'required',
        'model_id' => 'required',
        'name' => 'required',
        'totalamount' => 'required',
        'status' => 'nullable',
        'duedate' => 'nullable|date',
    ];


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

    public function transactions()
    {
        return $this->morphMany(Transaction::class, 'transactionable');
    }
    public function payments()
    {
        return $this->morphMany(Payment::class, 'model');
    }
    public function account()
    {
        return $this->belongsTo(Chartofaccount::class, 'chartofaccount_id');
    }
}
