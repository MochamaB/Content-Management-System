<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Unit;
use App\Models\Payment;
use App\Models\Property;
use App\Models\Invoice;
use App\Models\PaymentType;
use App\Traits\FormDataTrait;
use App\Services\PaymentService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

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

     public function __construct(PaymentService $paymentService)
     {
         $this->model = Payment::class;
         $this->controller = collect([
             '0' => 'payment', // Use a string for the controller name
             '1' => 'New Payment',
         ]);

         $this->paymentService = $paymentService;
     }

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
        $paymenttype = PaymentType::all();
        $className = get_class($invoice);

        ////REFRENCE NO
        $today = Carbon::now();
        $invoicenodate = $today->format('ym');
        $unitnumber = $invoice->unit->unit_number;
        $referenceno = 'RCT-'.$invoice->id.'-'.$invoicenodate . $unitnumber;
      //  dd($referenceno);

      Session::flash('previousUrl', request()->server('HTTP_REFERER'));
        return View('admin.lease.payment',compact('paymenttype','invoice','className','referenceno'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $invoiceId = $request->input('invoice');
        $model = Invoice::find($invoiceId);

        $items = $model->getItems;
     //   dd($items);
        $validationRules = Payment::$validation;
        $validatedData = $request->validate($validationRules);
        
     //   dd($validatedData);
        $this->paymentService->generatePayment($model,$validatedData);

        $previousUrl = Session::get('previousUrl');
        return redirect($previousUrl)->with('status', 'Payment Added Successfully');
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
