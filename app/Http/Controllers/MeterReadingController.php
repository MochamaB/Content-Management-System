<?php

namespace App\Http\Controllers;

use App\Models\MeterReading;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Traits\FormDataTrait;
use App\Models\Unit;
use App\Models\Property;
use App\Models\Unitcharge;
use App\Services\TableViewDataService;
use App\Services\FilterService;
use Carbon\Carbon;



class MeterReadingController extends Controller
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
    private $filterService;

    public function __construct(
        TableViewDataService $tableViewDataService,
        FilterService $filterService
    ) {
        $this->model = MeterReading::class;

        $this->controller = collect([
            '0' => 'meter-reading', // Use a string for the controller name
            '1' => ' Reading',
        ]);

        $this->tableViewDataService = $tableViewDataService;
        $this->filterService = $filterService;
    }

    public function index(Request $request)
    {
        // Clear previousUrl if navigating to a new create method
        session()->forget('previousUrl');
        $filters = $request->except(['tab', '_token', '_method']);
        $meterReadings = MeterReading::applyFilters($filters)->get();
        $mainfilter =  $this->model::pluck('unit_id')->toArray();
        $filterdata = $this->filterService->getMeterReadingsFilters();
        $controller = $this->controller;
        $tableData = $this->tableViewDataService->getMeterReadingsData($meterReadings, true);
        return View(
            'admin.CRUD.form',
            compact('mainfilter', 'tableData', 'controller', 'filterdata'),
            //  $viewData,
            [
                //     'cardData' => $cardData,
            ]
        );
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id = null, $model = null)
    {


        if ($model === 'properties') {
            $property = Property::find($id);
            $unit = $property->units;
            $charges = Unitcharge::where('property_id', $property->id)
                ->where('charge_type', 'units')
                ->get();

            $unitcharge = Unitcharge::where('property_id', $property->id)
                ->where('charge_type', 'units')
                ->get()
                ->groupBy('charge_name');
            $meterReading = MeterReading::where('property_id', $property->id)->get();
        } elseif ($model === 'units') {
            $unit = Unit::find($id);
            $property = Property::where('id', $unit->property->id)->first();
            $charges = '';
            $unitcharge = Unitcharge::where('unit_id', $unit->id)
                ->where('charge_type', 'units')
                ->get();
            $meterReading = MeterReading::where('unit_id', $unit->id)->latest()->first();
        }

        if ($unitcharge->isEmpty()) {
            return redirect()->back()->with('statuserror', ' Cannot Add Meter reading. No Charge of type units is not attached to this unit.');
        }

        //   dd($latestReading);

      ///SESSION /////
        if (!session()->has('previousUrl')) {
            session()->put('previousUrl', url()->previous());
        }

        return View('admin.Property.create_meterreading', compact('id', 'model', 'property', 'unit', 'unitcharge', 'meterReading', 'charges'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->model === 'properties') {
            return $this->storeProperty($request);
        } elseif ($request->model === 'units') {
            return $this->storeUnit($request);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MeterReading  $meterReading
     * @return \Illuminate\Http\Response
     */
    protected function storeProperty(Request $request)
    {
        $currentreadings = $request->input('currentreading', []);
        $lastreading = $request->input('lastreading', []);
        $endDates = $request->input('enddate', []);
        $startDates = $request->input('startdate', []);
        foreach ($currentreadings as $key => $reading) {
            if ($reading <= $lastreading[$key]) {
                return redirect()->back()->withInput()->with('statuserror', 'Current Reading must be greater than the Previous Reading.');
            }

            if (strtotime($endDates[$key]) <= strtotime($startDates[$key])) {
                return redirect()->back()->withInput()->with('statuserror', 'End date of Reading period must be greater than the Date of last reading.');
            }
        }

        $loggeduser = Auth::user();
        $meterReadings = [];
        if (!empty($request->input('unitcharge_id'))) {
            foreach ($request->input('unitcharge_id') as $index => $reading) {
                $rateatreading = $request->input("rate_at_reading.{$index}");
                $currentReading = $request->input("currentreading.{$index}");
                $lastReading = $request->input("lastreading.{$index}");
                $readingDifference = $currentReading - $lastReading;
                $amount = $readingDifference * $rateatreading;

                $meterReading = [
                    'property_id' => $request->property_id,
                    'unit_id' => $request->input("unit_id.{$index}"),
                    'unitcharge_id' => $request->input("unitcharge_id.{$index}"),
                    'lastreading' => $request->input("lastreading.{$index}"),
                    'currentreading' => $request->input("currentreading.{$index}"),
                    'rate_at_reading' => $rateatreading,
                    'amount' => $amount,
                    'startdate' => $request->input("startdate.{$index}"),
                    'enddate' => $request->input("enddate.{$index}"),
                    'recorded_by' => $loggeduser->email,
                    'created_at' => now(),
                    'updated_at' => now(),
                    // ... Other fields ...
                ];
                $meterReadings[] = $meterReading;
            }
        }
        MeterReading::insert($meterReadings);
        $redirectUrl = session()->pull('previousUrl', $this->controller['0']);
        return redirect($redirectUrl)->with('status', $this->controller['1'] . ' Added Successfully');
    }

    protected function storeUnit(Request $request)
    {
        if ($request->currentreading <= $request->lastreading) {
            return redirect()->back()->withInput()->with('statuserror', 'Current Reading must be greater than the Previous Reading.');
        }
        if (strtotime($request->enddate) <= strtotime($request->startdate)) {
            return redirect()->back()->withInput()->with('statuserror', 'End date of Reading period must be greater than the Date of last reading.');
        }

        $loggeduser = Auth::user();
        $rateatreading = Unitcharge::where('id', $request->unitcharge_id)->first();

        $validationRules = MeterReading::$validation;
        $validatedData = $request->validate($validationRules);
        $meterReading = new MeterReading;
        $meterReading->fill($validatedData);
        $meterReading->rate_at_reading = $rateatreading->rate;
        $meterReading->amount = ($request->currentreading - $request->lastreading) * $rateatreading->rate;
        //   dd($meterReading->amountdue);
        $meterReading->recorded_by = $loggeduser->email;
        $meterReading->save();

        $redirectUrl = session()->pull('previousUrl', $this->controller['0']);
        return redirect($redirectUrl)->with('status', $this->controller['1'] . ' Added Successfully');
    }
    public function show(MeterReading $meterReading)
    {
        ///// Used to Set Property TYpe name in the edit view.///
        $specialvalue = collect([
            'property_id' => $meterReading->property->property_name, // Use a string for the controller name
            'unit_id' => $meterReading->unit->unit_number,
            'unitcharge_id' => $meterReading->unitcharge->charge_name,
        ]);
        $viewData = $this->formData($this->model, $meterReading,$specialvalue);
        return View('admin.CRUD.details',$viewData);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\MeterReading  $meterReading
     * @return \Illuminate\Http\Response
     */
    public function edit(MeterReading $meterReading)
    {
        $pageheadings = collect([
            '0' => $meterReading->unitcharge->charge_name,
            '1' => $meterReading->unit->unit_number,
            '2' => $meterReading->property->property_name,
        ]);

        if (!session()->has('previousUrl')) {
            session()->put('previousUrl', url()->previous());
        }

        return View('admin.Property.edit_meterreading', compact('meterReading','pageheadings'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MeterReading  $meterReading
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MeterReading $meterReading)
    {
         // Validate the request data if necessary
        $validatedData = $request->validate([
            'currentreading' => 'required|numeric',
            'lastreading' => 'required|numeric',
            // other validation rules if needed
        ]);

       // Calculate the reading difference and new amount
        $reading = $request->input('currentreading') - $request->input('lastreading');
        $newamount = $reading * $meterReading->rate_at_reading;
        $meterReading->currentreading = $request->input('currentreading');
        $meterReading->amount = $newamount;
        $meterReading->save();
       
        $redirectUrl = session()->pull('previousUrl', $this->controller['0']);
        return redirect($redirectUrl)->with('status', $this->controller['1'] . ' Edited Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MeterReading  $meterReading
     * @return \Illuminate\Http\Response
     */
    public function destroy(MeterReading $meterReading)
    {
        //
    }

    public function fetchmeterReading(Request $request)
    {

        $data = MeterReading::where('unit_id', $request->unit_id)
            ->where('unitcharge_id', $request->unitcharge_id)
            ->latest('created_at') // Get the latest records based on created_at timestamp
            ->first();
        if ($data) {
            // Convert the result to an array
            $data = $data->toArray();
            return response()->json($data);
        } else {
            // Handle the case when no data is found
            return response()->json(['message' => 'No data found']);
        }
    }
    public function fetchpropertyMeterReading(Request $request)
    {
        $unitchargeIds =  $request->unitcharge_id;
        $data = MeterReading::where('property_id', $request->property_id)
            ->where('unitcharge_id', $unitchargeIds)
            ->with('unit')
            ->latest('created_at') // Get the latest records based on created_at timestamp
            ->get();
        if ($data) {
            // Convert the result to an array
            $data = $data->toArray();
            return response()->json($data);
        } else {
            // Handle the case when no data is found
            return response()->json(['message' => 'No data found']);
        }
    }

    public function fetchAllUnits(Request $request)
    {

        $data = Unit::where('property_id', $request->property_id)
            ->pluck('unit_number', 'id')->toArray();


        return response()->json($data);
    }
}
