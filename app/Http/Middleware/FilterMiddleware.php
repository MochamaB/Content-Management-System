<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FilterMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
       // Check if the request has any filter parameters
       if (!$request->has('from_date') && !$request->has('to_date')) {
        $now = Carbon::now();

        // Subtract 3 months from the current date
        $three_months_ago = $now->subMonths(3);

        // Format the dates as Y-m-d
        $from_date = $three_months_ago->format('Y-m-d');
        $to_date = $now->format('Y-m-d');

        // Set the default values for the from-date and to-date inputs
        $request->merge(['from_date' => $from_date, 'to_date' => $to_date]);
    }

        return $next($request);
    }

    
}
