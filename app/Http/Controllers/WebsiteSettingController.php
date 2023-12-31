<?php

namespace App\Http\Controllers;

use App\Models\WebsiteSetting;
use Illuminate\Http\Request;

class WebsiteSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sitesettings = WebsiteSetting::first();
        return View('admin.setting.websitesetting_index',compact('sitesettings'));
    }

   
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return View('admin.setting.websitesetting_create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       // dd($request->file('company_logo'));
        $settingSite = new WebsiteSetting();
        $settingSite->fill($request->all());
       
        if($request->file('company_logo')){
            $fieldName = 'company_logo';
            $mediaCollection = 'logo';
            $settingSite->UploadNewImage($settingSite, $fieldName, $mediaCollection,$request);
        }
        if($request->file('company_flavicon')){
            $fieldName = 'company_flavicon';
            $mediaCollection = 'flavicon';
            $settingSite->UploadNewImage($settingSite, $fieldName, $mediaCollection,$request);
        }

        $settingSite->save();
        return redirect('websitesetting')->with('status','Site Settings Added Successfully');

        
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
        $id =1;
        $settingSite = WebsiteSetting::find($id);
        $settingSite->site_name = $request->input('site_name');
        $settingSite->company_name = $request->input('company_name');
        $settingSite->company_telephone = $request->input('company_telephone');
        $settingSite->company_email = $request->input('company_email');
        $settingSite->company_location = $request->input('company_location');  
        $settingSite->company_googlemaps = $request->input('company_googlemaps');
        if($request->file('company_logo')){
            $file= $request->file('company_logo');
            $filename= date('YmdHi').$file->getClientOriginalName();
            $file-> move(base_path('resources/uploads/images'), $filename);
            $settingSite['company_logo']= $filename;
        }
        if($request->file('company_flavicon')){
            $file= $request->file('company_flavicon');
            $filename= date('YmdHi').$file->getClientOriginalName();
            $file-> move(base_path('resources/uploads/images'), $filename);
            $settingSite['company_flavicon']= $filename;
        }
        $settingSite->company_aboutus = $request->input('company_aboutus');  
        $settingSite->site_currency = $request->input('site_currency');
        $settingSite->banner_desc = $request->input('banner_desc');
        $settingSite->update();

        return redirect('websitesetting')->with('status','Site Settings Updated Successfully');
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
