<?php

namespace App\Http\Controllers;

use App\Models\Chartofaccount;
use App\Models\Deposit;
use App\Models\Property;
use App\Models\Unit;
use Illuminate\Http\Request;
use App\Models\Unitcharge;
use App\Models\Vendor;
use App\Services\TableViewDataService;
use App\Services\FilterService;
use App\Services\DepositService;
use App\Traits\FormDataTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class DepositController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    use FormDataTrait;
    protected $controller;
    protected $model;
    private $tableViewDataService;
    private $filterService;
    private $depositService;
   

    public function __construct(TableViewDataService $tableViewDataService,
    FilterService $filterService, DepositService $depositService)
    {
        $this->model = Deposit::class;
        $this->controller = collect([
            '0' => 'deposit', // Use a string for the controller name
            '1' => ' Deposits',
        ]);
       
        $this->tableViewDataService = $tableViewDataService;
        $this->filterService = $filterService;
        $this->depositService = $depositService;
       
    }
    public function index(Request $request)
    {
        $filters = $request->except(['tab','_token','_method']);
        $filterdata = $this->filterService->getPropertyFilters($request);
        $depositdata = $this->model::with('property','unit')->ApplyDateFilters($filters)->get();
     
        $controller = $this->controller;
        $tableData = $this->tableViewDataService->getDepositData($depositdata,true);
        
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
    public function create($id = null,$model = null)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($model === 'properties') {
            $property = Property::find($id);
            $unit = $property->units;
        } elseif ($model === 'units') {
            $unit = Unit::find($id);
            $property = Property::where('id', $unit->property->id)->first();
        }
        $account = Chartofaccount::whereIn('account_type', ['Liability'])->get();
        $accounts = $account->groupBy('account_type');
        $vendors = Vendor::all();
        $users = $user->filterUsers();


        Session::flash('previousUrl', request()->server('HTTP_REFERER'));

        return View('admin.accounting.create_Deposit', compact('id', 'property', 'unit','account', 'accounts','model','vendors','users'));
        //
    }

    public function generateDeposit(Request $request)
    {
        ///1. GET UNITS WITH RECURRING CHARGE
        $unitcharges = Unitcharge::where('recurring_charge', 'no')
            ->first();

            //1. Create invoice items from invoice service app/Services/InvoiceService
            $this->depositService->generateDeposit($unitcharges);

        


        
        return redirect()->back()->with('status', 'Sucess Deposit generated.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         ////REFRENCE NO <DOCUMENT><PROPERTYNO><UNITNUMBER><DATE><ID>
         $doc = 'DEP-';
         $propertynumber = 'P' . str_pad($request->property_id, 2, '0', STR_PAD_LEFT);
         $unitnumber = $request->unit_id ?? 'N';
         $date = Carbon::now()->format('ymd');
        
         $referenceno = $doc.$propertynumber.$unitnumber.'-'.$date;
 
         //// INSERT DATA TO THE PAYMENT VOUCHER
         $validationRules = Deposit::$validation;
         $validatedData = $request->validate($validationRules);

         $this->depositService->generateDepositForm($validatedData,$request,$referenceno);
 
         return redirect($this->controller['0'])->with('status', $this->controller['1'] . ' Added Successfully');
         /*
        $previousUrl = Session::get('previousUrl');
        if ($previousUrl) {
            return redirect($previousUrl)->with('status', 'Your Expense has been saved successfully');
        } else {
            return redirect($this->controller['0'])->with('status', $this->controller['1'] . ' Added Successfully');
        }*/
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Deposit $deposit)
    {
        $pageheadings = collect([
            '0' => $deposit->unit->unit_number ?? '',
            '1' => $deposit->unit->property->property_name  ?? $deposit->property->property_name,
            '2' => $deposit->unit->property->property_streetname  ?? $deposit->property->property_streetname,
        ]);
        $tabTitles = collect([
            'Overview',
        ]);
        $tabContents = [];
        foreach ($tabTitles as $title) {
            if ($title === 'Overview') {
                $tabContents[] = View('admin.lease.deposit_view', compact('deposit'))->render();
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