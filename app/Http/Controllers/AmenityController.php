<?php

namespace App\Http\Controllers;

use App\Models\Amenity;
use Illuminate\Http\Request;
use App\Models\Property;
use App\Traits\FormDataTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
Use App\Policies\DeletePolicy;
use App\Services\TableViewDataService;

class AmenityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    use FormDataTrait;
    protected $controller;
    protected $model;
    protected $user;
    private $tableViewDataService;

    public function __construct(TableViewDataService $tableViewDataService)
    {
        $this->model = Amenity::class; 
        $this->controller = collect([
            '0' => 'amenity', // Use a string for the controller name
            '1' => ' Amenity',
        ]);
        $this->user = Auth::user();
        $this->tableViewDataService = $tableViewDataService;
    }


    public function index()
    {
        
        $amenities = Amenity::with('properties')->showSoftDeleted()->get();
        $viewData = $this->formData($this->model);
        $controller = $this->controller;
        $tableData = $this->tableViewDataService->getAmenitiesData($amenities,false);
         /// TABLE DATA ///////////////////////////
        return View('admin.CRUD.form',compact('tableData','controller'),$viewData);
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
        $model = new Amenity();
        // Get the list of fillable fields from the model
        $fillableFields = $model->getFillable();
        // Loop through the fillable fields and set the values from the request
        foreach ($fillableFields as $field) {
            // Make sure the field exists in the request before setting it
            if ($request->has($field)) {
                $model->$field = $request->input($field);
            }
        }
        $model->save();
        return redirect($this->controller['0'])->with('status', $this->controller['1'] . ' Added Successfully');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Amenity $amenity)
    {
        
        $viewData = $this->formData($this->model,$amenity);

        return View('admin.CRUD.form',$viewData);
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
      
        $model = Amenity::find($id);
        // Get the list of fillable fields from the model
        $model->update($request->all());
        return redirect($this->controller[0])->with('status', $this->controller[1] . ' was edited Successfully');     
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
         // Retrieve the model instance
         $model = $this->model::findOrFail($id);

         // Define the relationships to check
         $relationships = ['properties'];
 
         // Call the destroy method from the DeletionService
         return $this->tableViewDataService->destroy($model, $relationships, 'Model Name');
    }
}
