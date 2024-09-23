<?php

namespace App\Http\Controllers;

use App\Models\Lease;
use App\Models\Property;
use App\Models\Setting;
use App\Models\Unit;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use App\Services\TableViewDataService;
use Illuminate\Support\Facades\Auth;
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

        return View('admin.Setting.index', compact('settings'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($model_type= null)
    {
        $modelClass =  'App\\Models\\'.$model_type;
        $setting = Setting::where('model_type', $modelClass)->get();
       
            switch ($model_type) {
                case 'Lease':
                    $leases = Lease::with(['unit', 'property'])->get();
                    $options = $leases->mapWithKeys(function ($lease) {
                        $value = $lease->property->property_name . ' - ' . optional($lease->unit)->unit_number;
                        return [$lease->id => $value];
                    })->toArray();// Convert to an array
                    break;
                case 'Property':
                    $options =  Property::pluck('property_name', 'id')->toArray();
                    break;
                default:
                    break; // or handle this case differently
            }
            
      ///SESSION /////
        if (!session()->has('previousUrl')) {
            session()->put('previousUrl', url()->previous());
        }

          
   
        return View('admin.Setting.create_override', compact('setting', 'options','modelClass'));
    }

    public function fetchSetting(Request $request)
    {

        $data = Setting::where('key', $request->key)
            ->first();

        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /// CHECK IF SETTING FOR THE MODEL_ID EXISTS
        $exists = Setting::where('name', $request->name)
            ->where('model_id', $request->model_id)
            ->where('key', $request->key)
            ->exists();
        if ($exists) {
            // Setting already exists, handle accordingly
            return redirect()->back()->with('statuserror', 'Override setting with this item already in system'); 
        }
        //GET THE SETTING ////
        $setting = Setting::where('key', $request->key)
        ->first();
        Setting::create([
            'model_type' => $setting->model_type,
            'model_id' => $request->model_id, // Lease ID 2
            'info' => $setting->info,
            'name' => $setting->name,
            'key' => $request->key,
            'value' => $request->value, // Disable notifications for this lease as well
            'description' => $setting->description,
        ]);

        $redirectUrl = session()->pull('previousUrl', 'setting');
        return redirect($redirectUrl)->with('status', 'Override Setting Added Successfully');
       
    }

   
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
        // Retrieve the model instance
        $model = Setting::findOrFail($id);
        $modelName = class_basename($model);

        // Define the relationships to check
        $relationships = [];

        // Call the destroy method from the DeletionService
        return $this->tableViewDataService->destroy($model, $relationships,  $modelName);
    }

    public function closewizard(Request $request,$routePart)
    {
        // Get all session keys
        $keys = $request->session()->all();
      //  dd($keys);

        // Loop through the keys and forget them, except for the user login and default session keys
        foreach ($keys as $key => $value) {
            if (!in_array($key, ['_token','url', '_previous', 'flash', 'login_web_' . Auth::id()])) {
                if (str_starts_with($key, 'wizard_')) {
                    $request->session()->forget($key);
                }
            }
        }

        // Redirect to the previous page or a default page
        return redirect($routePart)->with('statuserror', 'You have exited the wizard');
    }
}
