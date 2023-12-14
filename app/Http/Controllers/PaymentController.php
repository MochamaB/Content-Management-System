<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Unit;
use App\Models\Property;
use App\Models\Invoice;
use App\Models\PaymentType;
use Carbon\Carbon;

class PaymentController extends Controller
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
    public function create($id = null)
    {
        $invoice = Invoice::find($id);
        $unit = Unit::find($invoice->unit_id);
        // session(['unit' => $unit]);
        $property = Property::where('id', $invoice->unit_id)->first();  
        $paymenttype = PaymentType::all();
        $className = get_class($invoice);

        ////REFNO
        $today = Carbon::now();
        $invoicenodate = $today->format('ym');
        $unitnumber = $unit->unit_number;
        $referenceno = 'RCT-'.$invoice->id.'-'.$invoicenodate . $unitnumber;
      //  dd($referenceno);
        return View('admin.lease.payment',compact('unit','property','paymenttype','invoice','className','referenceno'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
