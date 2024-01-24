<?php

namespace App\Http\Middleware;

use App\Models\Invoice;
use App\Models\Property;
use App\Models\Unit;
use Closure;
use Illuminate\Http\Request;

class CheckCreateVariables
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
        $properties = Property::all();
        $units = Unit::with('property', 'lease')->get();
        $invoices = Invoice::all();
        $routeName = $request->route()->getName();
       // dd($routeName);

        if ($request->route('id')) {
            // 'id' is present, proceed with the regular execution
            return $next($request);
        } else {

            // 'id' is not present, load dynamic view
            return response()->view('admin.CRUD.select', compact('properties','units','routeName'));
        }
    }
}
