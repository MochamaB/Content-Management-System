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
use App\Services\TableViewDataService;
use App\Models\WebsiteSetting;
use App\Traits\FormDataTrait;


class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    use FormDataTrait;
    protected $controller;
    protected $model;
    private $invoiceService;
    private $tableViewDataService;


    public function __construct(InvoiceService $invoiceService,TableViewDataService $tableViewDataService)
    {
        $this->model = Invoice::class;
        $this->controller = collect([
            '0' => 'invoice', // Use a string for the controller name
            '1' => 'New Invoice',
        ]);
        $this->invoiceService = $invoiceService;
        $this->tableViewDataService = $tableViewDataService;
    }

    public function getInvoiceData($invoicedata)
    {
        $sitesettings = WebsiteSetting::first();

        /// TABLE DATA ///////////////////////////
        $tableData = [
            'headers' => ['REFERENCE NO', 'INVOICE DATE','UNIT DETAILS', 'TYPE', 'TENANT', 'AMOUNT DUE', 'PAID','BALANCE', 'ACTIONS'],
            'rows' => [],
        ];

        foreach ($invoicedata as $item) {
            //// Status Classes for the Invoices////////
            $statusClasses = [
                'paid' => 'active',
                'unpaid' => 'warning',
                'Over Due' => 'danger',
                'partially_paid' => 'information',
            ];
            //// GET INVOICE STATUS. IF STATUS UNPAID AND DUEDATE
            $today = Carbon::now();
            $totalPaid = $item->payments->sum('totalamount');
            $balance = $item->totalamount - $totalPaid;
            $payLink = ''; // Initialize $payLink

            if ($item->payments->isEmpty()) {
                $status = 'unpaid';
                $payLink = '<a href="' . route('payment.create', ['id' => $item->id]) . '" class="badge badge-information"  style="float: right; margin-right:10px">Add Payment</a>';
            } elseif ($totalPaid < $item->totalamount) {
                $status = 'partially_paid';
                $payLink = '<a href="' . route('payment.create', ['id' => $item->id]) . '" class="badge badge-information" style="float: right; margin-right:10px">Add Payment</a>';
            } elseif ($totalPaid == $item->totalamount) {
                $status = 'paid';
            }

            if ($item->duedate < $today && $status == 'unpaid') {
                $status = 'Over Due';
            }

            $statusClass = $statusClasses[$status] ?? 'secondary';
            $invoiceStatus = '<span class="badge badge-' .$statusClass . '">' . $status . '</span>';
            $balanceStatus = '<span style ="font-weight:700" class="text-' .$statusClass . '">' . $sitesettings->site_currency.'. '.$balance . '</span>';

                $tableData['rows'][] = [
                    'id' => $item->id,
                    $invoiceStatus . '</br></br> INV#: ' . $item->id . '-' . $item->referenceno,
                    '<span class="text-muted" style="font-weight:500;font-style: italic"> Invoice Date  -  Due Date</span></br>' .
                        Carbon::parse($item->created_at)->format('Y-m-d') . ' - ' . Carbon::parse($item->duedate)->format('Y-m-d'),
                    $item->property->property_name.' - '.$item->unit->unit_number,
                    $item->invoice_type,
                    $item->model->firstname.' '.$item->model->lastname,
                    $sitesettings->site_currency.'. '.$item->totalamount,
                    $sitesettings->site_currency.'. '.$totalPaid,
                    $balanceStatus.'  ' .$payLink,
                   

                ];
        }

        return $tableData;
    }




    public function index()
    {
        $invoicedata = $this->model::with('property','lease')->get();
        $mainfilter =  $this->model::distinct()->pluck('invoice_type')->toArray();
        $viewData = $this->formData($this->model);
     //   $cardData = $this->cardData($this->model,$invoicedata);
       // dd($cardData);
        $controller = $this->controller;
        $tableData = $this->getInvoiceData($invoicedata);
        
        return View('admin.CRUD.form', compact('mainfilter', 'tableData', 'controller'),
        $viewData,
        [
         //   'cardData' => $cardData,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        ///1. GET CHARGES WITH RECURRING CHARGE
     //   $unitchargedata = Unitcharge::where('recurring_charge', 'no')
     //       ->where('parent_id', null)
      //      ->whereHas('lease', function ($query) {
      //          $query->where('status', 'Active');
      //      })
       //     ->get();
          //  dd($unitchargedata);
    $unitchargedata = Unitcharge::where('recurring_charge', 'Yes')
                     ->where('parent_id', null)
                     ->whereHas('unit.lease', function ($query) {
                                  $query->where('status', 'Active');
                              })
                    ->get();

        $tableData = $this->tableViewDataService->getIndexUnitChargeData($unitchargedata);
        return View('admin.lease.invoice',['tableData' => $tableData,'controller' => ['unitcharge']]);
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
            ->whereHas('lease', function ($query) {
                $query->where('status', 'Active');
            })
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
            'Account Statement',
        ]);



        /// Data for the Account Statement
        $unitchargeId = $invoice->invoiceItems->pluck('unitcharge_id')->first();
        //    dd($unitchargeIds);
        $sixMonths = now()->subMonths(6);
        $transactions = Transaction::where('created_at', '>=', $sixMonths)
            ->where('unit_id', $invoice->unit_id)
            ->where('unitcharge_id', $unitchargeId)
            ->get();
        $groupedInvoiceItems = $transactions->groupBy('unitcharge_id');

        ////Opening Balance
        $openingBalance = $this->calculateOpeningBalance($invoice);

        $tabContents = [];
        foreach ($tabTitles as $title) {
            if ($title === 'Overview') {
                $tabContents[] = View('admin.lease.invoice_view', compact('invoice'))->render();
            } elseif ($title === 'Account Statement') {
                $tabContents[] = View('admin.lease.statement_view', compact('invoice', 'groupedInvoiceItems', 'transactions', 'openingBalance'))->render();
            }
        }


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
        $user->notify(new InvoiceGeneratedNotification($invoice, $user));
        return redirect()->back()->with('status', 'Sucess Invoice Reminder Sent to the tenant.');
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
