<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Unitcharge;
use App\Models\Unit;
use App\Models\Lease;
use App\Models\MeterReading;
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
        $unitCharges = Unitcharge::where('recurring_charge', 'yes')
            // $unitCharges = Unitcharge::where('charge_name', 'rent')
            //  ->where('nextdate', '<=', now()) // Check nextdate for generating invoices
            ->get();
        foreach ($unitCharges as $item) {
            $items = $item;
        }
        $children = $unitCharges->children;
        //   $parent = $unitCharges->parent;
        dd($children);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return View('admin.lease.invoice');
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
        $unitcharges = Unitcharge::where('recurring_charge', 'yes')
                    ->where('parent_id',null)    
                    ->get();

              

        foreach ($unitcharges as $unitcharge) {
            // Check if it's time to generate invoice
           
              
         //   if ($this->isTimeToGenerateInvoice($unitcharge)) {
                $invoiceData = $this->invoiceData($unitcharge);
                $invoice = $this->createInvoice($invoiceData);

                // Create invoice items
                $this->createInvoiceItems($invoice, $unitcharge);

                // Update the nextdate based on charge_cycle logic
          //      $this->updateNextDate($unitcharge);
          //  }
        }
        return redirect()->back()->with('status', 'Charge Name already defined in system.');
    }

    ///////Check time
    private function isTimeToGenerateInvoice($unitcharge)
    {
        $nextDate = Carbon::parse($unitcharge->nextdate);
        return Carbon::now()->isSameDay($nextDate);
    }

    private function invoiceData($unitcharge)
    {
        $today = Carbon::now();
        $invoicenodate = $today->format('ym');
        $unitnumber = Unit::where('id', $unitcharge->unit_id)->first();
        $user = Lease::where('unit_id', $unitcharge->unit_id)->first();

        return [
            'property_id' => $unitcharge->property_id,
            'unit_id' => $unitcharge->unit_id,
            'user_id' => $user->user_id,
            'referenceno' => $invoicenodate . $unitnumber->unit_number,
            'invoice_type' => $unitcharge->charge_name,
            'totalamount' => null,
            'status' => 'unpaid',
            'duedate' => null,
        ];
    }

    private function createInvoice($data)
    {
        return Invoice::create($data);
    }

    

    private function createInvoiceItems($invoice, $unitcharge)
    {
        if ($unitcharge->charge_type === 'units') {
            $nextdateFormatted = Carbon::parse($unitcharge->nextdate)->format('Y-m-d');
            $updatedFormatted = Carbon::parse($unitcharge->updated_at ?? Carbon::now())->format('Y-m-d');
          
            $amount = 0.00; 
            $meterReadings = MeterReading::where('unit_id', $unitcharge->unit_id)
            ->where('unitcharge_id',$unitcharge->id)
            ->where('startdate', '>=', $updatedFormatted) // Check readings after updated_at
            ->where('startdate', '<=', $nextdateFormatted) // Check readings before or equal to nextdate
            ->get();
              
            foreach ($meterReadings as $reading) {
                // Calculate the amount based on meter readings and assign it to $amount
                $amount = $reading->amount;
                    }
                } else {
                    // If charge_type is not 'units', use the unitcharge rate as the amount
                    $amount = $unitcharge->rate;
                }
        // Create invoice items
        InvoiceItems::create([
            'invoice_id' => $invoice->id,
            'unitcharge_id' => $unitcharge->id,
            'chartofaccount_id' => $unitcharge->chartofaccounts_id,
            'charge_name' => $unitcharge->charge_name,
            'description' => '',
            'amount' => $amount,
        ]);

       
            $childcharges = Unitcharge::where('parent_id', $unitcharge->id)->get();
            if ($childcharges->count() > 0) {
            // Create invoice items for child charges
            foreach ($childcharges as $childcharge) {
                InvoiceItems::create([
                    'invoice_id' => $invoice->id,
                    'unitcharge_id' => $childcharge->id,
                    'chartofaccount_id' => $childcharge->chartofaccounts_id,
                    'charge_name' => $childcharge->charge_name,
                    'description' => '',
                    'amount' => $childcharge->rate,
                ]);
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
