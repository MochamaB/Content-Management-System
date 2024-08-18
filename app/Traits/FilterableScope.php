<?php

namespace App\Traits;

use Carbon\Carbon;

trait FilterableScope
{
    public function scopeApplyDateFilters($query, $filters)
    {
        $fromDate = $filters['from_date'] ?? null;
        $toDate = $filters['to_date'] ?? null;

        // Apply date range filters
        if (!empty($fromDate) && !empty($toDate)) {
            $query->whereBetween('created_at', [$fromDate, $toDate]);
        } elseif (empty($fromDate) && empty($toDate)) {
            // Add default filter for the last four months
            $query->where("created_at", ">", Carbon::now()->subMonths(3));
        }

        // Apply filters for other columns
        foreach ($filters as $column => $value) {
            if (!in_array($column, ['from_date', 'to_date']) && !empty($value)) {
                $query->where($column, $value);
            }
        }

        return $query;
    }

    public function scopeApplyFilters($query, $filters)
    {
       
        // Apply filters for other columns
        foreach ($filters as $column => $value) {
            if (!in_array($column, ['from_date', 'to_date']) && !empty($value)) {
                $query->where($column, $value);
            }
        }

        return $query;
    }

    public function scopeApplyDateOnlyFilters($query, $filters)
    {
        $fromDate = $filters['from_date'] ?? null;
        $toDate = $filters['to_date'] ?? null;

        // Apply date range filters
        if (!empty($fromDate) && !empty($toDate)) {
            $query->whereBetween('created_at', [$fromDate, $toDate]);
        } elseif (empty($fromDate) && empty($toDate)) {
            // Add default filter for the last four months
            $query->where("created_at", ">", Carbon::now()->subMonths(1));
        }

        return $query;
    }
}