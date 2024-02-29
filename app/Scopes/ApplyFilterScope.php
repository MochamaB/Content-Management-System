<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Carbon\Carbon;


class ApplyFilterScope implements Scope
{ 
    public function apply(Builder $builder, Model $model)
    {
        $request = request();

        $builder->where(function ($query) use ($request) {

            $filters = $request->except(['tab','_token','_method']);

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
                $query->where("created_at", ">", Carbon::now()->subMonths(2));
            }
        });
    }
}
