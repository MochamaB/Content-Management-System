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
        return View('admin.CRUD.formwizard');
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
