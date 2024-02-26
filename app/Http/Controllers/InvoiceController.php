<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Unitcharge;
use App\Models\Unit;
use App\Models\Transaction;
use App\Services\InvoiceService;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Notifications\InvoiceGeneratedNotification;
use App\Services\TableViewDataService;
use App\Services\FilterService;
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
    private $filterService;


    public function __construct(InvoiceService $invoiceService, TableViewDataService $tableViewDataService,
    FilterService $filterService)
    {
        $this->model = Invoice::class;
        $this->controller = collect([
            '0' => 'invoice', // Use a string for the controller name
            '1' => 'Due Invoices',
        ]);
        $this->invoiceService = $invoiceService;
        $this->tableViewDataService = $tableViewDataService;
        $this->filterService = $filterService;
    }


    public function index(Request $request)
    {
        $filters = request()->all();
        $invoices = Invoice::applyFilters($filters)->get();
     //   $invoiceQuery2 = Invoice::query();
        
     //   $invoicedata = [];
        $mainfilter =  $this->model::distinct()->pluck('invoice_type')->toArray();
        ///CARD DATA
     //   $cardData = [];

    //    $month = $request->get('month', Carbon::now()->month);
    //    $year = $request->get('year', Carbon::now()->year);

   //     $this->tableViewDataService->applyDateRangeFilter($invoiceQuery2,$month,$year);
    //    $invoicedata = $invoiceQuery2->get();

    //    $cardData = $this->getCardData($month, $year);
     //  $filterData = $this->filterData($this->model);
        $filterdata = $this->filterService->getInvoiceFilters();
        $controller = $this->controller;
        $tableData = $this->tableViewDataService->getInvoiceData($invoices, true);
      //  dd($filterData);
        return view('admin.CRUD.form', array_merge(
            compact('mainfilter', 'tableData', 'controller','filterdata')
          //  ,['cardData' => $cardData]
        ));
    }


    private function getCardData($month, $year)
    {
        $invoiceQuery = Invoice::query();
        $paymentQuery = Payment::query();
        $this->tableViewDataService->applyDateRangeFilter($invoiceQuery, $month, $year);
      //  $this->tableViewDataService->applyDateRangeFilter($paymentQuery, $month, $year);
        // Count the invoices for the current month
        $invoiceCount = $invoiceQuery->count();
        $invoicePayments = $invoiceQuery->withCount('payments')->get();
        $paymentCount = $invoicePayments->sum('payments_count');
       // dd($paymentCount);
        $invoicesWithoutPaymentsCount = $invoiceQuery->whereDoesntHave('payments')->count();

        $invoices = $invoiceQuery->with('payments')->get();
        $paymentSum = 0;
        foreach ($invoices as $invoice) {
            $paymentSum += $invoice->payments->sum('totalamount');
        }
      // Assuming $invoiceQuery contains a collection of invoices
  
   // $paymentSum = $invoices->pluck('payments')->flatten()->sum('totalamount');

     //   $paymentSum = $paymentQuery->sum('totalamount');
        $invoiceSum = $invoiceQuery->sum('totalamount');
        $unpaidSum = $invoiceSum - $paymentSum;
        $totalCardInfo1 = 'Generated';
        $totalCardInfo2 = 'Paid';
        $totalCardInfo3 = 'Unpaid';
        //   dd($invoiceSum);
        // Structure the data with card type information.
        $cards = [
            'Total Invoices' => 'total',
            'Invoiced Amount' => 'cash',
            'Paid Amount' => 'cash',
            'Unpaid Amount' => 'cash'
            // Add other card types for admin role.
        ];

        $data = [
            'Total Invoices' => [     ///TOTAL CARD
                'modelCount' => $invoiceCount,
                'modeltwoCount' => $paymentCount,
                'modelthreeCount' => $invoicesWithoutPaymentsCount,
                'totalCardInfo1' => $totalCardInfo1,
                'totalCardInfo2' => $totalCardInfo2,
                'totalCardInfo3' => $totalCardInfo3,
            ],
            'Invoiced Amount' => [ ////CASH CARD
                'modelCount' => $invoiceSum,
               
                // Add other data points related to maintenanceCount card.
            ],
            'Paid Amount' => [
                'modelCount' => $paymentSum,
                // Add other data points related to maintenanceCount card.
            ],
            'Unpaid Amount' => [
                'modelCount' => $unpaidSum,
                // Add other data points related to maintenanceCount card.
            ],
            // Add other card data for admin role.
        ];

        return ['cards' => $cards, 'data' => $data];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        ///1. GET CHARGES WITH RECURRING CHARGE
       
    //    $unitchargedata = Unitcharge::where('recurring_charge', 'Yes')
       //     ->where('parent_id', null)
        //    ->whereMonth('nextdate', now()->month)
       //     ->whereHas('unit.lease', function ($query) {
        //        $query->where('status', 'Active');
        //    })
        //    ->get();
        $unitchargedata = $this->invoiceService->getUnitCharges();


        $tableData = $this->tableViewDataService->getUnitChargeData($unitchargedata, true);
        return View('admin.lease.invoice', ['tableData' => $tableData, 'controller' => ['unitcharge']]);
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
        $unitcharges = $this->invoiceService->getUnitCharges();


        foreach ($unitcharges as $unitcharge) {
            //1. Create invoice items from invoice service app/Services/InvoiceService
            $this->invoiceService->generateInvoice($unitcharge);

            //2. Send Email/Notification to the Tenant containing the invoice.



        }
        return redirect($this->controller['0'])->with('status', $this->controller['1'] . ' Added Successfully');
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

        //// Data for the Payment Methods
        $PaymentMethod = PaymentMethod::where('property_id',$invoice->property_id)->get();

        $tabContents = [];
        foreach ($tabTitles as $title) {
            if ($title === 'Overview') {
                $tabContents[] = View('admin.lease.invoice_view', compact('invoice','PaymentMethod'))->render();
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
