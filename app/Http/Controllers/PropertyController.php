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
use App\Services\CardService;

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
    private $cardService;

    public function __construct(TableViewDataService $tableViewDataService,FilterService $filterService, CardService $cardService)
    {
        $this->model = Property::class;

        $this->controller = collect([
            '0' => 'property', // Use a string for the controller name
            '1' => 'Property',
        ]);
        $this->tableViewDataService = $tableViewDataService;
        $this->filterService = $filterService;
        $this->cardService = $cardService;
    }



    public function index(Request $request)
    {
        // Clear previousUrl if navigating to a new  method
        session()->forget('previousUrl');
        $filters = $request->except(['tab','_token','_method']);
        $filterdata = $this->filterService->getPropertyFilters($request);
        $tablevalues = Property::with('units','propertyType')->showSoftDeleted()->ApplyFilters($filters)->get();
        $cardData = $this->cardService->propertyCard($tablevalues);
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
            $isDeleted = $item->deleted_at !== null;
            $tableData['rows'][] = [
                'id' => $item->id,
                $item->property_name . ' - ' . $item->property_streetname,
                $item->property_location,
                $item->units->count(),
                $item->propertyType->property_type,
                'isDeleted' => $isDeleted,
            ];
        }

        return view('admin.CRUD.form', [
            'tableData' => $tableData,
            'controller' => $this->controller,
            'viewData' => $viewData,
            'filterdata' => $filterdata,
            'cardData' => $cardData,
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
        // Clear previousUrl if navigating to a new create method
        session()->forget('previousUrl');
        ////VARIABLES FOR CRUD TEMPLATES
         // Eager load relationships
        $property->load([
            'amenities',
            'units',
            'users',
            'utilities',
            'paymentMethods',
            'meterReadings',
            'tickets',
            'expenses',
            'deposits',
            'settings',
            'propertyType'
        ]);
        
        $pageheadings = collect([
            '0' => $property->property_name,
            '1' => $property->property_streetname,
            '2' => $property->property_location,
        ]);
        
        $tabTitles = collect([
            'Summary',
            'Units',
            'Users',
            'Utilities',
            'Pay Methods',
            'Meter Readings',
            'Tickets',
            'Deposits',
            'Expenses',
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
        $totalExpenses = $property->expenses->sum('totalamount');
        //2. UNITS
        $units = $property->units;
        $unitTableData = $this->tableViewDataService->getUnitData($property->units);

        //3. USERS
        $users = $property->users()->distinct()->get();
        $userTableData = $this->tableViewDataService->getUserData($users);
        //3.UTILITIES
        $utilities = $property->utilities;
        $utilityController = new UtilityController();
        $utilityTableData = $utilityController->getUtilitiesData($utilities);

        // PAYMENT METHODS
        $paymentMethods = $property->paymentMethods;
        $paymentMethodTableData = $this->tableViewDataService->getPaymentMethodData($paymentMethods);

         ///4. METER READINGS
         $readings = $property->meterReadings;
        $meterReadingTableData = $this->tableViewDataService->getMeterReadingsData($readings);

        ///5. REQUESTS/TICKETS
        $tickets = $property->tickets;
        $requestTableData = $this->tableViewDataService->getTicketData($tickets);

            /// DATA FOR EXPENSES TAB
         $expenses = $property->expenses;
         $expenseTableData = $this->tableViewDataService->getExpenseData($expenses);

          
          /// DATA FOR Deposit TAB
          $deposit = $property->deposits;
          // dd($payments);
           $depositTableData = $this->tableViewDataService->getDepositData($deposit);
    
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
                $tabContents[] = View('admin.Property.show_summary', $viewData, compact('amenities', 'allamenities','specialvalue','property'))->render();
            } elseif ($title === 'Units') {
                $tabContents[] = View('admin.CRUD.index_show', ['tableData' => $unitTableData,'controller' => ['unit']], 
                compact('amenities', 'allamenities'))->render();
            } elseif ($title === 'Users') {
                $tabContents[] = View('admin.CRUD.index_show', ['tableData' => $userTableData,'controller' => ['user']], 
                compact('amenities', 'allamenities'))->render();
            } elseif ($title === 'Utilities') {
                $tabContents[] = View('admin.CRUD.index_show', ['tableData' => $utilityTableData,'controller' => ['utility']], 
                compact('amenities', 'allamenities'))->render();
            }elseif ($title === 'Pay Methods') {
                $tabContents[] = View('admin.CRUD.index_show', ['tableData' => $paymentMethodTableData,'controller' => ['payment-method']], 
                compact('id', 'model'))->render();
            }elseif ($title === 'Meter Readings') {
                $tabContents[] = View('admin.CRUD.index_show', ['tableData' => $meterReadingTableData,'controller' => ['meter-reading']], 
                compact('amenities', 'allamenities','id','model'))->render();
            }elseif ($title === 'Tickets') {
                $tabContents[] = View('admin.CRUD.index_show', ['tableData' => $requestTableData,'controller' => ['ticket']], 
                compact('amenities', 'allamenities','id','model'))->render();
            }elseif ($title === 'Deposits') {
                $tabContents[] = View('admin.CRUD.index_show', ['tableData' => $depositTableData, 'controller' => ['deposit']], compact('id','model'))->render();
            }elseif ($title === 'Expenses') {
                $tabContents[] = View('admin.CRUD.index_show', ['tableData' => $expenseTableData, 'controller' => ['expense']], compact('id','model'))->render();
            }elseif ($title === 'Settings') {
                $tabContents[] = View('admin.CRUD.tabs_horizontal_show', 
                ['tabTitles' => $settingTableData['tabTitles'], 
                'tabContents' => $settingTableData['tabContents'],
                'controller' => ['setting']], 
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
        // Retrieve the Model
        $model = $this->model::findOrFail($id);
    
        // Get all relationships defined on the model
        $relationships = ['units', 'leases','utilities','payments','paymentMethods','tickets','expenses','deposits'];
        $blockingRelationships = [];
    
        foreach ($relationships as $relationship) {
            if ($model->$relationship()->exists()) {
                $blockingRelationships[] = $relationship;
            }
        }
    
        if (!empty($blockingRelationships)) {
            $blockingRelationshipsString = implode(', ', $blockingRelationships);
            return back()->with('statuserror', 'Cannot delete ' . $this->controller['1'] . ' because the following related records exist:' . $blockingRelationshipsString . '.');
        }
    
        // Perform deletion
        $model->delete();
    
        return back()->with('status', $this->controller['1'] . ' deleted successfully.');
    }


    public function updateAmenities(Request $request, $id)
    {
        $property = Property::find($id);
        $property->amenities()->sync($request->amenities);

        return redirect()->back()->with('success', 'Amenities synced successfully!');
    }
}
