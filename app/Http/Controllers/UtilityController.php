<?php

namespace App\Http\Controllers;

use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Traits\FormDataTrait;
use App\Models\Property;
use App\Models\Website;

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

    public function __construct()
    {
        $this->model = Utility::class;

        $this->controller = collect([
            '0' => 'utility', // Use a string for the controller name
            '1' => ' Utility',
        ]);
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
        $mainfilter =  Property::pluck('property_name')->toArray();
        $viewData = $this->formData($this->model);
        $controller = $this->controller;
        $tableData = $this->getUtilitiesData($utilitiesdata);
        // Clear previousUrl if navigating to a new create method
        session()->forget('previousUrl');

        return View('admin.CRUD.form', compact('mainfilter', 'tableData', 'controller'));
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

        return View('admin.CRUD.form', $viewData);
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
        
        $specialvalue = collect([
            'property_id' => $utility->property->property_name, // Use a string for the controller name
            'chartofaccounts_id' => $utility->accounts->account_name,
        ]);

        $viewData = $this->formData($this->model, $utility, $specialvalue);

        return View('admin.CRUD.form', $viewData);
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
            'property_id' => 'required',
            'chartofaccounts_id' => 'required|numeric',
            'utility_name' => 'required',
            'utility_type' => 'required',
            'rate' => 'required|numeric',
        ]);
        $utility = Utility::find($utility->id);
        $utility->fill($validatedData);
        $utility->update();
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
