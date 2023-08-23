<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;
use App\Traits\FormDataTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class UnitController extends Controller
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
        $this->model = Unit::class; 
        $this->controller = collect([
            '0' => 'unit', // Use a string for the controller name
            '1' => 'New Unit',
        ]);
    }
    
    public function index($property = null)
    {
        $user = Auth::user();
        
        if (Gate::allows('view-all', $user)) {
            $tablevalues = $this->model::with('property')->get();
        }else{
            $tablevalues = $user->supervisedUnits;
        }
    
        $mainfilter =  $this->model::pluck('unit_type')->toArray();
        $viewData = $this->formData($this->model);
        $controller = $this->controller;
        /// TABLE DATA ///////////////////////////
        $tableData = [
            'headers' => ['UNIT', 'PROPERTY','TENANT', 'EVENTS','ACTIONS'],
            'rows' => [],
        ];

        foreach ($tablevalues as $item) {
            $tableData['rows'][] = [
                'id' => $item->id,
                $item->unit_number,
                $item->property->property_name.'-'. $item->property->property_location,
                $item->unit_type,
                $item->unit_type,
            ];
        }
        $unitviewData = compact('tableData', 'mainfilter', 'viewData','controller');
    //    return [

      //      'unitviewData' => $unitviewData,
      //      // Add other variables you want to return here...
       // ];

        return View('admin.CRUD.form',compact('mainfilter','tableData','controller'),$viewData,$unitviewData);
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
        $model = new  $this->model;
        // Get the list of fillable fields from the model
        $fillableFields = $model->getFillable();
        // Loop through the fillable fields and set the values from the request
        foreach ($fillableFields as $field) {
            // Make sure the field exists in the request before setting it
            if ($request->has($field)) {
                $model->$field = $request->input($field);
            }
            // Handle file upload if the current field is a file input
        if ($request->hasFile($field)) {
            $file = $request->file($field);
            $filename = date('YmdHi') . $file->getClientOriginalName();
            $file->move(base_path('resources/uploads/images'), $filename);
            $model->$field = $filename;
        }
        }
       
        $model->save();
        return redirect($this->controller['0'])->with('status', $this->controller['1'] . ' Added Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function show(Unit $unit)
    {
        $unit->load('property','unitSupervisors');
        $pageheadings = collect([
            '0' => $unit->unit_number,
            '1' => $unit->property->property_name,
            '2' => $unit->property->property_streetname,
        ]);
        $tabTitles = collect([
            'Summary',
            'Users',
         //   'Utilities',
        //    'Maintenance',
        //    'Financials',
        //    'Users',
        //    'Invoices',
        //    'Payments'
            // Add more tab titles as needed
        ]);
   
     $unitEditData =$this->edit($unit)->getData();
     $tableData = [
        'headers' => ['USER','PROPERTY', 'ROLE'],
        'rows' => [],
    ];
    $users =$unit->unitSupervisors;
    $property = $unit->property;
    foreach ($users as $user) {
        $role = $user->roles->first();
        $tableData['rows'][] = [
         //   'id' => $item->id,
            $user->firstname .' '.  $user->lastname,
            $property->property_name,
            $role->name,
           
        ];
    }
        
        $viewData = $this->formData($this->model,$unit);
      //  $unitviewData = $result['unitviewData'];
         // Render the Blade views for each tab content
         $tabContents = [];
         foreach ($tabTitles as $title) {
            if ($title === 'Summary') {
             $tabContents[] = View('admin.property.unit_'.$title,$unitEditData)->render();
            }if ($title === 'Users') {
                $tabContents[] = View('admin.property.unit_'.$title,['data' => $tableData], compact('unit'))->render();
               }
            
         }

        return View('admin.CRUD.form',compact('pageheadings','tabTitles','tabContents'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function edit(Unit $unit)
    {
        $unit->load('property');
        $specialvalue = collect([
            'property_id' => $unit->property->property_name, // Use a string for the controller name
            '1' => 'New Unit',
        ]);
        $viewData = $this->formData($this->model,$unit,$specialvalue);
        $unitEditData = compact( 'specialvalue');

        

        return View('admin.CRUD.form',$viewData);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Unit $unit)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function destroy(Unit $unit)
    {
        //
    }

    
}
