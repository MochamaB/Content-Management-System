<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Unitcharge;
use App\Models\Unit;
use App\Models\InvoiceItems;
use Illuminate\Http\Request;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $unitCharges = Unitcharge::where('recurring_charge', 'Yes')
      //  ->where('nextdate', '<=', now()) // Check nextdate for generating invoices
        ->get();

        dd($unitCharges);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $unitCharges = Unitcharge::where('recurring_charge', 'yes')
        ->where('nextdate', '<=', now()) // Check nextdate for generating invoices
        ->get()->dd();
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
    }
    public function generateInvoice(Request $request)
    {
        $unitcharges = Unitcharge::where('recurring_charge', 'yes')->get();

        foreach ($unitcharges as $unitcharge) {
            // Check if it's time to generate invoice
            $nextDate = Carbon::parse($unitcharge->nextdate);
            $invoicenodate = Carbon::parse($nextDate)->format('ym');
            $unitnumber = Unit::where('unit_id',$unitcharge->unit_id)->first();
            ///
            if($unitcharge->charge_type !== 'fixed')

            if (Carbon::now()->gte($nextDate)) {
                // Create invoice header
                $invoice = Invoice::create([
                    'property_id' => $unitcharge->property_id,
                    'unit_id' => $unitcharge->unit_id,
                    'referenceno' =>$invoicenodate.$unitnumber, // generate reference number logic,
                    'invoice_type' => 'Utilities', // specify invoice type,
                    'totalamount' => '',// calculate total amount,
                    'status' => 'unpaid', // or any default status
                    'duedate' => '',// calculate due date based on your logic,
                ]);

                // Create invoice items////
                InvoiceItems::create([
                    'invoice_id' => $invoice->id,
                    'unitcharge_id' => $unitcharge->id,
                    'chartofaccount_id' => $unitcharge->chartofaccounts_id, // specify chart of account id,
                    'charge_name' =>$unitcharge->charge_name, // specify charge name,
                    'description' =>'', // specify description,
                    'amount' =>$unitcharge->rate, // specify amount,
                ]);

                // Update the nextdate based on charge_cycle logic
                $nextDate->addMonths($unitcharge->charge_cycle); // or any other logic based on your requirements

                $unitcharge->update(['nextdate' => $nextDate]);
            }
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function show(Invoice $invoice)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function edit(Invoice $invoice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Invoice $invoice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function destroy(Invoice $invoice)
    {
        //
    }
}
