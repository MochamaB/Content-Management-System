<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Unitcharge;
use App\Models\Unit;
use App\Models\Transaction;
use App\Models\User;
use App\Services\InvoiceService;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Notifications\InvoiceGeneratedNotification;
use App\Services\TableViewDataService;
use App\Services\FilterService;
use App\Services\CardService;
use App\Models\WebsiteSetting;
use App\Traits\FormDataTrait;
use Illuminate\Support\Facades\Session;

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
    private $cardService;


    public function __construct(InvoiceService $invoiceService, TableViewDataService $tableViewDataService,
    FilterService $filterService, CardService $cardService)
    {
        $this->model = Invoice::class;
        $this->controller = collect([
            '0' => 'invoice', // Use a string for the controller name
            '1' => 'Due Invoices',
        ]);
        $this->invoiceService = $invoiceService;
        $this->tableViewDataService = $tableViewDataService;
        $this->filterService = $filterService;
        $this->cardService = $cardService;
    }


    public function index(Request $request)
    {
        $filters = $request->except(['tab','_token','_method']);
        $filterdata = $this->filterService->getInvoiceFilters();
        $baseQuery = Invoice::with('unit', 'property', 'payments')->ApplyCurrentMonthFilters($filters);
        $cardData = $this->cardService->invoiceCard($baseQuery->get());
        $tabTitles = ['All', 'Paid', 'Unpaid', 'Overdue'];
        $tabContents = [];
        $tabCounts = [];
        foreach ($tabTitles as $title) {
            $query = clone $baseQuery;
            switch ($title) {
                case 'Paid':
                    $query->where('status', 'paid');
                    break;
                case 'Unpaid':
                    $query->where('status', 'unpaid');
                    break;
                case 'Overdue':
                    $query->where('status', 'unpaid')
                          ->where('duedate', '<', now());
                    break;
                // 'All' doesn't need any additional filters
            }
            $invoices = $query->get();
            $count = $invoices->count();
            $tableData = $this->tableViewDataService->getInvoiceData($invoices, true);
            $controller = $this->controller;
            $tabContents[] = view('admin.CRUD.table', [
                'data' => $tableData,
                'controller' => $controller,
            ])->render();
            $tabCounts[$title] = $count;
        }
       
      //  dd($filterData);
     //   return view('admin.CRUD.form', array_merge(
      //      compact('tableData', 'controller','filterdata','filters','cardData')
          //  ,['cardData' => $cardData]
      //  ));

        return View('admin.CRUD.form', compact( 'controller','tabTitles', 'tabContents','filters','filterdata','cardData','tabCounts'));
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
        $info = null; // Initialize the variable
        if ($unitchargedata->isEmpty()) {
            // If no data, return the view with the info message
           $info =  'Invoices for this month already generated successfully';
        }

        $tableData = $this->tableViewDataService->getUnitChargeData($unitchargedata, true);
        Session::flash('previousUrl', request()->server('HTTP_REFERER'));
        
        return View('admin.Lease.invoice', ['tableData' => $tableData,'info' => $info, 'controller' => ['unitcharge']]);
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
                $tabContents[] = View('admin.Lease.invoice_view', compact('invoice','PaymentMethod'))->render();
            } elseif ($title === 'Account Statement') {
                $tabContents[] = View('admin.Lease.statement_view', compact('invoice', 'transactions', 'openingBalance'))->render();
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
            ->where('charge_name', $invoice->name)
            ->where('transactionable_type', 'App\Models\Invoice')
            ->sum('amount');

        // Calculate the sum of payment amounts
        $paymentAmount = Transaction::where('created_at', '<', $sixMonthsAgo)
            ->where('unit_id', $invoice->unit_id)
            ->where('charge_name', $invoice->name)
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

     //   $user = $invoice->model;
     //   $user->notify(new InvoiceGeneratedNotification($invoice, $user));
        $this->invoiceService->invoiceEmail($invoice);
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

    public function invoicemail()
    {
        $user = User::find(1); // Get a user to test the notification
        $invoice = Invoice::find(1); // Get an invoice to test the notification

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

        return View('email.statement', [
        'user' => $user,
        'invoice' => $invoice,
        'transactions' => $transactions, // Pass an empty collection or fetch actual transactions
        'groupedInvoiceItems' => $groupedInvoiceItems, // Pass an empty collection or fetch actual grouped items
        'openingBalance' => $openingBalance,
        'PaymentMethod' => $PaymentMethod, // Pass a default value or calculate the opening balance
    ]);

    // Return the notification view
  //  return (new InvoiceGeneratedNotification($invoice, $user, $viewContent))->toMail($user);
    }
}
