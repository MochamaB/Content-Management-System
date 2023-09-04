<?php

namespace App\Http\Controllers;

use App\Models\lease;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Traits\FormDataTrait;

class LeaseController extends Controller
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
        $this->model = Lease::class; 
        
        $this->controller = collect([
            '0' => 'lease', // Use a string for the controller name
            '1' => 'New Lease',
        ]);
    }
    public function index()
    {
        $user = Auth::user();
        if (Gate::allows('view-all', $user)) {
            $tablevalues= lease::all();
        }else{
            $tablevalues = lease::all();
        }
 
        $mainfilter =  $this->model::pluck('status')->toArray();
        $viewData = $this->formData($this->model);
        $controller = $this->controller;
        /// TABLE DATA ///////////////////////////
        $tableData = [
            'headers' => ['LEASE', 'TYPE','STATUS','ACTIONS'],
            'rows' => [],
        ];

        foreach ($tablevalues as  $item) {
            $tableData['rows'][] = [
                'id' => $item->id,
                $item->property_id,
                $item->lease_period,
                $item->status,
              
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
        
        $tabTitles = collect([
            'Lease Details',
            'Tenant $ Cosigners',
            'Rent',
            'Utilities',
            'Terms & Conditions',
        ]);
        $tabContents = [];
        foreach ($tabTitles as $title) {
            if ($title === 'Lease Details') {
                $tabContents[] = View('admin.lease.leasedetails',$viewData)->render();
            } elseif ($title === 'Tenant $ Cosigners') {
                $tabContents[] = View('admin.lease.tenantdetails')->render();
            } elseif ($title === 'Rent') {
                $tabContents[] = View('admin.lease.rent')->render();
            } 
        }
       
        return View('admin.lease.lease',compact('tabTitles','tabContents'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\lease  $lease
     * @return \Illuminate\Http\Response
     */
    public function show(lease $lease)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\lease  $lease
     * @return \Illuminate\Http\Response
     */
    public function edit(lease $lease)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\lease  $lease
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, lease $lease)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\lease  $lease
     * @return \Illuminate\Http\Response
     */
    public function destroy(lease $lease)
    {
        //
    }
}
