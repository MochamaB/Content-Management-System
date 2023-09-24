<?php

namespace App\Http\Controllers;

use App\Models\Utilities;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Traits\FormDataTrait;
use App\Models\Property;

class UtilitiesController extends Controller
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
        $this->model = Utilities::class;

        $this->controller = collect([
            '0' => 'utilities', // Use a string for the controller name
            '1' => 'New Utility',
        ]);
    }
    public function index($property = null)
    {
        $user = Auth::user();
        if (Gate::allows('view-all', $user)) {
            $tablevalues = Utilities::all();
          //  $tablevalues = ($property) ? $this->model::with('property')->where('property_id', $property->id)->get() : $this->model::with('property')->get();
        } else {
            $tablevalues = Utilities::with('property')->get();
          //  $tablevalues = ($property) ? $this->model::with('property')->where('property_id', $property->id)->get() : $this->model::with('property')->get();
        }

        $mainfilter =  Property::pluck('property_name')->toArray();
        $viewData = $this->formData($this->model);
        $controller = $this->controller;
        /// TABLE DATA ///////////////////////////
        $tableData = [
            'headers' => ['UTILITY', 'PROPERTY', 'TYPE', 'RATE', 'ACTIONS'],
            'rows' => [],
        ];

        foreach ($tablevalues as  $item) {
            $tableData['rows'][] = [
                'id' => $item->id,
                $item->utility_name,
                $item->property->property_name,
                $item->utility_type,
                $item->rate,

            ];
        }

        $utilitiesviewData = compact('tableData', 'mainfilter', 'viewData','controller');

        return View(
            'admin.CRUD.form',
            compact('mainfilter', 'tableData', 'controller'),
            $viewData,
            ['controller' => $this->controller]
        );
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

        $validatedData = $request->validate([
            'property_id' => 'required',
            'chartofaccounts_id' => 'required|numeric',
            'utility_name' => 'required',
            'utility_type' => 'required',
            'rate' => 'required|numeric',
        ]);
        $utilities = new Utilities();
        $utilities->fill($validatedData);
        $utilities->save();

        return redirect($this->controller['0'])->with('status', $this->controller['1'] . ' Added Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Utilities  $utilities
     * @return \Illuminate\Http\Response
     */
    public function show(Utilities $utilities)
    {
       // $utility = Utilities::find($utilities->id);
        dd($utilities);
        // $viewData = $this->formData($this->model,$amenity);

        return View('admin.CRUD.edit',compact('utilities'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Utilities  $utilities
     * @return \Illuminate\Http\Response
     */
    public function edit(Utilities $utilities)
    {
       // dd($utilities);
       
        $viewData = $this->formData($this->model,$utilities);

        return View('admin.CRUD.form', $viewData);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Utilities  $utilities
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Utilities $utilities)
    {
        $validatedData = $request->validate([
            'property_id' => 'required',
            'chartofaccounts_id' => 'required|numeric',
            'utility_name' => 'required',
            'utility_type' => 'required',
            'rate' => 'required|numeric',
        ]);
        $utilities = Utilities::find($utilities);
        $utilities->fill($validatedData);
        $utilities->update();

        return redirect($this->controller['0'])->with('status', $this->controller['1'] . ' Edited Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Utilities  $utilities
     * @return \Illuminate\Http\Response
     */
    public function destroy(Utilities $utilities)
    {
        //
    }
}
