<?php

namespace App\Http\Controllers;

use App\Models\Amenity;
use App\Models\Property;
use App\Models\Unit;
use App\Models\UnitDetail;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\TableViewDataService;
use App\Services\FilterService;
use App\Services\CardService;
use App\Actions\UploadMediaAction;


class ListingController extends Controller
{
    protected $controller;
    protected $model;
    private $cardService;
    private $tableViewDataService;
    private $filterService;
    protected $uploadMediaAction;

    public function __construct(
        CardService $cardService,
        TableViewDataService $tableViewDataService,
        FilterService $filterService,
        UploadMediaAction $uploadMediaAction,
    ) {
        $this->model = UnitDetail::class;
        $this->controller = collect([
            '0' => 'listing', // Use a string for the controller name
            '1' => ' Listings',
        ]);
        $this->cardService = $cardService;
        $this->tableViewDataService = $tableViewDataService;
        $this->filterService = $filterService;
        $this->uploadMediaAction = $uploadMediaAction;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Clear previousUrl if navigating to a new create method
        session()->forget('previousUrl');
        $filters = $request->except(['tab', '_token', '_method']);
        $filterdata = $this->filterService->getExpenseFilters($request);
        $baseQuery = Unit::with('unitdetails')->ApplyDateFilters($filters);
        $cardData = $this->cardService->expenseCard($baseQuery->get());
        // Variable to track the applied scope
        $tabTitles = ['Units Listed', 'Units Not Listed'];

        $tabContents = [];
        $tabCounts = [];
        foreach ($tabTitles as $title) {
            $query = clone $baseQuery;
            switch ($title) {
                case 'Units Listed':
                    $query->has('unitdetails');
                    break;
                case 'Units Not Listed':
                    $query->doesntHave('unitdetails');
                    break;
                    // 'All' doesn't need any additional filters
            }
            $listings = $query->get();
            $count = $listings->count();
            $tableData = $this->tableViewDataService->getUnitListingData($listings, true);
            $controller = $this->controller;
            $tabContents[] = view('admin.CRUD.table', [
                'data' => $tableData,
                'controller' => $controller,
            ])->render();
            $tabCounts[$title] = $count;
        }

        


        return View('admin.CRUD.form', compact('tabTitles', 'tabContents', 'tabCounts', 'filterdata', 'controller', 'cardData', 'filters',));
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $id = null)
    {
        //1. UNIT DETAIL Initialize variables
        //  $property = [];
        //  $unit = [];

        // Check if $id is provided
        if ($id !== null) {
            // Find the selected unit and its property
            $unit = Unit::findOrFail($id);
            $selectedProperty = Property::findOrFail($unit->property_id); // The pre-selected property
            $properties = Property::all(); // All properties for the dropdown
        } else {
            $unit = '';
            $selectedProperty = null; // No pre-selected property
            $properties = Property::all(); // All properties for the dropdown
        }
        // dd($unit);
        //2. Amenities
        $wizardData = $request->session()->get('wizard_unitdetails');
        $propertyId = $wizardData['property_id'] ?? '';
        // Fetch amenities associated with the property using pivot table
        $amenities = Amenity::whereHas('properties', function ($query) use ($propertyId) {
            $query->where('property_id', $propertyId);
        })->get();

        //3. Listing Info
        $users = User::with('units', 'roles')->visibleToUser()->excludeTenants()->get();

        $steps = collect([
            'Unit Details',
            'Amenities',
            'Listing Info',
            'Photos',
        ]);
        $activetab = $request->query('active_tab', '0');
        $stepContents = [];
        foreach ($steps as $title) {
            if ($title === 'Unit Details') {
                $stepContents[] = View('wizard.listing.unit_details', compact('properties', 'selectedProperty', 'unit'))->render();
            } elseif ($title === 'Amenities') {
                $stepContents[] = View('wizard.listing.unit_amenities', compact('amenities'))->render();
            } elseif ($title === 'Listing Info') {
                $stepContents[] = View('wizard.listing.unit_listinginfo', compact('users'))->render();
            } elseif ($title === 'Photos') {
                $stepContents[] = View('wizard.listing.unit_photos', compact('properties'))->render();
            }
        }

        return View('wizard.moveout.moveout', compact('steps', 'stepContents', 'activetab'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Step 1: Validate file uploads
        $request->validate([
            'photos.*' => 'file|mimes:jpg,jpeg,png|max:2048', // Each photo max 2MB
        ]);

        // Step 2: Retrieve wizard session data
        $wizardData = $request->session()->get('wizard_unitdetails');

        if (!$wizardData) {
            return redirect()->back()->with('error', 'No wizard data found in session.');
        }

        // Step 3: Create a new UnitDetails record
        $unitDetails = new UnitDetail();
        $unitDetails->unit_id = $wizardData['unit_id'];
        $unitDetails->user_id = $wizardData['user_id']; // Assuming authenticated user
        $unitDetails->title = $wizardData['title'] ?? null;
        $unitDetails->description = $wizardData['description'] ?? null;
        $unitDetails->size = $wizardData['size'] ?? null;
        $unitDetails->slug = null; // Generate a slug
        $unitDetails->amenities = $wizardData['amenities'] ?? null;
        $unitDetails->additional_features = $wizardData['additional_features'] ?? null;
        $unitDetails->save();

         // Step 3: Find or create the Unit model
         $unit = Unit::find($wizardData['unit_id']);
         if (!$unit) {
             return redirect()->back()->with('error', 'Unit not found.');
         }
         // Step 4: Upload and attach photos to the Unit model
        if ($request->hasFile('photos')) {
            $photos = $request->file('photos',[]);
            foreach ($photos as $photo) {
                $unit->addMedia($photo)->toMediaCollection('unit-photo'); // Save to 'unit-photos'
            }
        }
         // Step 6: Clear session wizard data
         $request->session()->forget('wizard_unitdetails');

         // Step 7: Redirect with success message
         return redirect()->route('listing.index')->with('status', 'Unit details and photos saved successfully!');
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
        $unitDetail = UnitDetail::findOrFail($id);
         // Check if 'amenities' are in the request and update
    if ($request->has('amenities')) {
         // Save the amenities as JSON
        $unitDetail->amenities = json_encode($request->amenities);
    }
    if ($request->has('user_id')) {
        $unitDetail->user_id = $request->user_id;
    }
    if ($request->has('title')) {
        $unitDetail->title = $request->title;
    }
    if ($request->has('description')) {
        $unitDetail->description = $request->description;
    } if ($request->has('size')) {
        $unitDetail->size = $request->size;
    }
  //  dd($request->file('photos',[]));
     // Ensure multiple file upload works
     if ($request->hasFile('photos')) {
       
        $unit = Unit::findOrFail($unitDetail->unit_id);
        $photos = $request->file('photos',[]);
        foreach ($photos as $photo) {
            $unit->addMedia($photo)->toMediaCollection('unit-photo'); // Save to 'unit-photos'
        }

     }
        $unitDetail->save();

    return redirect()->back()->with('status', 'Amenities updated successfully.');

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
    public function fetchListingUnits(Request $request)
    {

        $data = Unit::where('property_id', $request->property_id)
            ->where(function ($query) {
                $query->doesntHave('unitdetails');
            })
            ->pluck('unit_number', 'id')
            ->toArray(); // Use get() instead of pluck() to fetch all columns

        return response()->json($data);
    }
    public function unitdetails(Request $request)
    {
        // Step 1: Validate the incoming data
        $validatedData = $request->validate([
            'property_id' => 'required|exists:properties,id', // Property must exist
            'unit_id' => 'required|exists:units,id',         // Unit must exist
        ]);

        // Step 2: Check if session already has data; if not, initialize it
        if (empty($request->session()->get('wizard_unitdetails'))) {
            // Initialize a new array for wizard_unitdetails
            $wizardData = [
                'property_id' => $validatedData['property_id'],
                'unit_id' => $validatedData['unit_id'],
            ];
            $request->session()->put('wizard_unitdetails', $wizardData); // Store to session
        } else {
            // Retrieve the existing data and update it
            $wizardData = $request->session()->get('wizard_unitdetails');
            $wizardData['property_id'] = $validatedData['property_id'];
            $wizardData['unit_id'] = $validatedData['unit_id'];

            $request->session()->put('wizard_unitdetails', $wizardData); // Update session
        }

        // Step 3: Redirect to the next wizard step with a success message
        return redirect()->route('listing.create', ['active_tab' => '1'])
            ->with('status', 'Property and Unit Details saved successfully. Proceed to the next step.');
    }
    public function unitamenities(Request $request)
    {
        // Validate that amenities are passed
        $validatedData = $request->validate([
            'amenities' => 'required|array',
            'amenities.*' => 'exists:amenities,id', // Ensure all IDs are valid
        ]);

        // Retrieve existing wizard_unitdetails from the session
        $wizardData = $request->session()->get('wizard_unitdetails', []);

        // Save amenities as JSON in wizard_unitdetails
        $wizardData['amenities'] = json_encode($validatedData['amenities']);

        // Update the session
        $request->session()->put('wizard_unitdetails', $wizardData);

        // Redirect to the next wizard step
        return redirect()->route('listing.create', ['active_tab' => '2'])
            ->with('status', 'Amenities saved successfully.');
    }
    public function unitListingInfo(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'user_id' => 'required',
            'size' => 'required|string|max:255', // Validate size as string (optional)
            'title' => 'required|string|max:255', // Validate title as string (optional)
            'description' => 'required|string', // Validate description (optional)
        ]);

        // Retrieve the existing wizard_unitdetails from the session
        $wizardData = $request->session()->get('wizard_unitdetails', []);

        // Store the new fields in the session
        $wizardData['user_id'] = $validatedData['user_id'] ?? null;
        $wizardData['size'] = $validatedData['size'] ?? null;
        $wizardData['title'] = $validatedData['title'] ?? null;
        $wizardData['description'] = $validatedData['description'] ?? null;


        // Update the session with the new data
        $request->session()->put('wizard_unitdetails', $wizardData);

        // Redirect to the next wizard step
        return redirect()->route('listing.create', ['active_tab' => '3'])
            ->with('status', 'Unit Listing details saved successfully.');
    }
}
