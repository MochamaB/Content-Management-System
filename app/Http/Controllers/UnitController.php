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

    public function __construct()
    {
        $this->model = Unit::class;
        $this->controller = collect([
            '0' => 'unit', // Use a string for the controller name
            '1' => 'New Unit',
        ]);
    }

    public function getUnitData($unitdata)
    {
        /// TABLE DATA ///////////////////////////
        $tableData = [
            'headers' => ['UNIT','TYPE', 'BEDS', 'BATHS', 'LEASE', 'ACTIONS'],
            'rows' => [],
        ];

        foreach ($unitdata as $item) {
            $leaseStatus = $item->lease ? '<span class="badge badge-active">Active</span>' : '<span class="badge badge-danger">No Lease</span>';
            $tableData['rows'][] = [
                'id' => $item->id,
                $item->unit_number.' - '.$item->property->property_name.'('.$item->property->property_location.')',
                $item->unit_type,
                $item->bedrooms,
                $item->bathrooms,
                $leaseStatus,
            ];
        }

        return $tableData;
    }

    public function index($property = null)
    {
        
        $unitdata = $this->model::with('property','lease')->get();
        $mainfilter =  $this->model::pluck('unit_type')->toArray();
        $viewData = $this->formData($this->model);
        $controller = $this->controller;
        $tableData = $this->getUnitData($unitdata);
        
        return View('admin.CRUD.form', compact('mainfilter', 'tableData', 'controller'));
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
        if (Unit::where('unit_number', $request->unit_number)
                 ->where('property_id', $request->property_id)
                ->exists())  {
            return redirect()->back()->with('statuserror', 'Unit Number Already in system.');
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

            return redirect($this->controller['0'])->with('status', $this->controller['1'] . ' Added Successfully');
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function show(Unit $unit)
    {
     //   $unit->load('property', 'unitSupervisors');
        $pageheadings = collect([
            '0' => $unit->unit_number,
            '1' => $unit->property->property_name,
            '2' => $unit->property->property_streetname,
        ]);
      
        $tabTitles = collect([
            'Summary',
            'Users',
            'Charges & Utilities',
            'Meter Readings',
            //    'Maintenance',
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
        $charges = $unit->unitcharges; ///data for utilities page
        $meterReadings = $unit->meterReadings;
        $meterReaderController = new MeterReadingController();
        $MeterReadingsTableData = $meterReaderController->getMeterReadingsData($meterReadings);
        $parentmodel = $unit;
        // Render the Blade views for each tab content
        $tabContents = [];
        foreach ($tabTitles as $title) {
            if ($title === 'Summary') {
                $tabContents[] = View('admin.property.unit_' . $title, $unitEditData, compact('unit', 'unitdetails'))->render();
            }elseif ($title === 'Users') {
                $tabContents[] = View('admin.property.unit_' . $title, ['data' => $tableData], compact('unit'))->render();
            }elseif ($title === 'Charges & Utilities') {
                $tabContents[] = View('admin.property.unit_utilities',  compact('charges'))->render();
            }elseif ($title === 'Meter Readings') {
                $tabContents[] = View('admin.CRUD.show_index', ['tableData' => $MeterReadingsTableData,'controller' => 'meter-reading'], 
                compact('parentmodel'))->render();
            }
        }

        return View('admin.CRUD.form', compact('pageheadings', 'tabTitles', 'tabContents'));
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
            '1' => 'New Unit',
        ]);
        $viewData = $this->formData($this->model, $unit, $specialvalue);
        $unitEditData = compact('specialvalue');



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
       

        return redirect($this->controller['0'])->with('status', $this->controller['1'] . ' Edited Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function destroy(Unit $unit)
    {
        //Check if unit is first attached to leases, invoices,payments,  
        $unit->users()->detach();   /////// Remove assigned units
        $unit->delete();   //// Delete User          //////// Remove Role


        return redirect()->back()->with('status', 'Unit deleted successfully.');
    }
}
