<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Transaction extends Model
{
    use HasFactory;
    protected $table = 'transactions';
    protected $fillable = [
        'property_id',
        'unit_id',
        'unitcharge_id',
        'charge_name',
        'transactionable_id',
        'transactionable_type',
        'description',
        'debitaccount_id',
        'creditaccount_id',
        'amount',
    ];

    public function transactionable()
    {
        return $this->morphTo();
    }

    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    public function units()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }
    public function debitAccount()
    {
        return $this->belongsTo(Chartofaccount::class, 'debitaccount_id');
    }

    public function creditAccount()
    {
        return $this->belongsTo(Chartofaccount::class, 'creditaccount_id');
    }

    public function scopeApplyFilters($query, $filters)
    {
        $fromDate = $filters['from_date'] ?? null;
        $toDate = $filters['to_date'] ?? null;

        // Apply date range filters
        if (!empty($fromDate) && !empty($toDate)) {
            $query->whereBetween('created_at', [$fromDate, $toDate]);
        } elseif (empty($fromDate) && empty($toDate)) {
            // Add default filter for the last four months
            $query->where("created_at", ">", Carbon::now()->subMonths(4));
        }
        // Apply filters for other columns
        foreach ($filters as $column => $value) {
            if (!in_array($column, ['from_date', 'to_date']) && !empty($value)) {
                $query->where($column, $value);
            }
        }

        return $query;

        
    }
}
