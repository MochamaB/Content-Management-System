<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Traits\FormDataTrait;
use App\Models\Property;
use App\Models\Amenity;
use App\Models\Unit;
use Illuminate\Http\Request;
use App\Http\Controllers\UnitController;
use App\Models\Setting;
use App\Services\TableViewDataService;
use App\Services\FilterService;

class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    use FormDataTrait;
    protected $controller;
    protected $model;
    private $tableViewDataService;
    protected $filterService;

    public function __construct(TableViewDataService $tableViewDataService,FilterService $filterService)
    {
        $this->model = Property::class;

        $this->controller = collect([
            '0' => 'property', // Use a string for the controller name
            '1' => 'Property',
        ]);
        $this->tableViewDataService = $tableViewDataService;
        $this->filterService = $filterService;
    }



    public function index(Request $request,)
    {
        $filters = $request->except(['tab','_token','_method']);
        $filterdata = $this->filterService->getPropertyFilters($request);
        $tablevalues = Property::ApplyFilters($filters)->get();
        //   $tablevalues = Property::withUserUnits()->get();
        $viewData = $this->formData($this->model);
     //   $cardData = $this->cardData($this->model);
      //  dd($cardData);
        $controller = $this->controller;
        /// TABLE DATA ///////////////////////////
        $tableData = [
            'headers' => ['PROPERTY', 'LOCATION', 'NO OF UNITS', 'TYPE', 'ACTIONS'],
            'rows' => [],
        ];

        foreach ($tablevalues as $item) {
            $tableData['rows'][] = [
                'id' => $item->id,
                $item->property_name . ' - ' . $item->property_streetname,
                $item->property_location,
                $item->units->count(),
                $item->propertyType->property_type,
            ];
        }

        return view('admin.CRUD.form', [
            'tableData' => $tableData,
            'controller' => $this->controller,
            'viewData' => $viewData,
            'filterdata' => $filterdata,
       //     'cardData' => $cardData,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */



    public function create()
    {
        $viewData = $this->formData($this->model);

        return View('admin.CRUD.form', $viewData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //// Data Entry validation/////////////
        if (Property::where('property_name', $request->property_name)
            ->exists()
        ) {
            return redirect('admin.property.properties_index')->with('statuserror', 'Property is already in the system');
        } else {
            $validationRules = Property::$validation;
            $validatedData = $request->validate($validationRules);
            $property = new Property;
            $property->fill($validatedData);
            $property->property_status ='Active';
            $property->save();

            return redirect()->route('property.show', $property->id)->with('status', 'Property Added Successfully');

        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Property $property)
    {
        ////VARIABLES FOR CRUD TEMPLATES
       
        $pageheadings = collect([
            '0' => $property->property_name,
            '1' => $property->property_streetname,
            '2' => $property->property_location,
        ]);
        
        $tabTitles = collect([
            'Summary',
            'Units',
            'Utilities',
            'Meter Readings',
            'Tickets',
            'Settings'
            //    'Maintenance',
            //    'Financials',
            //    'Users',
            //    'Invoices',
            //    'Payments'
            // Add more tab titles as needed
        ]);
        //1. AMENITIES
        $amenities = $property->amenities;
        $allamenities = Amenity::all();
        //2. UNITS
        $units = $property->units;
     //   $unitController = new UnitController();
        $unitTableData = $this->tableViewDataService->getUnitData($units);
     //   $unitTableData = $unitController->getUnitData($units);
      //  $mainfilter = $unitController->index()->getData()['mainfilter'];
        //3.UTILITIES
        $utilities = $property->utilities;
        $utilityController = new UtilityController();
        $utilityTableData = $utilityController->getUtilitiesData($utilities);

         ///4. METER READINGS
         $readings = $property->meterReadings;
         //   $unitController = new UnitController();
            $meterReadingTableData = $this->tableViewDataService->getMeterReadingsData($readings);

        ///4. REQUESTS
        $tickets = $property->tickets;
        //   $unitController = new UnitController();
           $requestTableData = $this->tableViewDataService->getTicketData($tickets);
    
         //5. SETTINGS
         $namespace = 'App\\Models\\'; // Adjust the namespace according to your application structure
         // Combine the namespace with the class name
         $modelType = $namespace . 'Property';
         $setting = $property->settings;
         $setting = Setting::where('model_type', $modelType)->first();
        
         $settingTableData = $this->tableViewDataService->generateSettingTabContents($modelType, $setting);
       
         $model = 'properties';
         $id = $property;
        // dd($property);

        ///// Used to Set Property TYpe name in the edit view.///
        $specialvalue = collect([
            'property_type' => $property->propertyType->property_type, // Use a string for the controller name
            '1' => ' Unit',
        ]);
        $viewData = $this->formData($this->model, $property,$specialvalue);
       
        //  $unitviewData = $result['unitviewData'];
        // Render the Blade views for each tab content
        $tabContents = [];
        foreach ($tabTitles as $title) {
            if ($title === 'Summary') {
                $tabContents[] = View('admin.property.show_' . $title, $viewData, compact('amenities', 'allamenities','specialvalue'))->render();
            } elseif ($title === 'Units') {
                $tabContents[] = View('admin.CRUD.index_show', ['tableData' => $unitTableData,'controller' => ['unit']], 
                compact('amenities', 'allamenities'))->render();
            } elseif ($title === 'Utilities') {
                $tabContents[] = View('admin.CRUD.index_show', ['tableData' => $utilityTableData,'controller' => ['utility']], 
                compact('amenities', 'allamenities'))->render();
            }elseif ($title === 'Meter Readings') {
                $tabContents[] = View('admin.CRUD.index_show', ['tableData' => $meterReadingTableData,'controller' => ['meter-reading']], 
                compact('amenities', 'allamenities','id','model'))->render();
            }elseif ($title === 'Tickets') {
                $tabContents[] = View('admin.CRUD.index_show', ['tableData' => $requestTableData,'controller' => ['ticket']], 
                compact('amenities', 'allamenities','id'))->render();
            }elseif ($title === 'Settings') {
                $tabContents[] = View('admin.CRUD.tabs_horizontal_show', ['tabTitles' => $settingTableData['tabTitles'], 'tabContents' => $settingTableData['tabContents'],'controller' => ['setting']], 
                compact('amenities', 'allamenities','model','id'))->render();
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
    public function edit(Property $property)
    {
        
        $viewData = $this->formData($this->model, $property);
        $pageheadings = collect([
            '0' => $property->property_name,
            '1' => $property->property_streetname,
            '2' => $property->property_location,
        ]);

        return View('admin.CRUD.form', compact('pageheadings'), $viewData);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Property $property, Request $request)
    {
        $validationRules = Property::$validation;
        $validatedData = $request->validate($validationRules);
        $property->fill($validatedData);
        $property->update();

        return redirect($this->controller['0'])->with('status', $this->controller['1'] . ' Edited Successfully');
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


    public function updateAmenities(Request $request, $id)
    {
        $property = Property::find($id);
        $property->amenities()->sync($request->amenities);

        return redirect()->back()->with('success', 'Amenities synced successfully!');
    }
}
