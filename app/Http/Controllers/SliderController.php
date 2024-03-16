<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Slider;
use App\Traits\FormDataTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class SliderController extends Controller
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
        $this->model = Slider::class; 
        $this->controller = collect([
            '0' => 'slider', // Use a string for the controller name
            '1' => ' Slider',
        ]);
    }

     public function sliderData(Slider $slider = null)
            {
               
                $fields = $this->model::$fields;
                 // Initialize an empty array for $data if $testimonial is null
                $data = ($slider) ? [] : null;
                ////// For create and edit
                foreach ($fields as $field => $label) {
                    $data[$field] = $this->model::getFieldData($field);
                        $actualvalues = ($slider) ? $slider : null;   
                }
                //// For 

                return compact('fields','data', 'actualvalues');
            }
    
    public function index()
    { 
        $tablevalues= $this->model::all();
        $mainfilter =  $this->model::pluck('id')->toArray();
        $viewData = $this->sliderData();
        $controller = $this->controller;

        $tableData = [
            'headers' => ['ID', 'PICTURE','DESCRIPTION', 'INFORMATION','ACTIONS'],
            'rows' => [],
        ];

        foreach ($tablevalues as $item) {
            $tableData['rows'][] = [
                'id' => $item->id,
                $item->id,
                '<img src="'.$item->getFirstMediaUrl('slider', 'thumb').'" style="width:350px;height:200px">',
                '<div class="col-sm-3" style=" width: 300px;
                overflow: hidden;
                white-space: wrap;
                text-overflow: ellipsis;">'.$item->slider_desc.'</div>',
                '<div class="col-sm-3" style=" width: 300px;
                overflow: hidden;
                white-space: wrap;
                text-overflow: ellipsis;">'.$item->slider_info.'</div>', // Apply the word-wrap class here
                 // Add action buttons to each row
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
        $viewData = $this->sliderData();

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
            'slider_title' => 'required',
            'slider_picture' => 'required|image|mimes:jpg,png,jpeg,gif,svg,jfif|dimensions:min_width=1500,min_height=700,max:2048',
            'slider_desc' => 'nullable',
            'slider_info' => 'nullable',
        ], [
            'slider_picture.required' => 'Please upload an image.',
            'slider_picture.image' => 'Invalid image format. Only JPG, PNG, JPEG, GIF, or SVG allowed.',
            'slider_picture.dimensions' => 'Slider Image dimensions should be at least 3500x2500 pixels.',
            'slider_picture.max' => 'Image size should not exceed 2MB.',
        ]);

        $slider = new Slider();
        $slider->fill($validatedData);

        if ($request->file('slider_picture')) {
            $fieldName = 'slider_picture';
            $mediaCollection = 'slider';
            $slider->UploadNewImage($slider, $fieldName, $mediaCollection, $request);
        }
        
        $slider->save();
        return redirect('slider')->with('status', 'Slider Added Successfully');
        
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
    public function edit(Slider $slider)
    {
        $mediaCollection = 'slider'; 
        $viewData = $this->formData($this->model,$slider);
        $viewData['mediaCollection'] = $mediaCollection;

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
        $slider = Slider::find($id);
        $slider->fill($request->all());

        if ($request->file('slider_picture')) {
            $slider->clearMediaCollection('slider');
            $slider->addMedia($request->file('slider_picture'))->toMediaCollection('slider');
        }

        $slider->update();
        return redirect('slider')->with('status', 'Slider Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $slider = Slider::find($id);
        $slider->delete();

        return redirect()->back()->with('status', 'Slider deleted successfully.');
    }
}
