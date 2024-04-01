<?php

namespace App\Http\Controllers;

use App\Models\Lease;
use App\Models\Property;
use App\Models\Setting;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use App\Services\TableViewDataService;

class SettingController extends Controller
{
    private $tableViewDataService;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(TableViewDataService $tableViewDataService)
    {

        $this->tableViewDataService = $tableViewDataService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getSettingData($setting)
    {
        $tableData = [
            'headers' => ['MODEL', 'INSTANCE', 'NAME', 'VALUE', 'DESCRIPTION', 'ACTIONS'],
            'rows' => [],
        ];

        foreach ($setting as $item) {
            $tableData['rows'][] = [
                'id' => $item->id,
                $item->settingable_type,
                $item->settingable_id,
                $item->setting_name,
                $item->setting_value,
                $item->setting_description,
            ];
        }

        return $tableData;
    }
    public function index()
    {

        $settings =  $reports = Setting::all()
            ->unique('name')
            ->groupBy('module');

        return View('admin.setting.index', compact('settings'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($name = null)
    {
        $options = [];
        if ($name) {
            $setting = Setting::where('name', $name)->first();
            if ($setting->model_type === 'App\\Model\\Property') {
                $options =  Property::pluck('property_name', 'id')->toArray();
            }else if($setting->model_type === 'App\\Model\\Lease'){
                $options = Lease::with('units')->pluck('units.unit_number', 'id')->toArray();
            }
        } else {
        }

        return View('admin.setting.create', compact('name', 'setting','options'));
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
            'settingable_id' => $request->settingable_id,
            'setting_name' => $request->setting_name,
            'setting_value' => $request->setting_value,
            'setting_description' => $request->setting_description,
        ]);

        return redirect('setting/' . $request->settingable_type)->with('status', ' Setting Added Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $name)
    {

        $setting = Setting::where('name', $name)->first();
        $pageheadings = collect([
            '0' => $setting->name,
            '1' => 'Category',
            '2' => $setting->module,
        ]);

        $tabTitles = collect([
            'Global Settings',
            'Overrides',

        ]);

        $controller = 'setting';

        $globalSettings = Setting::where('name', $setting->name)
            ->whereNull('model_id')
            ->get();
        $individualSetting = Setting::where('name', $setting->name)
            ->whereNotNull('model_id')
            ->get();

        $settingsTableData = $this->tableViewDataService->getSettingData($individualSetting, true);
        $id = $name;

        $tabContents = [];
        foreach ($tabTitles as $title) {
            if ($title === 'Global Settings') {
                $tabContents[] = View('admin.setting.global_settings', compact('setting', 'globalSettings'))->render();
            } elseif ($title === 'Overrides') {
                $tabContents[] = View('admin.CRUD.index_show', ['tableData' => $settingsTableData, 'controller' => ['setting']], compact('id'))->render();
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

    public function systemsetting()
    {
        // Get all .env variables
        $envVariables = $_ENV;

        // Pass the variables to the view
        return view('admin.Setting.systemsetting', compact('envVariables'));
    }

    public function updateSystemSettings(Request $request)
    {
        $data = $request->except(['_token']); // Exclude the CSRF token

        // Loop over the data and update the .env file
        foreach ($data as $key => $value) {
            $this->changeEnv($key, $value);
        }

        return back()->with('status', 'System Settings updated successfully.');
    }
    protected function changeEnv($key, $value)
    {
        $path = base_path('.env');
        if (file_exists($path)) {
            file_put_contents($path, preg_replace(
                "/^{$key}=.*/m",
                "{$key}={$value}",
                file_get_contents($path)
            ));
        }
    }

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
