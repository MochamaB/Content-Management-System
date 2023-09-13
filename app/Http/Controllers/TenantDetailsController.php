<?php

namespace App\Http\Controllers;

use App\Models\tenantdetails;
use Illuminate\Http\Request;

class TenantDetailsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        dd('tenantcontroller-index');
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
            'user_id' => 'required',
            'user_relationship' => 'required',
            'emergency_name' => 'required',
            'emergency_number' => 'required',
            'emergency_email' => 'required|email',
        ]);

        if(empty($request->session()->get('tenantdetails'))){
            $tenantdetails = new Tenantdetails();
            $tenantdetails->fill($validatedData);
            $request->session()->put('tenantdetails', $tenantdetails);
        }else{
            $tenantdetails = $request->session()->get('tenantdetails');
            $tenantdetails->fill($validatedData);
            $request->session()->put('tenantdetails', $tenantdetails);
        }

        // Redirect to the lease.create route with a success message

        return redirect()->route('lease.create', ['active_tab' => '2'])
            ->with('status', 'Tenant Details Created Successfully. Enter Rent Details');
    }
    

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\tenantdetails  $tenantdetails
     * @return \Illuminate\Http\Response
     */
    public function show(tenantdetails $tenantdetails)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\tenantdetails  $tenantdetails
     * @return \Illuminate\Http\Response
     */
    public function edit(tenantdetails $tenantdetails)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\tenantdetails  $tenantdetails
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, tenantdetails $tenantdetails)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\tenantdetails  $tenantdetails
     * @return \Illuminate\Http\Response
     */
    public function destroy(tenantdetails $tenantdetails)
    {
        //
    }
}
