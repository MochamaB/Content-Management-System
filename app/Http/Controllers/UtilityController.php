<?php

namespace App\Http\Controllers;

use App\Models\Chartofaccount;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Traits\FormDataTrait;
use App\Models\Property;
use App\Models\Unitcharge;
use App\Models\Website;
use App\Services\DashboardService;
use App\Services\TableViewDataService;

class UtilityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    use FormDataTrait;
    protected $controller;
    protected $model;
    private $dashboardService;
    private $tableViewDataService;

    public function __construct(DashboardService $dashboardService, TableViewDataService $tableViewDataService,)
    {
        $this->model = Utility::class;

        $this->controller = collect([
            '0' => 'utility', // Use a string for the controller name
            '1' => ' Utility',
        ]);
        $this->dashboardService = $dashboardService;
        $this->tableViewDataService = $tableViewDataService;
    }

    public function getUtilitiesData($utilitiesdata)
    {
        /// TABLE DATA ///////////////////////////
        $sitesettings = Website::first();
        $tableData = [
            'headers' => ['UTILITY', 'PROPERTY', 'TYPE','CYCLE', 'RATE', ''],
            'rows' => [],
        ];

        foreach ($utilitiesdata as  $item) {
            $isDeleted = $item->deleted_at !== null;
            $tableData['rows'][] = [
                'id' => $item->id,
                $item->utility_name,
                $item->property->property_name,
                $item->utility_type,
                $item->default_charge_cycle,
                $sitesettings->site_currency.' '.number_format($item->default_rate, 0, '.', ','),
                'isDeleted' => $isDeleted,


            ];
        }

        return $tableData;
    }

    public function index()
    {

        $utilitiesdata = Utility::with('property')->get();
        $viewData = $this->formData($this->model);
        $controller = $this->controller;
        $tableData = $this->tableViewDataService->getUtilityData($utilitiesdata);
        $dashboardConfig = $this->dashboard($utilitiesdata);
        // Clear previousUrl if navigating to a new create method
        session()->forget('previousUrl');

        return View('admin.CRUD.form', compact('tableData', 'controller','dashboardConfig'));
    }

    protected function dashboard($data)
    {
        return [
            'rows' => [
                [
                    'columns' => [
                        [
                            'width' => 'col-md-12',
                            'component' => 'admin.Dashboard.widgets.card',
                            'data' => [
                                'cardData' => $this->dashboardService->utilityCard($data),
                                'title' => 'Overview'
                            ]
                        ],
                    ]
                ]
            ]
        ];
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //// Sets the session previous url to return to where create was initiated from
        if (!session()->has('previousUrl')) {
            session()->put('previousUrl', url()->previous());
        }
        $viewData = $this->formData($this->model);
        $information = 'Create default utilities for the property except the Rent utility which is added in
                    the lease creation process';

        return View('admin.CRUD.form',compact('information'), $viewData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (Utility::where('utility_name', $request->get('utility_name'))
            ->where('property_id', $request->get('property_id'))->exists()
        ) {
            return redirect()->back()->withInput()->with('statuserror', 'The Utility is already attached to the property');
        }

        $validationRules = Utility::$validation;
        $validatedData = $request->validate($validationRules);
        
        $utility = new Utility();
        $utility->fill($validatedData);
        $utility->save();

        $redirectUrl = session()->pull('previousUrl', $this->controller['0']);

        return redirect($redirectUrl)->with('status', $this->controller['1'] . ' Added Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\utility  $utility
     * @return \Illuminate\Http\Response
     */
    public function show(Utility $utility)
    {
        // $utility = utility::find($utility->id);
       // dd($utility);
        // $viewData = $this->formData($this->model,$amenity);

        return redirect()->route('utility.edit', ['utility' => $utility]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\utility  $utility
     * @return \Illuminate\Http\Response
     */
    public function edit(Utility $utility)
    {
         //// Sets the session previous url to return to where edit was initiated from
         if (!session()->has('previousUrl')) {
            session()->put('previousUrl', url()->previous());
        }
        $utility->load('property','accounts');
        $account = Chartofaccount::whereIn('account_type', ['Income', 'Liability'])->get();
        $accounts = $account->groupBy('account_type');
        $information = 'Select the unit charges attached to this utility that will be also be edited with the change of the
                        utility account, type, rate or billing cycle ';
        $unitCharges = Unitcharge::where('utility_id', $utility->id)->get();
        

        return View('admin.Property.edit_utility',compact('utility','accounts','information','unitCharges'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\utility  $utility
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Utility $utility)
    {
        $validatedData = $request->validate([
            'chartofaccounts_id' => 'required',
            'utility_type' => 'required',
            'default_charge_cycle' => 'required',
            'default_rate' => 'required|numeric',
            'selected_charges' => 'array'
        ]);
        $utility = Utility::find($utility->id);
        $utility->fill($validatedData);
        $utility->update();

        $selectedCharges = $request->input('selected_charges', []);
        // Update all selected unit charges that belong to this utility
    if (!empty($selectedCharges)) {
        UnitCharge::whereIn('id', $selectedCharges)
            ->where('utility_id', $utility->id)
            ->update([
                'chartofaccounts_id' => $validatedData['chartofaccounts_id'],
                'charge_type' => $validatedData['utility_type'],
                'charge_cycle' => $validatedData['default_charge_cycle'],
                'rate' => $validatedData['default_rate']
            ]);
    }

        $redirectUrl = session()->pull('previousUrl', $this->controller['0']);
        return redirect($redirectUrl)->with('status', $this->controller['1'] . ' Edited Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\utility  $utility
     * @return \Illuminate\Http\Response
     */
    public function destroy(utility $utility)
    {
        //
    }
}
