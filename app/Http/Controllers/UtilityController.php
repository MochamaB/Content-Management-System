<?php

namespace App\Http\Controllers;

use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Traits\FormDataTrait;
use App\Models\Property;

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
            '1' => 'New Utility',
        ]);
    }

    public function getUtilitiesData($utilitiesdata)
    {
         /// TABLE DATA ///////////////////////////
         $tableData = [
            'headers' => ['UTILITY', 'PROPERTY', 'TYPE', 'RATE', 'ACTIONS'],
            'rows' => [],
        ];

        foreach ($utilitiesdata as  $item) {
            $tableData['rows'][] = [
                'id' => $item->id,
                $item->utility_name,
                $item->property->property_name,
                $item->utility_type,
                $item->rate,

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
      
        return View('admin.CRUD.form',compact('mainfilter', 'tableData', 'controller'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
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
        if (Utility::where('utility_name', $request->get('utility_name'))->exists()) {
            return redirect()->back()->with('statuserror', ' The Utility is already attached to the property');
        }

        $validatedData = $request->validate([
            'property_id' => 'required',
            'chartofaccounts_id' => 'required|numeric',
            'utility_name' => 'required',
            'utility_type' => 'required',
            'rate' => 'required|numeric',
        ]);
        $utility = new Utility();
        $utility->fill($validatedData);
        $utility->save();

        return redirect($this->controller['0'])->with('status', $this->controller['1'] . ' Added Successfully');
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
        dd($utility);
        // $viewData = $this->formData($this->model,$amenity);

        return View('admin.CRUD.edit',compact('utility'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\utility  $utility
     * @return \Illuminate\Http\Response
     */
    public function edit(Utility $utility)
    {
       // dd($utility);
       
        $viewData = $this->formData($this->model,$utility);

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

        return redirect($this->controller['0'])->with('status', $this->controller['1'] . ' Edited Successfully');
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
