<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use App\Models\Unit;
use App\Models\Payment;
use App\Models\Property;
use App\Models\Invoice;
use App\Models\PaymentMethod;
use App\Models\Deposit;
use App\Traits\FormDataTrait;
use App\Services\PaymentService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use App\Notifications\PaymentNotification;
use App\Services\TableViewDataService;
use App\Actions\RecordTransactionAction;


class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    use FormDataTrait;
    protected $controller;
    protected $model;
    private $paymentService;
    private $tableViewDataService;
    private $recordTransactionAction;

    public function __construct(PaymentService $paymentService, TableViewDataService $tableViewDataService,
    RecordTransactionAction $recordTransactionAction)
    {
        $this->model = Payment::class;
        $this->controller = collect([
            '0' => 'payment', // Use a string for the controller name
            '1' => ' Payment',
        ]);

        $this->paymentService = $paymentService;
        $this->tableViewDataService = $tableViewDataService;
        $this->recordTransactionAction = $recordTransactionAction;
    }

    public function index()
    {
        $paymentdata = $this->model::with('property', 'lease', 'unit')->get();
        $mainfilter =  $this->model::distinct()->pluck('payment_code')->toArray();
        //   $viewData = $this->formData($this->model);
        //   $cardData = $this->cardData($this->model,$invoicedata);
        // dd($cardData);
        $controller = $this->controller;
        $tableData = $this->tableViewDataService->getPaymentData($paymentdata, true);

        return View(
            'admin.CRUD.form',
            compact('mainfilter', 'tableData', 'controller'),
            //  $viewData,
            [
                //   'cardData' => $cardData,
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id = null, $model = null)
    {
        switch ($model) {
            case 'Expense':
                $instance = Expense::find($id); // replace with actual logic to load an Expense
                $doc = 'EXP-';
                $propertynumber =  'P' . str_pad($instance->property->id, 2, '0', STR_PAD_LEFT);
                $unitnumber =$instance->unit->unit_number ?? 'N';
                break;
            case 'Deposit':
                $instance = Deposit::find($id); 
                $doc = 'DEP-';
                $propertynumber =  'P' . str_pad($instance->property->id, 2, '0', STR_PAD_LEFT);
                $unitnumber =$instance->unit->unit_number ?? 'N';
                break;
            default:
                $instance = Invoice::with('invoiceItems', 'payments.paymentItems')->find($id);
                $doc = 'INV-';
                $propertynumber =  'P' . str_pad($instance->property->id, 2, '0', STR_PAD_LEFT);
                $unitnumber =$instance->unit->unit_number ?? 'N';
                break; // or handle this case differently
        }

        //     $invoice = Invoice::with('invoiceItems', 'payments.paymentItems')->find($id);
        $PaymentMethod = PaymentMethod::where('property_id', $instance->property_id)->get();
        $className = get_class($instance);
        ///REFERENCE NO
        $date = Carbon::now()->format('ymd');
       
        $referenceno = $doc.$propertynumber.$unitnumber.'-'.$date;



        Session::flash('previousUrl', request()->server('HTTP_REFERER'));
        return View('admin.lease.payment', compact('PaymentMethod', 'instance', 'className', 'referenceno', 'model'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       
        $validationRules = Payment::$validation;
        $validatedData = $request->validate($validationRules);
        $instanceId = $request->input('instanceId');
        $user = Auth::user();
        $payment = null;

     //   dd($request->model);
        switch ($request->model) {
            case 'Expense':
                $model = Expense::find($instanceId);
                $payment = $this->paymentService->generatePayment($model, $validatedData);
                break;
            case 'Deposit':
                $model = Deposit::find($instanceId);
                $payment = $this->paymentService->generatePayment($model, $validatedData);
                break;
            default:
                $model = Invoice::find($instanceId);
                $payment = $this->paymentService->generatePayment($model, $validatedData);
                break; // or handle this case differently
        }

        return redirect()->route('payment.index')->with('status', 'Payment Added Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Payment $payment)
    {

        //dd($payment->model->totalamount);
        $pageheadings = collect([
            '0' => $payment->unit->unit_number ?? '',
            '1' => $payment->unit->property->property_name ?? $payment->property->property_name,
            '2' => $payment->unit->property->property_streetname ?? $payment->property->property_streetname,
        ]);
        $tabTitles = collect([
            'Overview',
        ]);

        $tabContents = [];
        foreach ($tabTitles as $title) {
            if ($title === 'Overview') {
                $tabContents[] = View('admin.lease.payment_view', compact('payment'))->render();
            }
        }
        return View('admin.CRUD.form', compact('pageheadings', 'tabTitles', 'tabContents'));
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

    public function sendPayment(Payment $payment)
    {
        //  $invoice->load('property');
        //    dd($invoice);
        //  return View('email.invoice',compact('invoice'));
        //   $pdf = PDF::loadView('email.invoice', compact('invoice'));
        //  return $pdf->download('invoice12.pdf');
        //   return $pdf->stream('invoice.pdf');

     //   $user = $invoice->model;
     //   $user->notify(new InvoiceGeneratedNotification($invoice, $user));
        $this->paymentService->paymentEmail($payment);
        return redirect()->back()->with('status', 'Sucess Payment receipt Sent.');
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
