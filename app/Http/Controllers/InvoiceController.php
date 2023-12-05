<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Unitcharge;
use App\Models\Unit;
use App\Models\Transaction;
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
        $pageheadings = collect([
            '0' => $invoice->unit->unit_number,
            '1' => $invoice->unit->property->property_name,
            '2' => $invoice->unit->property->property_streetname,
        ]);
        $tabTitles = collect([
            'Overview',
            'Statement',
        ]);

        /// Data for the Account Statement
        $sixMonths = now()->subMonths(6);
        $transactions = Transaction::where('created_at', '>=', $sixMonths)
            ->where('unit_id', $invoice->unit_id)
            ->where('charge_name', $invoice->invoice_type)
            ->get();

        ////Opening Balance
        $openingBalance = $this->calculateOpeningBalance($invoice);

        $tabContents = [];
        foreach ($tabTitles as $title) {
            if ($title === 'Overview') {
                $tabContents[] = View('admin.lease.invoice_view', compact('invoice'))->render();
            } elseif ($title === 'Statement') {
                $tabContents[] = View('admin.lease.statement_view', compact('invoice', 'transactions','openingBalance'))->render();
            }
        }

       // dd($openingBalance);
        return View('admin.CRUD.form', compact('pageheadings', 'tabTitles', 'tabContents'));
    }

    public function calculateOpeningBalance(Invoice $invoice)
    {
        // Get the date 6 months ago from today
        $sixMonthsAgo = now()->subMonths(6);

        // Calculate the sum of invoice amounts
        $invoiceAmount = Transaction::where('created_at', '<', $sixMonthsAgo)
            ->where('unit_id', $invoice->unit_id)
            ->where('charge_name', $invoice->invoice_type)
            ->where('transactionable_type', 'App\Models\Invoice')
            ->sum('amount');

        // Calculate the sum of payment amounts
        $paymentAmount = Transaction::where('created_at', '<', $sixMonthsAgo)
            ->where('unit_id', $invoice->unit_id)
            ->where('charge_name', $invoice->invoice_type)
            ->where('transactionable_type', 'App\Models\Payment')
            ->sum('amount');

        // Calculate the opening balance
        $openingBalance = $invoiceAmount - $paymentAmount;

        return $openingBalance;
    }
    

    public function sendInvoice(Invoice $invoice)
    {
        //  $invoice->load('property');
        //    dd($invoice);
        //  return View('email.invoice',compact('invoice'));
        //   $pdf = PDF::loadView('email.invoice', compact('invoice'));
        //  return $pdf->download('invoice12.pdf');
        //   return $pdf->stream('invoice.pdf');

        $user = $invoice->model;
        $user->notify(new InvoiceGeneratedNotification($invoice,$user));
        return redirect()->back()->with('status', 'Sucess Invoice generated.');
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
