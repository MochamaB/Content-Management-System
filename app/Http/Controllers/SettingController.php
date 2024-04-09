<?php

namespace App\Http\Controllers;

use App\Models\Lease;
use App\Models\Property;
use App\Models\Setting;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use App\Services\TableViewDataService;
use Illuminate\Support\Facades\Session;

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

        $settings = Setting::all()
            ->unique('model_type')
            ->groupBy('model_type');

        return View('admin.setting.index', compact('settings'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($name = null)
    {
        if ($name) {
            $setting = Setting::where('name', $name)->first();
            if ($setting->model_type === 'App\Models\Property') {
                $options =  Property::pluck('property_name', 'id')->toArray();
            } else if ($setting->model_type === 'App\Model\Lease') {
                $options = Lease::with('units')->pluck('units.unit_number', 'id')->toArray();
            }
        } else {
        }

        Session::flash('previousUrl', request()->server('HTTP_REFERER'));

        return View('admin.setting.create', compact('name', 'setting', 'options'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $exists = Setting::where('name', $request->name)
            ->where('model_id', $request->model_id)
            ->where('key', $request->key)
            ->exists();
        if ($exists) {
            // Setting already exists, handle accordingly
            return redirect()->back()->with('statuserror', 'Override setting with this item already in system'); 
        }
        $validationRules = Setting::$validation;
        $validatedData = $request->validate($validationRules);
        $setting = new Setting();
        $setting->fill($validatedData);
        $setting->save();

        $previousUrl = Session::get('previousUrl');
        if ($previousUrl) {
            return redirect($previousUrl)->with('status', 'Your Override setting has been saved successfully');
        } else {
            return redirect('setting/' . $request->name)->with('status', ' Setting Added Successfully');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $model_type)
    {
        $namespace = 'App\\Models\\'; // Adjust the namespace according to your application structure
        // Combine the namespace with the class name
        $modelType = $namespace . $model_type;
        $setting = Setting::where('model_type', $modelType)->first();
        $pageheadings = collect([
            '0' => $model_type.' Settings',
            '1' => 'Category',
            '2' => $setting->info,
        ]);

        $tabData = $this->tableViewDataService->generateSettingTabContents($modelType, $setting);

        return view('admin.CRUD.form', [
            'pageheadings' => $pageheadings,
            'tabTitles' => $tabData['tabTitles'],
            'tabContents' => $tabData['tabContents'],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function systemsetting()
    {
        
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
        return redirect()->back()->with('statuserror', 'Override Setting cannot be edited but only deleted');
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
        $settings = Setting::find($id);
        $settings->fill($request->all());
        $settings->update();

        return redirect()->back()->with('status', 'your Global Setting hasb been updated');
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
