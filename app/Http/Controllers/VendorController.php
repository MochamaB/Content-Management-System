<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use App\Models\VendorSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Traits\FormDataTrait;
use App\Services\TableViewDataService;



class VendorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    use FormDataTrait;
    protected $controller;
    protected $model;
    private $tableViewDataService;

    public function __construct(TableViewDataService $tableViewDataService)
    {
        $this->model = Vendor::class;
        $this->controller = collect([
            '0' => 'vendors', // Use a string for the controller name
            '1' => 'Vendor',
        ]);

        $this->tableViewDataService = $tableViewDataService;
    }



    public function index()
    {
        $vendors = Vendor::all();
        $mainfilter =  Vendor::pluck('name')->toArray();
        //  $filterData = $this->filterData($this->model);
        $controller = $this->controller;
        $tableData = $this->tableViewDataService->getVendorData($vendors, false);
        // $userviewData = compact('tableData', 'mainfilter', 'controller');

        return View('admin.CRUD.form', compact('mainfilter', 'tableData', 'controller'));
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
        $validationRules = Vendor::$validation;
        $validatedData = $request->validate($validationRules);
        $vendor = new Vendor();
        $vendor->fill($validatedData);
        $vendor->password = 'vendor123';
        $vendor->save();

        ///CREATE SUBSCRIPTION
        $subscription = new VendorSubscription([
            'vendor_id' => $vendor->id ,
            'cycle' => 'Monthly',
            'price' => 1000,
            'subscription_status' => 'No subscription' ,
            'start_date' => now(),
            'end-date' => now()->addMonth(),
        ]);
        $subscription->save();

        return redirect('vendors')->with('status', 'Vendor Added Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Vendor $vendor)
    {
        $specialvalue = collect([
            'property_id' => $vendor->property->property_name, // Use a string for the controller name
            'vendorcategory_id' => $vendor->vendorCategory->vendor_category,
        ]);
        $viewData = $this->formData($this->model,$vendor,$specialvalue);
        
        return View('admin.CRUD.details',$viewData);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Vendor $vendor)
    {
        $specialvalue = collect([
            'property_id' => $vendor->property->property_name, // Use a string for the controller name
            'vendorcategory_id' => $vendor->vendorCategory->vendor_category,
        ]);
       $viewData = $this->formData($this->model,$vendor,$specialvalue);
        
       return View('admin.CRUD.form',$viewData);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Vendor $vendor)
    {
        $validationRules = Vendor::$validation;
        $validatedData = $request->validate($validationRules);
      //  dd($vendorCategory);
        // Fill the model with validated data
        $vendor->fill($validatedData);

        // Save the updated model to the database
        $vendor->save();
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
