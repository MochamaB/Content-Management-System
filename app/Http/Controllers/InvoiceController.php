<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Unitcharge;
use App\Models\Unit;
use App\Services\InvoiceService;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Notifications\InvoiceGeneratedNotification;


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
            ->where('parent_id', null)
            ->get();

        foreach ($unitcharges as $unitcharge) {
            //1. Create invoice items from invoice service app/Services/InvoiceService
            $this->invoiceService->generateInvoice($unitcharge);

            //2. Send Email/Notification to the Tenant containing the invoice.



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
       
      //  dd($invoice->InvoiceItems);
        return View('admin.lease.document_view',compact('invoice'));
    }

    public function createPDF(Invoice $invoice) 
    {
      //  $invoice->load('property');
    //    dd($invoice);
  //  return View('email.invoice',compact('invoice'));
   //   $pdf = PDF::loadView('email.invoice', compact('invoice'));
    //  return $pdf->download('invoice12.pdf');
   //   return $pdf->stream('invoice.pdf');

        $tenant = $invoice->model;
        $tenant->notify(new InvoiceGeneratedNotification($invoice));
        return redirect()->back()->with('status', 'Sucess Invoice generated.');
       
      }

      public function sendInvoice()
      {

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
