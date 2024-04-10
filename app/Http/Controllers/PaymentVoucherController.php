<?php

namespace App\Http\Controllers;

use App\Models\Paymentvoucher;
use App\Models\Property;
use App\Models\Unit;
use Illuminate\Http\Request;
use App\Models\Unitcharge;
use App\Services\PaymentVoucherService;
use App\Services\TableViewDataService;
use App\Services\FilterService;
use App\Traits\FormDataTrait;
use Illuminate\Support\Facades\Session;

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
    private $filterService;
   

    public function __construct(PaymentVoucherService $paymentVoucherService,TableViewDataService $tableViewDataService,
    FilterService $filterService)
    {
        $this->model = Paymentvoucher::class;
        $this->controller = collect([
            '0' => 'paymentvoucher', // Use a string for the controller name
            '1' => ' Payment Voucher',
        ]);
        $this->paymentVoucherService = $paymentVoucherService;
        $this->tableViewDataService = $tableViewDataService;
        $this->filterService = $filterService;
       
    }
    public function index(Request $request)
    {
        $filters = $request->except(['tab','_token','_method']);
        $filterdata = $this->filterService->getPaymentVoucherFilters($request);
        $paymentvoucherdata = $this->model::with('property','unit')->ApplyDateFilters($filters)->get();
     //   $viewData = $this->formData($this->model);
     //   $cardData = $this->cardData($this->model,$invoicedata);
       // dd($cardData);
        $controller = $this->controller;
        $tableData = $this->tableViewDataService->getPaymentVoucherData($paymentvoucherdata,true);
        
        return View('admin.CRUD.form', compact('filterdata', 'tableData', 'controller'),
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
    public function create($id = null)
    {

     //   $unit = Unit::find($id);
     //   $property = Property::where('id', $unit->property->id)->first();
       
 
        //   dd($latestReading);
        return redirect()->route('unitcharge.create');
     //   Session::flash('previousUrl', request()->server('HTTP_REFERER'));

     //   return View('admin.lease.create_paymentvoucher', compact('id','property', 'unit'));
    

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
    public function show(Paymentvoucher $paymentvoucher)
    {
        $pageheadings = collect([
            '0' => $paymentvoucher->unit->unit_number,
            '1' => $paymentvoucher->unit->property->property_name,
            '2' => $paymentvoucher->unit->property->property_streetname,
        ]);
        $tabTitles = collect([
            'Overview',
        ]);
        $tabContents = [];
        foreach ($tabTitles as $title) {
            if ($title === 'Overview') {
                $tabContents[] = View('admin.lease.paymentvoucher_view', compact('paymentvoucher'))->render();
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
