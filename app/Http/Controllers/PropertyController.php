<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Traits\FormDataTrait;
use App\Models\Property;
use App\Models\Amenity;
use App\Models\Unit;
use Illuminate\Http\Request;
use App\Http\UnitController;

class PropertyController extends Controller
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
        $this->model = Property::class; 
        
        $this->controller = collect([
            '0' => 'property', // Use a string for the controller name
            '1' => 'New Property',
        ]);
    }

 

    public function index()
    {
        $user = Auth::user();
        if (Gate::allows('view-all', $user)) {
            $tablevalues= Unit::viewallunits();
        }else{
            $tablevalues =$user->assignedunits();
        }

        
        $mainfilter =  $this->model::pluck('property_name')->toArray();
        $viewData = $this->formData($this->model);
        $controller = $this->controller;
        /// TABLE DATA ///////////////////////////
        $tableData = [
            'headers' => ['PROPERTY', 'LOCATION','MANAGER', 'TYPE','ACTIONS'],
            'rows' => [],
        ];

        foreach ($tablevalues as $propertyId  => $item) {
            $property = $item->first()->property;
            $tableData['rows'][] = [
                'id' => $property->id,
                $property->property_name.' - '.$property->property_streetname,
                $property->property_location,
                $property->property_manager,
                $property->property_type,
            ];
        }

        return View('admin.CRUD.form',compact('mainfilter','tableData','controller'),$viewData, 
        ['controller' => $this->controller]);
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
         //// Data Entry validation/////////////
         if (Property::where('property_name', $request->property_name)
         ->exists()) {
            return redirect('admin.property.properties_index')->with('statuserror','Property is already in the system');
         }
         else
         {

             $property = new Property;
             $property->property_name = $request->input('property_name');
             $property->property_type = $request->input('property_type');
             $property->property_location = $request->input('property_location');
             $property->property_streetname = $request->input('property_streetname');
             $property->property_manager = $request->input('property_manager');
             $property->property_status = $request->input('property_status'); 
             $property->save();
             
             return redirect('property')->with('status','Property Added Successfully');
         }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Property $property)
    {
        ////VARIABLES FOR CRUD TEMPLATES

        $pageheadings = collect([
            '0' => $property->property_name,
            '1' => $property->property_streetname,
            '2' => $property->property_location,
        ]);
        $tabTitles = collect([
            'Summary',
            'Units',
         //   'Utilities',
        //    'Maintenance',
        //    'Financials',
        //    'Users',
        //    'Invoices',
        //    'Payments'
            // Add more tab titles as needed
        ]);
        $amenities = $property->amenities;
        $allamenities = Amenity::all();

        $result = app('App\Http\Controllers\UnitController')->index($property);
        // Access the individual variables from the $result array
        $unitviewData = $result->getData();

        // Access the variables directly from the array
        $tableData = $unitviewData['tableData'];
        $mainfilter = $unitviewData['mainfilter'];
     //   $viewData = $unitviewData['viewData'];


  
        

        
        $viewData = $this->formData($this->model,$property);
      //  $unitviewData = $result['unitviewData'];
         // Render the Blade views for each tab content
         $tabContents = [];
         foreach ($tabTitles as $title) {
            if ($title === 'Summary') {
             $tabContents[] = View('admin.property.show_'.$title,$viewData,compact('amenities','allamenities'))->render();
            }if ($title === 'Units') {
                $tabContents[] = View('admin.CRUD.index',$unitviewData, 
                compact('amenities', 'allamenities'))->render();
               }
            
         }

        return View('admin.CRUD.form',compact('pageheadings','tabTitles','tabContents'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Property $property)
    {
        $viewData = $this->formData($this->model,$property);
        $pageheadings = collect([
            '0' => $property->property_name,
            '1' => $property->property_streetname,
            '2' => $property->property_location,
        ]);

        return View('admin.CRUD.form',compact('pageheadings'),$viewData);
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


    public function updateAmenities(Request $request, $id){
        $property = Property::find($id);
        $property->amenities()->sync($request->amenities);

    return redirect()->back()->with('success', 'Amenities synced successfully!');


    }
    
}
