<?php

namespace App\Http\Controllers;

use App\Models\PropertyType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Traits\FormDataTrait;

class PropertyTypeController extends Controller
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
        $this->model = PropertyType::class;

        $this->controller = collect([
            '0' => 'propertytype', // Use a string for the controller name
            '1' => 'New Property Type',
        ]);
    }
    public function index()
    {
        $user = Auth::user();
        if (Gate::allows('view-all', $user)) {
            $tablevalues = PropertyType::all();
        } else {
            $tablevalues = PropertyType::all();
        }

        $mainfilter =  $this->model::pluck('property_category')->toArray();
        $viewData = $this->formData($this->model);
        $controller = $this->controller;
        /// TABLE DATA ///////////////////////////
        $tableData = [
            'headers' => ['TYPE', 'CATEGORY','ACTIONS'],
            'rows' => [],
        ];

        foreach ($tablevalues as  $item) {
            $tableData['rows'][] = [
                'id' => $item->id,
                $item->property_type,
                $item->property_category,

            ];
        }

        return View('admin.CRUD.form',compact('mainfilter', 'tableData', 'controller'),$viewData,
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

        return View('admin.CRUD.form',$viewData);
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
            'property_type' => 'required',
            'property_category' => 'required',
           
        ]);
        $propertytype = new PropertyType();
        $propertytype->fill($validatedData);
        $propertytype->save();

        return redirect($this->controller['0'])->with('status', $this->controller['1'] . ' Added Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\propertyType  $propertyType
     * @return \Illuminate\Http\Response
     */
    public function show(propertyType $propertyType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\propertyType  $propertyType
     * @return \Illuminate\Http\Response
     */
    public function edit(propertyType $propertyType)
    {
        $viewData = $this->formData($this->model,$propertyType,);
        
        return View('admin.CRUD.form',$viewData);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\propertyType  $propertyType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, propertyType $propertyType)
    {
        $validatedData = $request->validate([
            'property_type' => 'required',
            'property_category' => 'required',
           
        ]);
        $propertytype = PropertyType:: find($propertyType);
        $propertytype->fill($validatedData);
        $propertytype->update();

        return redirect($this->controller['0'])->with('status', $this->controller['1'] . ' Edited Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\propertyType  $propertyType
     * @return \Illuminate\Http\Response
     */
    public function destroy(propertyType $propertyType)
    {
        //
    }
}
