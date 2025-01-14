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
use App\Services\DashboardService;
use App\Actions\UploadMediaAction;

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
    private $dashboardService;
    protected $uploadMediaAction;

    public function __construct(TableViewDataService $tableViewDataService,FilterService $filterService, CardService $cardService,
    DashboardService $dashboardService, UploadMediaAction $uploadMediaAction,)
    {
        $this->model = Property::class;

        $this->controller = collect([
            '0' => 'property', // Use a string for the controller name
            '1' => 'Property',
        ]);
        $this->tableViewDataService = $tableViewDataService;
        $this->filterService = $filterService;
        $this->cardService = $cardService;
        $this->dashboardService = $dashboardService;
        $this->uploadMediaAction = $uploadMediaAction;

    }



    public function index(Request $request)
    {
        // Clear previousUrl if navigating to a new  method
        session()->forget('previousUrl');
        $filters = $request->except(['tab','_token','_method']);
        $filterdata = $this->filterService->getPropertyFilters($request);
        $tablevalues = Property::with(['units', 'propertyType', 'amenities', 'utilities', 'users', 'paymentMethods','vendors', 'sliders'])
        ->showSoftDeleted()
        ->ApplyFilters($filters)
        ->get();
      //  $cardData = $this->cardService->propertyCard($tablevalues);
        //   $tablevalues = Property::withUserUnits()->get();
        $viewData = $this->formData($this->model);
        $dashboardConfig = $this->dashboard($tablevalues);
     //   $cardData = $this->cardData($this->model);
      //  dd($cardData);
        $controller = $this->controller;
        // PROGRESS DATA
        
        /// TABLE DATA ///////////////////////////
        $tableData = [
            'headers' => ['PROPERTY', 'NO OF UNITS', 'TYPE', 'SETUP PROGRESS',''],
            'rows' => [],
        ];

        foreach ($tablevalues as $item) {
            $isDeleted = $item->deleted_at !== null;
             // Progress calculation
            $relatedCounts = [
                'amenities' => $item->amenities->count(),
                'units' => $item->units->count(),
                'utilities' => $item->utilities->count(),
                'users' => $item->users->count(),
                'paymentMethods' => $item->paymentMethods->count(),
                'vendors' => $item->vendors->count(),
                'sliders' => $item->sliders->count(),
            ];
            $completed = array_sum($relatedCounts);
            $expectedCounts = [
                'amenities' => 10, // Adjust these values based on your requirements
                'units' => 20,
                'utilities' => 5,
                'users' => 15,
                'paymentMethods' => 5,
                'vendors' => 7,
                'sliders' => 10,
            ];
            $total = array_sum($expectedCounts); // Total expected setup items
            $progressPercentage = ($total > 0) ? round(min(($completed / $total) * 100, 100)) : 0;
            // Generate the progress bar HTML
            $progressBarHtml = '<div>
                    <div class="d-flex justify-content-between align-items-center mb-1 max-width-progress-wrap">
                        <p class="text-success">' . $progressPercentage . '%</p>
                        <p>' . $completed . '/' . $total . '</p>
                    </div>
                    <div class="progress progress-md">
                        <div class="progress-bar bg-success" role="progressbar" 
                            style="width: ' . $progressPercentage . '%;" 
                            aria-valuenow="' . $progressPercentage . '" 
                            aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>
                </div>';
            /// Default Property Image
            $mediaURL = $item->getFirstMediaUrl('property-photo');
            if ($mediaURL) {
                $coverimage = url($item->getFirstMediaUrl('property-photo'));
            } else {
                $coverimage = url('uploads/default/defaultproperty2.jpg');
            }
            //url('resources/uploads/images/' . Auth::user()->profilepicture ?? 'avatar.png');
            $propertyDetails =     '<div class="d-flex "> <img src="' . $coverimage . '" alt="">
            <div>
             <h6 style ="padding:0.1rem 0.1rem">' .  $item->property_name . ' - ' . $item->property_location.'</h6>
                <p class="text-muted" style ="padding:0.1rem 0.1rem"> <i class="mdi mdi-map-marker mr-1" style="vertical-align: middle;font-size:1.4rem"></i>'. $item->property_streetname .
                '</p>
            </div>
          </div>';

            $tableData['rows'][] = [
                'id' => $item->id,
               // $item->property_name . ' - ' . $item->property_streetname,
                $propertyDetails,
                $item->units->count(),
                $item->propertyType->property_type,
                $progressBarHtml,
                'isDeleted' => $isDeleted,
            ];
        }
       
        return view('admin.CRUD.form', [
            'tableData' => $tableData,
            'controller' => $this->controller,
            'viewData' => $viewData,
            'filterdata' => $filterdata,
            'dashboardConfig' => $dashboardConfig
        ]);
    }
     ///TOP DASHBOARD DATA FUNCTION
    protected function dashboard($data)
    {
        return [
            'rows' => [
                [
                    'columns' => [
                        [
                            'width' => 'col-md-9 col-sm-12',
                            'component' => 'admin.Dashboard.widgets.card',
                            'data' => [
                                'cardData' => $this->dashboardService->propertyCard($data),
                                'title' => 'Overview'
                            ]
                        ],
                        [
                            'width' => 'col-md-3 col-sm-12',
                            'component' => 'admin.Dashboard.charts.circleProgressChart',
                            'data' => [
                                'percentage' =>  $this->dashboardService->propertyOccupancyRate($data),
                                'title' => 'Occupancy Rate'
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */



    public function create()
    {
        $viewData = $this->formData($this->model);
        $informationSecondary = 'Upload all files associated with the property such as pictures, videos and documents
                        like the lease agreement and rules and regulations ';

        return View('admin.CRUD.form',compact('informationSecondary'), $viewData);
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
          //  $property->property_status ='Active';
          //// UPLOAD FILES
          if ($request->hasFile('uploaded_files')) {
            $uploadedFiles = $request->file('uploaded_files', []);
            $this->uploadMediaAction->UploadFile($uploadedFiles, $property);
            }
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
            'events',
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
            'Utilities',
            'Users',
            'Pay Methods',
            'Expenses',
            'Deposits',
            'Events Audit',
            'Tickets',
            'Meter Readings',
            'Settings'
            //    'Maintenance',
            //    'Financials',
            //    'Users',
            //    'Invoices',
            //    'Payments'
            // Add more tab titles as needed
        ]);

        //1. SUMMARY 
        $dashboardConfig = $this->summaryDashboard($property);
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

        // EVENTS
        $events = $property->events;
        $eventTableData = $this->tableViewDataService->getAuditData($events);


        //3.UTILITIES
        $utilities = $property->utilities;
        $utilityTableData = $this->tableViewDataService->getUtilityData($utilities);

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
                $tabContents[] = View('admin.Property.show_summary',compact('amenities', 'allamenities','dashboardConfig','property'));
            } elseif ($title === 'Units') {
                $tabContents[] = View('admin.CRUD.index_show', ['tableData' => $unitTableData,'controller' => ['unit']], 
                compact('amenities', 'allamenities'))->render();
            }elseif ($title === 'Events Audit') {
                $tabContents[] = View('admin.CRUD.table', ['data' => $eventTableData,'controller' => ['audit']], 
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

    //// FUNCTION FOR SUMMARY PAGE /////////
    protected function summaryDashboard($data)
    {
        return [
            'rows' => [
                [
                    'columns' => [
                        [
                            'width' => 'col-md-9 col-sm-12',
                            'component' => 'admin.Dashboard.widgets.card',
                            'data' => [
                                'cardData' =>$this->dashboardService->unitCard($data->units),
                                'title' => 'Overview'
                            ]
                        ],
                        [
                            'width' => 'col-md-3 col-sm-12',
                            'component' => 'admin.Dashboard.charts.circleProgressChart',
                            'data' => [
                                'percentage' =>   $this->dashboardService->unitOccupancyRate($data->units),
                                'title' => 'Occupancy Rate'
                            ]
                        ]
                    ]
                ]
            ]
        ];
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
        // Fetch existing media
        $collectionNames = ['images', 'documents', 'videos']; // Add more as needed
        $existingMedia = collect(); // Initialize an empty collection
        
        foreach ($collectionNames as $collectionName) {
            $existingMedia = $existingMedia->merge($property->getMedia($collectionName));
        }
     //   dd($existingMedia);



        return View('admin.CRUD.form', compact('pageheadings', 'existingMedia'), $viewData);
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

        /// FILE UPLOAD AND DELETE

        // Check if deleted_files is filled or uploaded_files are present
        if (($request->filled('deleted_files') || $request->hasFile('uploaded_files'))) {
            // Proceed with your logic, since at least one is true
            if ($request->filled('deleted_files')) {
                 $removedFilesIds = explode(',', $request->input('deleted_files'));
                 $this->uploadMediaAction->deleteFile($removedFilesIds, $property);
            }

            if ($request->hasFile('uploaded_files')) {
                $uploadedFiles = $request->file('uploaded_files', []);
                $this->uploadMediaAction->UploadFile($uploadedFiles, $property);
                }
        }
       
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
         // Retrieve the model instance
         $model = $this->model::findOrFail($id);
         $modelName = class_basename($model);

            // Get the relationships from the model
        $relationships = $model->getRelationships();
 
         // Call the destroy method from the DeletionService
         return $this->tableViewDataService->destroy($model, $relationships, $modelName);

    }


    public function updateAmenities(Request $request, $id)
    {
        $property = Property::find($id);
        $property->amenities()->sync($request->amenities);

        return redirect()->back()->with('success', 'Amenities synced successfully!');
    }
}
