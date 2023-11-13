<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Unitcharge;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

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
        ///1. GET UNITS WITH RECURRING CHARGE
        $unitcharges = Unitcharge::where('recurring_charge', 'yes')
                    ->where('parent_id',null)    
                    ->get();

        foreach ($unitcharges as $unitcharge) {
                // Create invoice items from invoice service app/Services/InvoiceService
            $this->invoiceService->generateInvoice($unitcharge);

                // Update the nextdate based on charge_cycle logic
          //      $this->updateNextDate($unitcharge);
          //  }
        }
        return redirect()->back()->with('status', 'Sucess Invoice generated.');
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
