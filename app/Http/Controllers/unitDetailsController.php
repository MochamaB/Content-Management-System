<?php

namespace App\Http\Controllers;

use App\Models\UnitDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class unitDetailsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    protected $controller;
    protected $model;

    public function __construct()
    {
        $this->model = UnitDetail::class; 
        $this->controller = collect([
            '0' => 'unitdetail', // Use a string for the controller name
            '1' => 'New Unit Detail',
        ]);
    }

    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            'slug' => 'required', // You might want to add appropriate validation rules for 'slug' here
            'desc' => [
                'required_if:slug,photo', // Requires 'desc' if 'slug' is 'photo'
                'image',
                'mimes:jpg,png,jpeg,gif,svg',
                'dimensions:min_width=300,min_height=200,max:2048',
            ],[
                'desc.required_if' => 'The description field is required when the slug is "photo".',
                'desc.image' => 'The description must be an image.',
                'desc.mimes' => 'The description must be a JPG, PNG, JPEG, GIF, or SVG image.',
                'desc.dimensions' => 'The image is too small.',
            ]
         
        ]);
        
        
        $unitdetail = new UnitDetail();
        $unitdetail->unit_id = $request->input('unit_id');
        $unitdetail->property_id = $request->input('property_id');
        $unitdetail->slug = $request->input('slug');

        if ($request->input('slug') === 'photo') {
            if ($request->file('desc')) {
                $file = $request->file('desc');
                $filename = date('YmdHi') . $file->getClientOriginalName();
                $file->move(base_path('resources/uploads/images/property'), $filename);
                $unitdetail->desc = $filename;
            }
        } else {
            $unitdetail->desc = $request->input('desc');
        }
        $unitdetail->save();
        
        return redirect()->back()->with('status','Photo Added successfully.');
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
    public function edit($id)
    {
        //
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
    public function destroy(UnitDetail $unitdetail)
    {
        $unitdetail->delete();
        Storage::delete('resources/uploads/images/property/' . $unitdetail->desc);

        return redirect()->back()->with('status','Unitdetail deleted successfully.');
        
    }
}
