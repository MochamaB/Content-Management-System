<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Property;
use Illuminate\Http\Request;
use App\Traits\FormDataTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Events\AssignUserToUnit;
use App\Http\Controllers\MeterReadingController;
use App\Services\InvoiceService;
use App\Services\CardService;
use App\Services\DashboardService;
use App\Services\TableViewDataService;
use App\Services\FilterService;


class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    use FormDataTrait;
    protected $controller;
    protected $model;
    private $invoiceService;
    private $tableViewDataService;
    private $cardService;
    private $filterService;
    private $dashboardService;

    public function __construct(InvoiceService $invoiceService,TableViewDataService $tableViewDataService,
    CardService $cardService,FilterService $filterService, DashboardService $dashboardService)
    {
        $this->model = Unit::class;
        $this->controller = collect([
            '0' => 'unit', // Use a string for the controller name
            '1' => ' Unit',
        ]);
        $this->invoiceService = $invoiceService;
        $this->tableViewDataService = $tableViewDataService;
        $this->cardService = $cardService;
        $this->filterService = $filterService;
        $this->dashboardService = $dashboardService;
    }

    public function getUnitData($unitdata)
    {
        /// TABLE DATA ///////////////////////////
        $tableData = [
            'headers' => ['UNIT','TYPE', 'BEDS / BATHS', 'LEASE', 'ACTIONS'],
            'rows' => [],
        ];

        foreach ($unitdata as $item) {
            $leaseStatus = $item->lease ? '<span class="badge badge-active">Active</span>' : '<span class="badge badge-danger">No Lease</span>';
            $isDeleted = $item->deleted_at !== null;
            $tableData['rows'][] = [
                'id' => $item->id,
                $item->unit_number.' - '.$item->property->property_name.'('.$item->property->property_location.')',
                $item->unit_type,
                $item->bedrooms.'<span style="font-size:20px;"> / </span> '.$item->bathrooms,
                $leaseStatus,
                'isDeleted' => $isDeleted,
            ];
        }

        return $tableData;
    }

    public function index(Request $request, $property = null)
    {
        // Clear previousUrl if navigating to a new create method
        session()->forget('previousUrl');
        $filters = $request->except(['tab','_token','_method']);
        $filterdata = $this->filterService->getUnitFilters();
        $baseQuery = $this->model::with('property','lease')->showSoftDeleted()->ApplyFilters($filters);
        $cardData = $this->cardService->unitCard($baseQuery->get());
        $dashboardConfig = $this->unitTopDashboard($baseQuery->get());
      //  $controller = $this->controller;
      //  $tableData = $this->getUnitData($unitdata);
        $tabTitles = ['All','For Rent','For Sale'];
        $tabContents = [];
        $tabCounts = [];
        foreach ($tabTitles as $title) {
            $query = clone $baseQuery;
            switch ($title) {
                case 'For Rent':
                    $query->where('unit_type', 'rent');
                    break;
                case 'For Sale':
                    $query->where('unit_type', 'sale');
                    break;
                    // 'All' doesn't need any additional filters
            }
            $units = $query->get();
            $count = $units->count();
            $tableData = $this->tableViewDataService->getUnitData($units);
            $controller = $this->controller;
            $tabContents[] = view('admin.CRUD.table', [
                'data' => $tableData,
                'controller' => $controller,
            ])->render();
            $tabCounts[$title] = $count;
        }
        
        return View('admin.CRUD.form', compact('tabTitles', 'tabContents','tabCounts','filterdata', 'tableData', 'controller'),
        [
            'cardData' => $cardData,
            'dashboardConfig' => $dashboardConfig
        ]);
    }

    protected function unitTopDashboard($data)
    {
        return [
            'rows' => [
                [
                    'columns' => [
                        [
                            'width' => 'col-md-9',
                            'component' => 'admin.Dashboard.widgets.card',
                            'data' => [
                                'cardData' => $this->dashboardService->unitCard($data),
                                'title' => 'Overview'
                            ]
                        ],
                        [
                            'width' => 'col-md-3',
                            'component' => 'admin.Dashboard.charts.circleProgressChart',
                            'data' => [
                                'percentage' =>  $this->dashboardService->unitOccupancyRate($data),
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
        if (!session()->has('previousUrl')) {
            session()->put('previousUrl', url()->previous());
        }
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
        if (Unit::where('unit_number', $request->unit_number)
                 ->where('property_id', $request->property_id)
                ->exists())  {
            return redirect()->back()->withInput()->with('statuserror', 'Unit Number Already in system.');
        } 
        $validationRules = Unit::$validation;
        $validatedData = $request->validate($validationRules);
        $unit = new Unit;
        $unit->fill($validatedData);
        $unit->save();

        // Attach the currently authenticated user to the unit_user pivot table
        $user = Auth::user();
        $unitId = Unit::find($unit->id);
        $propertyId = $unit->property_id;
        if ($user->id !== 1) {
            /// Event to attach the user who created the unit
            //    event(new AssignUserToUnit($user, $unitId, $propertyId));
            $unit->users()->attach($user, ['property_id' => $propertyId]);
        }

        $redirectUrl = session()->pull('previousUrl', $this->controller['0']);
         
        return redirect($redirectUrl)->with('status', $this->controller['1'] . ' Added Successfully');
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function show(Unit $unit)
    {
        // Clear previousUrl if navigating to a new create method
        session()->forget('previousUrl');
     //   $unit->load('property', 'unitSupervisors');
        $pageheadings = collect([
            '0' => $unit->unit_number,
            '1' => $unit->property->property_name,
            '2' => $unit->property->property_streetname,
        ]);
      
        $tabTitles = collect([
            'Summary',
            'Listing',
            'Users',
            'Charges',
            'Invoices',
            'Payments',
            'Meter Readings',
            'Tickets',
            'Deposits',
            'Expenses',
            'Files'
            //    
            //    'Financials',
            //    'Users',
            //    'Invoices',
            //    'Payments'
            // Add more tab titles as needed
        ]);

        $unitEditData = $this->edit($unit)->getData();
        $tableData = [
            'headers' => ['USER', 'PROPERTY', 'ROLE'],
            'rows' => [],
        ];
        //// shows users attached to the house
        $users = $unit->users;
        $property = $unit->property;
        foreach ($users as $user) {
            $role = $user->roles->first();
            $tableData['rows'][] = [
                //   'id' => $item->id,
                $user->firstname . ' ' .  $user->lastname,
                $property->property_name,
                $role->name,
            ];
        }
        
        $viewData = $this->formData($this->model, $unit);
        $unitdetails = $unit->unitdetails;

        ///Data for utilities page
        $charges = $unit->unitcharges()->whereNull('parent_id')->get(); 
        $unitChargeTableData = $this->tableViewDataService->getUnitChargeData($charges);

        /// DATA FOR INVOICES TAB
        $invoices = $unit->invoices;
        $invoiceTableData = $this->tableViewDataService->getInvoiceData($invoices);

          /// DATA FOR Deposit TAB
          $deposit = $unit->deposits;
          // dd($payments);
           $depositTableData = $this->tableViewDataService->getDepositData($deposit);

         /// DATA FOR PAYMENTS TAB
         $payments = $unit->payments;
        // dd($payments);
         $paymentTableData = $this->tableViewDataService->getPaymentData($payments);


        $meterReadings = $unit->meterReadings;
        $MeterReadingsTableData = $this->tableViewDataService->getMeterReadingsData($meterReadings);
        $id = $unit;

         /// DATA FOR EXPENSES TAB
         $expenses = $unit->expenses;
        // dd($payments);
         $expenseTableData = $this->tableViewDataService->getExpenseData($expenses);

          /// DATA FOR TICKETS TAB
          $tickets = $unit->tickets;
          // dd($payments);
           $ticketTableData = $this->tableViewDataService->getTicketData($tickets);

        ///DATA FOR FILES //////
        $collections = ['picture', 'pdf', 'collection3'];
        $files = $unit->getMedia('Lease-Agreement');
     //   dd($files);
        $mediaTableData = $this->tableViewDataService->getMediaData($files);
        $id = $unit;
        $model = 'units';

         $listing = $unit->unitdetails;
         $listingTableData = $this->tableViewDataService->generateListingTabContents($listing);
         

        
        // Render the Blade views for each tab content
        $tabContents = [];
        foreach ($tabTitles as $title) {
            if ($title === 'Summary') {
                $tabContents[] = View('admin.Property.unit_summary', $unitEditData, compact('unit', 'unitdetails'))->render();
            }elseif ($title === 'Listing') {
                if ($listing->isNotEmpty()) {
                $tabContents[] = View('admin.CRUD.tabs_vertical', 
                ['tabTitles' => $listingTableData['tabTitles'], 
                'tabContents' => $listingTableData['tabContents'],
                'controller' => ['unit']], 
                compact('listing'))->render();
                }else{
                    $tabContents[] = View('admin.CRUD.no_data_image', [
                        'message' => 'No listing data available.',
                        'imagePath' => asset('uploads/vectors/unit.png')
                    ])->render();
                }
            }elseif ($title === 'Users') {
                $tabContents[] = View('admin.Property.unit_users', ['data' => $tableData], compact('unit'))->render();
            }elseif ($title === 'Charges') {
                $tabContents[] = view('admin.CRUD.index_show',['tableData' => $unitChargeTableData,'controller' => ['unitcharge']], compact('id','model'));
            }elseif ($title === 'Invoices') {
                $tabContents[] = view('admin.CRUD.table', ['data' => $invoiceTableData, 'controller' => ['invoice']])->render();
            }elseif ($title === 'Deposits') {
                $tabContents[] = View('admin.CRUD.index_show', ['tableData' => $depositTableData, 'controller' => ['deposit']], compact('id','model'))->render();
            }elseif ($title === 'Payments') {
                $tabContents[] = View('admin.CRUD.table', ['data' => $paymentTableData, 'controller' => ['payment']])->render();
            }elseif ($title === 'Meter Readings') {
                $tabContents[] = View('admin.CRUD.index_show', ['tableData' => $MeterReadingsTableData,'controller' => ['meter-reading']], compact('id','model'))->render();
            }elseif ($title === 'Expenses') {
                $tabContents[] = View('admin.CRUD.index_show', ['tableData' => $expenseTableData, 'controller' => ['expense']], compact('id','model'))->render();
            }elseif ($title === 'Tickets') {
                $tabContents[] = View('admin.CRUD.index_show', ['tableData' => $ticketTableData, 'controller' => ['ticket']], compact('id','model'))->render();
            }elseif ($title === 'Files') {
                $tabContents[] = View('admin.CRUD.index_show', ['tableData' => $mediaTableData,'controller' => ['']], compact('id'))->render();
            }
        }

        return View('admin.CRUD.show', compact('pageheadings', 'tabTitles', 'tabContents'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function edit(Unit $unit)
    {
        $unit->load('property');
        $specialvalue = collect([
            'property_id' => $unit->property->property_name, // Use a string for the controller name
            '1' => ' Unit',
        ]);
        $viewData = $this->formData($this->model, $unit, $specialvalue);
        $unitEditData = compact('specialvalue');

        if (!session()->has('previousUrl')) {
            session()->put('previousUrl', url()->previous());
        }


        return View('admin.CRUD.form', $viewData);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Unit $unit)
    {
        $unit->update($request->all());
       
        $redirectUrl = session()->pull('previousUrl', $this->controller['0']);
        return redirect($redirectUrl)->with('status', $this->controller['1'] . ' Edited Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function destroy(Unit $unit)
    {
        // Retrieve the Model
    
        // Get all relationships defined on the model
        $relationships = ['lease','unitdetails','payments','invoices','unitcharges','meterReadings','deposits','meterReadings'];
        $blockingRelationships = [];
    
        foreach ($relationships as $relationship) {
            if ($unit->$relationship()->exists()) {
                $blockingRelationships[] = $relationship;
            }
        }
    
        if (!empty($blockingRelationships)) {
            $blockingRelationshipsString = implode(', ', $blockingRelationships);
            return back()->with('statuserror', 'Cannot delete ' . $this->controller['1'] . ' because the following related records exist:<strong>' . $blockingRelationshipsString . '</strong>.');
        }
    
        //Check if unit is first attached to leases, invoices,payments,  
        $unit->users()->detach();   /////// Remove assigned units
        $unit->delete();   //// Delete User          //////// Remove Role


        return redirect()->back()->with('status', 'Unit deleted successfully.');
    }
}
