<?php

namespace App\Http\Controllers;

use App\Models\VendorCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Traits\FormDataTrait;


class VendorCategoryController extends Controller
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
         $this->model = VendorCategory::class;
 
         $this->controller = collect([
             '0' => 'vendor-category', // Use a string for the controller name
             '1' => ' Vendor Categories',
         ]);
     }
    public function index()
    {
        $user = Auth::user();
        if (Gate::allows('view-all', $user)) {
            $tablevalues = VendorCategory::all();
        } else {
            $tablevalues = VendorCategory::all();
        }

        $mainfilter =  $this->model::pluck('vendor_category')->toArray();
        $viewData = $this->formData($this->model);
        $controller = $this->controller;
        /// TABLE DATA ///////////////////////////
        $tableData = [
            'headers' => ['TYPE', 'CATEGORY','ACTIONS'],
            'rows' => [],
        ];

        foreach ($tablevalues as  $item) {
            $isDeleted = $item->deleted_at !== null;
            $tableData['rows'][] = [
                'id' => $item->id,
                $item->vendor_type,
                $item->vendor_category,
                'isDeleted' => $isDeleted,

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
            'vendor_type' => 'required',
            'vendor_category' => 'required',
           
        ]);
        $vendorcategory= new VendorCategory();
        $vendorcategory->fill($validatedData);
        $vendorcategory->save();

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
    public function edit(VendorCategory $vendorCategory)
    {
      //  dd($vendorCategory);
        $viewData = $this->formData($this->model,$vendorCategory,);
        
        return View('admin.CRUD.form',$viewData);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, VendorCategory $vendorCategory)
    {
        $validatedData = $request->validate([
            'vendor_type' => 'required',
            'vendor_category' => 'required',
           
        ]);
      //  dd($vendorCategory);
        // Fill the model with validated data
        $vendorCategory->fill($validatedData);

        // Save the updated model to the database
        $vendorCategory->save();
        return redirect($this->controller['0'])->with('status', $this->controller['1'] . ' Edited Successfully');
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
}
