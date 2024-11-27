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
use App\Services\DepositService;
use App\Services\FilterService;
use App\Services\CardService;
use App\Actions\UpdateNextDateAction;

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
    private $DepositService;
    private $filterService;
    private $cardService;
    private $updateNextDateAction;

    public function __construct(TableViewDataService $tableViewDataService, DepositService $DepositService,
    FilterService $filterService, CardService $cardService, UpdateNextDateAction $updateNextDateAction)
    {
        $this->model = unitcharge::class;
        $this->controller = collect([
            '0' => 'unitcharge', // Use a string for the controller name
            '1' => ' Unit Charge',
        ]);
        $this->tableViewDataService = $tableViewDataService;
        $this->DepositService = $DepositService;
        $this->filterService = $filterService;
        $this->cardService = $cardService;
        $this->updateNextDateAction = $updateNextDateAction;
    }



    public function index(Request $request)
    {
        // Clear previousUrl if navigating to a new create method
        session()->forget('previousUrl');
        $filters = $request->except(['tab','_token','_method']);
        $baseQuery = $this->model::with('property', 'unit')->whereNull('parent_id')->showSoftDeleted()->applyFilters($filters);
        $filterdata = $this->filterService->getUnitChargeFilters($request);
        $cardData = $this->cardService->unitchargeCard($baseQuery->get());
        $tabTitles = ['All', 'Recurring', 'Charged Once'];
        $tabContents = [];
        $tabCounts = [];
        foreach ($tabTitles as $title) {
            $query = clone $baseQuery;
            switch ($title) {
                case 'Recurring':
                    $query->where('recurring_charge', 'yes');
                    break;
                case 'Charged Once':
                    $query->where('recurring_charge', 'no');
                    break;
                    // 'All' doesn't need any additional filters
            }
            $unitCharges = $query->orderBy('unit_id')->get();
            $count = $unitCharges->count();
            $tableData = $this->tableViewDataService->getUnitChargeData($unitCharges, true);
            $controller = $this->controller;
            $tabContents[] = view('admin.CRUD.table', [
                'data' => $tableData,
                'controller' => $controller,
            ])->render();
            $tabCounts[$title] = $count;
        }
    
        return View('admin.CRUD.form', compact( 'controller','tabTitles', 'tabContents','filters','filterdata','cardData','tabCounts'));
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
        $utilities = Utility:: where('property_id',$property->id)->get();


         ///SESSION /////
         if (!session()->has('previousUrl')) {
            session()->put('previousUrl', url()->previous());
        }

        return View('admin.Lease.create_unitcharge', compact('id', 'property', 'unit', 'accounts','model','utilities'));
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

        if ($utilityNameExists && $chargeNameExists) {
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
            $this->DepositService->generateDeposit($unitcharge);
        }else{
            //Update Next nad Updated Date in the Unitcharge
            $this->updateNextDateAction->newChargeNextdate($unitcharge);
        }

        $redirectUrl = session()->pull('previousUrl', $this->controller['0']);
        return redirect($redirectUrl)->with('status', 'Unitcharge Entered Successfully');
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\unitcharge  $unitcharge
     * @return \Illuminate\Http\Response
     */
    public function show(unitcharge $unitcharge)
    {
        ///// Used to Set Property TYpe name in the edit view.///
        $specialvalue = collect([
            'property_id' =>$unitcharge->property->property_name, // Use a string for the controller name
            'unit_id' => $unitcharge->unit->unit_number,
            'chartofaccounts_id' => $unitcharge->chartofaccounts->account_name,
        ]);
        $viewData = $this->formData($this->model, $unitcharge,$specialvalue);
        return View('admin.CRUD.details',$viewData);
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
        return View('admin.Lease.charges', compact('pageheadings', 'accounts', 'lease', 'rentcharge', 'splitRentcharges', 'unitcharge'));
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

    public function fetchCharge(Request $request)
    {

        $data = Utility::where('utility_name', $request->charge_name)
        ->where('property_id', $request->property_id)
            ->first();


        return response()->json($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\unitcharge  $unitcharge
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Retrieve the model instance
        $model = $this->model::findOrFail($id);
        $modelName = class_basename($model);

        // Define the relationships to check
        $relationships = ['meterReading','invoices','childrencharge'];

        // Call the destroy method from the DeletionService
        return $this->tableViewDataService->destroy($model, $relationships,  $modelName);
    }
}
