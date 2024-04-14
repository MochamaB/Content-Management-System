<?php

namespace App\Http\Controllers;

use App\Models\unitcharge;
use Illuminate\Http\Request;
use App\Traits\FormDataTrait;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\Lease;
use App\Models\Chartofaccount;
use App\Models\Property;
use App\Models\Unit;
use App\Models\Utility;
use Carbon\Carbon;
use App\Services\TableViewDataService;
use App\Services\PaymentVoucherService;
use App\Services\FilterService;

class UnitChargeController extends Controller
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
    private $paymentVoucherService;
    private $filterService;

    public function __construct(TableViewDataService $tableViewDataService, PaymentVoucherService $paymentVoucherService,
    FilterService $filterService)
    {
        $this->model = unitcharge::class;
        $this->controller = collect([
            '0' => 'unitcharge', // Use a string for the controller name
            '1' => ' Unit Charge',
        ]);
        $this->tableViewDataService = $tableViewDataService;
        $this->paymentVoucherService = $paymentVoucherService;
        $this->filterService = $filterService;
    }



    public function index(Request $request)
    {
        $filters = $request->except(['tab','_token','_method']);
        $unitChargeData = $this->model::with('property', 'unit')->applyFilters($filters)->get();
        $filterdata = $this->filterService->getUnitChargeFilters($request);
      //  $mainfilter =  $this->model::pluck('charge_type')->toArray();
        $viewData = $this->formData($this->model);
        $controller = $this->controller;
        $tableData = $this->tableViewDataService->getUnitChargeData($unitChargeData, true);

        return View('admin.CRUD.form', compact('filterdata', 'tableData', 'controller'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id = null,$model = null)
    {
        if ($model === 'properties') {
            $property = Property::find($id);
            $unit = $property->units;
        } elseif ($model === 'units') {
            $unit = Unit::find($id);
            $property = Property::where('id', $unit->property->id)->first();
        }
        $account = Chartofaccount::whereIn('account_type', ['Income', 'Liability'])->get();
        $accounts = $account->groupBy('account_type');


        Session::flash('previousUrl', request()->server('HTTP_REFERER'));

        return View('admin.lease.create_unitcharge', compact('id', 'property', 'unit', 'accounts','model'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    { 
        ///Check if the value exists in the database
        $chargeName = $request->input('charge_name');

        $utilityNameExists = Utility::where('property_id', $request->property_id)
            ->where('utility_name', $chargeName)
            ->exists();
        $chargeNameExists = Unitcharge::where('unit_id', $request->unit_id)
            ->where('charge_name', $chargeName)
            ->exists();

        if ($utilityNameExists || $chargeNameExists) {
            return redirect()->back()->with('statuserror', 'Charge already attached to the unit or to the property in system.');
        }
        //// INSERT DATA TO THE UNITCHARGE
        $validationRules = unitcharge::$validation;
        $validatedData = $request->validate($validationRules);

        $unitcharge = new Unitcharge();
        $unitcharge->fill($validatedData);
      //  $accounttype = $unitcharge->chartofaccounts->account_type;
      //  dd($accounttype);
        $unitcharge->save();

        ////GENERATE A VOUCHER OR EXPENSE IF ITS A ONE TIME CHARGE
        if ($request->charge_cycle === "Once") {
           // dd($request->charge_cycle);
            $this->paymentVoucherService->generatePaymentVoucher($unitcharge);
        }

        $previousUrl = Session::get('previousUrl');
        return redirect($previousUrl)->with('status', 'Unitcharge Entered Successfully');
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\unitcharge  $unitcharge
     * @return \Illuminate\Http\Response
     */
    public function show(unitcharge $unitcharge)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\unitcharge  $unitcharge
     * @return \Illuminate\Http\Response
     */
    public function edit(unitcharge $unitcharge)
    {

        // Check if the parent_id is not 0
        if ($unitcharge->parent_id !== null) {
            // If parent_id is not 0, open the parent utility and pass it as the $unitcharge
            $parentUtility = Unitcharge::find($unitcharge->parent_id);
            // Pass the parent utility as the $unitcharge
            $unitcharge = $parentUtility;
        }


        $pageheadings = collect([
            '0' => $unitcharge->charge_name,
            '1' => $unitcharge->unit->unit_number,
            '2' => $unitcharge->property->property_name,
        ]);



        $lease = Lease::where('unit_id', $unitcharge->unit_id);
        $rentcharge = $unitcharge;
        $splitRentcharges = Unitcharge::where('parent_id', $rentcharge->id)->get();
        $account = Chartofaccount::all();
        $accounts = $account->groupBy('account_type');

        session::flash('previousUrl', request()->server('HTTP_REFERER'));
        return View('admin.lease.charges', compact('pageheadings', 'accounts', 'lease', 'rentcharge', 'splitRentcharges', 'unitcharge'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\unitcharge  $unitcharge
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, unitcharge $unitcharge)
    {
        $unitchargedata = Unitcharge::findOrFail($unitcharge->id);
        $unitchargedata->update($request->all());

        if (!empty($request->input('splitcharge_name'))) {
            foreach ($request->input('splitcharge_name') as $index => $chargeName) {
                Unitcharge::UpdateOrCreate(
                    // Conditions to find the record (in this case, using charge_name and parent_id)
                    [
                        'property_id' => $unitcharge->property_id,
                        'unit_id' => $unitcharge->unit_id,
                        'charge_name' => $chargeName,
                        'parent_id' => $unitcharge->id,
                    ],
                    // Data to update or create
                    [
                        'property_id' => $unitcharge->property_id,
                        'unit_id' => $unitcharge->unit_id,
                        'chartofaccounts_id' => $request->input('splitchartofaccounts_id'),
                        'charge_name' => $chargeName,
                        'charge_cycle' => $unitcharge->charge_cycle,
                        'charge_type' => $request->input('splitcharge_type'),
                        'rate' => $request->input('splitrate'),
                        'parent_id' => $unitcharge->id,
                        'recurring_charge' => $unitcharge->recurring_charge,
                        'startdate' => $unitcharge->startdate,
                        'nextdate' => $unitcharge->nextdate,
                        // ... Other fields ...
                    ]
                );
            }
        }

        $previousUrl = Session::get('previousUrl');
        return redirect($previousUrl)->with('status', 'Charge Edited Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\unitcharge  $unitcharge
     * @return \Illuminate\Http\Response
     */
    public function destroy(unitcharge $unitcharge)
    {
        //
    }
}
