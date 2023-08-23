<?php

namespace App\Http\Controllers;

use App\Models\Testimonial;
use Illuminate\Http\Request;
use App\Traits\FormDataTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;


class TestimonialController extends Controller
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
        $this->model = Testimonial::class; 
        $this->controller = collect([
            '0' => 'testimonial', // Use a string for the controller name
            '1' => 'New Testimonial',
        ]);
    }

     public function testimonialData(Testimonial $testimonial = null)
            {
               
                $fields = $this->model::$fields;
                 // Initialize an empty array for $data if $testimonial is null
                $data = ($testimonial) ? [] : null;
                ////// For create and edit
                foreach ($fields as $field => $label) {
                    $data[$field] = $this->model::getFieldData($field);
                        $actualvalues = ($testimonial) ? $testimonial : null;   
                }
                //// For 

                return compact('fields','data', 'actualvalues');
            }
    
    public function index()
    {
        $tablevalues= $this->model::all();
        $mainfilter =  $this->model::pluck('client_name')->toArray();
        $viewData = $this->testimonialData();
        $controller = $this->controller;

         /// TABLE DATA ///////////////////////////
         $tableData = [
            'headers' => ['NAME', 'DESIGNATION','PICTURE', 'COMPANY','TESTIMONIAL','ACTIONS'],
            'rows' => [],
        ];

        foreach ($tablevalues as $item) {
            $showLink = url($this->controller['0'].'show' . $item->id);
            $tableData['rows'][] = [
                'id' => $item->id,
                $item->client_name,
                $item->client_title,
                '<img src="'.url('resources/uploads/images/'.$item->client_picture).'" style="width:100px;height:80px">',
                $item->client_company,
                '<div class="col-sm-3" style=" width: 300px;
                overflow: hidden;
                white-space: wrap;
                text-overflow: ellipsis;">'.$item->testimonial.'</div>',
                
            ];
        }

        return View('admin.CRUD.form',compact('mainfilter','tableData','controller'),$viewData);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $viewData = $this->testimonialData();

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
     * @param  \App\Models\Testimonial  $testimonial
     * @return \Illuminate\Http\Response
     */
    public function show(Testimonial $testimonial)
    {
        $viewData = $this->testimonialData($testimonial);


        return View('admin.CRUD.show',$viewData);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Testimonial  $testimonial
     * @return \Illuminate\Http\Response
     */
    public function edit(Testimonial $testimonial)
    {
        $viewData = $this->formData($this->model,$testimonial);


        return View('admin.CRUD.form',$viewData);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Testimonial  $testimonial
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
       
        $model = $this->model::find($id);
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
       
        $model->update();
        return redirect($this->controller['0'])->with('status', $this->controller['0'] . ' was edited Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Testimonial  $testimonial
     * @return \Illuminate\Http\Response
     */
    public function destroy(Testimonial $testimonial)
    {
        //
    }
}
