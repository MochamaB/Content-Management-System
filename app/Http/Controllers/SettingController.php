<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $modules = Permission::all();
        $groupedmodules = $modules->groupBy('module');
        $settings = collect([
            'Property' => ['icon' => 'bank', 'submodules' => ['property', 'unit', 'utility']],
            'Leasing' => ['icon' => 'key','submodules' => ['lease']],
            'Accounting' => ['icon' => 'cash-usd', 'submodules' => ['chartofaccount']],
            'Communication' => ['icon' => 'email-open', 'submodules' => ['',]],
            'Maintenance' => ['icon' => 'broom', 'submodules' => ['',]],
            'Tasks' => ['icon' => 'timetable', 'submodules' => ['',]],
            'Files' => ['icon' => 'file-multiple', 'submodules' => ['',]],
            'Settings' => ['icon' => 'settings', 'submodules' => ['setting','websitesetting']],
            'User' => ['icon' => 'account-circle-outline', 'submodules' => ['user', 'role', 'permission']],
        ]);
        return View('admin.setting.index',compact('settings'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($model)
    {
       
        return View('admin.setting.create',compact('model'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Setting::create([
            'settingable_type' => $request->settingable_type,
            'settingable_id' => 0,
            'setting_name' => $request->setting_name,
            'setting_value' => $request->setting_value,
            'setting_description' => $request->setting_description,
        ]);

        return redirect('setting/'.$request->settingable_type)->with('status',' Setting Added Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($model)
    {

        $pageheadings = collect([
            '0' => $model,
            '1' =>"" ,
            '2' => "" ,
        ]);
        
        $tabTitles = collect([
            'Global Settings',
            'Overrides',
         
        ]);

        $controller = $model;

        $globalSettings = Setting::where('settingable_type', $model)
        ->where('settingable_id', 0)
        ->get();
        
        $tabContents = [];
        foreach ($tabTitles as $title) {
            if ($title === 'Global Settings') {
                $tabContents[] = View('admin.setting.global_settings', compact('globalSettings','model'))->render();
            } elseif ($title === 'Overrides') {
                $tabContents[] = View('admin.setting.global_settings', compact('globalSettings','model'))->render();
            }
        }
        return View('admin.CRUD.form', compact('pageheadings', 'tabTitles', 'tabContents'));
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
    public function destroy($id)
    {
        //
    }
}
