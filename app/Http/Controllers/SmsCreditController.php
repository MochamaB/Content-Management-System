<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\SmsCredit;
use App\Models\User;
use Illuminate\Http\Request;

class SmsCreditController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
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
        $users = User::with('units','roles')->visibleToUser()->excludeTenants()->get();
        $properties = Property::all();
        
         ///SESSION /////
         if (!session()->has('previousUrl')) {
            session()->put('previousUrl', url()->previous());
        }

        return View('admin.Communication.create_tariff', compact('users','properties'));
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validationRules = SmsCredit::$validation;
        $validatedData = $request->validate($validationRules);

        // Check if a record with a different credit_type already exists
        $existingDifferentType = SmsCredit::where('credit_type', '!=', $validatedData['credit_type'])->exists();

        // If another credit_type already exists, block this entry
        if ($existingDifferentType) {
            return redirect()->back()->with(
                'statuserror','A different credit type plan already exists in the system.',
            )->withInput();
        }
        
        $smsCredit = new SmsCredit();
        $smsCredit->fill($validatedData);
      
        $smsCredit->save();

        $redirectUrl = session()->pull('previousUrl', 'smsCredit');

        return redirect($redirectUrl)->with('status','Tariff  Added Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SmsCredit  $smsCredit
     * @return \Illuminate\Http\Response
     */
    public function show(SmsCredit $smsCredit)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SmsCredit  $smsCredit
     * @return \Illuminate\Http\Response
     */
    public function edit(SmsCredit $smsCredit)
    {
       
        if (!session()->has('previousUrl')) {
            session()->put('previousUrl', url()->previous());
        }

        return View('admin.Communication.edit_tariff', compact('smsCredit'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SmsCredit  $smsCredit
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SmsCredit $smsCredit)
    {
        $validatedData = $request->validate([
            'tariff' => 'required',
           
        ]);
        $smsCredit->fill($validatedData);
        $smsCredit->update();

        $redirectUrl = session()->pull('previousUrl', 'smsCredit');

        return redirect($redirectUrl)->with('status','Tariff  Edited Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SmsCredit  $smsCredit
     * @return \Illuminate\Http\Response
     */
    public function destroy(SmsCredit $smsCredit)
    {
        //
    }
}
