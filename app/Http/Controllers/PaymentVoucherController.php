<?php

namespace App\Http\Controllers;

use App\Models\Paymentvoucher;
use Illuminate\Http\Request;
use App\Models\Unitcharge;
use App\Services\PaymentVoucherService;
use App\Services\TableViewDataService;
use App\Traits\FormDataTrait;

class PaymentVoucherController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    use FormDataTrait;
    protected $controller;
    protected $model;
    private $paymentVoucherService;
    private $tableViewDataService;
   

    public function __construct(PaymentVoucherService $paymentVoucherService,TableViewDataService $tableViewDataService)
    {
        $this->model = Paymentvoucher::class;
        $this->controller = collect([
            '0' => 'paymentvoucher', // Use a string for the controller name
            '1' => 'New Paymentvoucher',
        ]);
        $this->paymentVoucherService = $paymentVoucherService;
        $this->tableViewDataService = $tableViewDataService;
       
    }
    public function index()
    {
        $paymentvoucherdata = $this->model::with('property','unit')->get();
        $mainfilter =  $this->model::distinct()->pluck('voucher_type')->toArray();
     //   $viewData = $this->formData($this->model);
     //   $cardData = $this->cardData($this->model,$invoicedata);
       // dd($cardData);
        $controller = $this->controller;
        $tableData = $this->tableViewDataService->getPaymentVoucherData($paymentvoucherdata,true);
        
        return View('admin.CRUD.form', compact('mainfilter', 'tableData', 'controller'),
      //  $viewData,
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
        return View('admin.lease.paymentvoucher');
    }

    public function generatePaymentVoucher(Request $request)
    {
        ///1. GET UNITS WITH RECURRING CHARGE
        $unitcharges = Unitcharge::where('recurring_charge', 'no')
            ->first();

            //1. Create invoice items from invoice service app/Services/InvoiceService
            $this->paymentVoucherService->generatePaymentVoucher($unitcharges);

        


        
        return redirect()->back()->with('status', 'Sucess Paymentvoucher generated.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
