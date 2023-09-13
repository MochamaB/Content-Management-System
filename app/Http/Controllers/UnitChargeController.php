<?php

namespace App\Http\Controllers;

use App\Models\unitcharge;
use Illuminate\Http\Request;

class UnitChargeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'property_id' => 'required', 
            'unit_id' => 'required|numeric', 
            'chartofaccounts_id' => 'required', 
            'charge_name' => 'required',
            'charge_cycle' => 'required',
            'charge_type' => 'required',
            'rate' => 'required',
            'parent_utility' => 'required',
            'recurring_charge' => 'nullable|',
            'startdate' => 'nullable|date',
            'nextdate' => 'nullable|date',       
        ]);

        if(empty($request->session()->get('charge'))){
            $charge = new Unitcharge();
            $charge->fill($validatedData);
            $request->session()->put('charge', $charge);
         //   $charge->save();
        }
        else{
            $charge = $request->session()->get('charge');
            $charge->fill($validatedData);
            $request->session()->put('charge', $charge);
         //   $charge->update();
        }

        $splitCharges = [];

        foreach ($request->input('splitcharge_name') as $index => $chargeName) {
            $splitCharge = new Unitcharge([
                'property_id' => $charge->property_id, 
                'unit_id' => $charge->unit_id, 
                'chartofaccounts_id' => $request->input('splitchartofaccounts_id'), 
                'charge_name' => $chargeName,
                'charge_cycle' => $charge->charge_cycle,
                'charge_type' => $request->input('splitcharge_type'),
                'rate' => $request->input('splitrate'),
                'parent_utility' => $charge->id,
                'recurring_charge' => $charge->recurring_charge,
                'startdate' => $charge->startdate,
                'nextdate' => $charge->nextdate,
                // ... Other fields ...
            ]);

            $splitCharge->load('chartofaccounts');

            $splitCharges[] = $splitCharge;
        }
    
        // Save the split rent charge data to the database
    //    Unitcharge::insert($splitCharges);
        $request->session()->put('splitcharges', $splitCharges);


        return redirect()->route('lease.create', ['active_tab' => '3'])
        ->with('status', 'Rent Assigned Successfully. Enter Security Deposit Details');
       
    }
    

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\unitcharge  $unitcharge
     * @return \Illuminate\Http\Response
     */
    public function show(unitcharge $unitcharge)
    {
        

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\unitcharge  $unitcharge
     * @return \Illuminate\Http\Response
     */
    public function edit(unitcharge $unitcharge)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\unitcharge  $unitcharge
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, unitcharge $unitcharge)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\unitcharge  $unitcharge
     * @return \Illuminate\Http\Response
     */
    public function destroy(unitcharge $unitcharge)
    {
        //
    }
}
