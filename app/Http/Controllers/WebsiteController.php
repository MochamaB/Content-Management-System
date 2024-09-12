<?php

namespace App\Http\Controllers;

use App\Models\Website;
use Illuminate\Http\Request;

class WebsiteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tabTitles = collect([
            'Overview',
            'Branding',
            'Logos',
        ]);

        $tabContents = [];

        $sitesettings = Website::first();
        $tabContents = [];
        foreach ($tabTitles as $title) {
            if ($title === 'Overview') {
                $tabContents[] = View('admin.Setting.overview', compact('sitesettings'))->render();
            } elseif ($title === 'Branding') {
                $tabContents[] = View('admin.Setting.branding', compact('sitesettings'))->render();
            }elseif ($title === 'Logos') {
                $tabContents[] = View('admin.Setting.Logo', compact('sitesettings'))->render();
            }
        }


        return View('admin.CRUD.show', compact('tabTitles', 'tabContents'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return View('admin.Setting.website_create');
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
        $settingSite = new Website();
        $settingSite->fill($request->all());

        if ($request->file('company_logo')) {
            $fieldName = 'company_logo';
            $mediaCollection = 'logo';
            $settingSite->UploadNewImage($settingSite, $fieldName, $mediaCollection, $request);
        }
        if ($request->file('company_flavicon')) {
            $fieldName = 'company_flavicon';
            $mediaCollection = 'flavicon';
            $settingSite->UploadNewImage($settingSite, $fieldName, $mediaCollection, $request);
        }

        $settingSite->save();
        return redirect('Website')->with('status', 'Site Settings Added Successfully');
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
        $settingSite = Website::find($id);
        $settingSite->fill($request->all());

        if ($request->file('company_logo')) {
            $settingSite->clearMediaCollection('logo');
            $settingSite->addMedia($request->file('company_logo'))->toMediaCollection('logo');
        }

        if ($request->file('company_flavicon')) {
            $settingSite->clearMediaCollection('flavicon');
            $settingSite->addMedia($request->file('company_flavicon'))->toMediaCollection('flavicon');
        }

        $settingSite->update();
        return redirect('Website')->with('status', 'Site Settings Updated Successfully');
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
