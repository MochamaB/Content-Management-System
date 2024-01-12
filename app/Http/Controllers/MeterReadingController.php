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

    public function __construct(TableViewDataService $tableViewDataService)
    {
        $this->model = MeterReading::class;

        $this->controller = collect([
            '0' => 'meter-reading', // Use a string for the controller name
            '1' => ' Reading',
        ]);

        $this->tableViewDataService = $tableViewDataService;
    }

    public function index(Request $request)
    {
        $meterReadingQuery = MeterReading::query();
        $meterReadings = [];
        $mainfilter =  $this->model::pluck('unit_id')->toArray();

        $month = $request->get('month', Carbon::now()->month);
        $year = $request->get('year', Carbon::now()->year);

        $this->tableViewDataService->applyDateRangeFilter($meterReadingQuery,$month,$year);
        $meterReadings = $meterReadingQuery->get();
       
        $controller = $this->controller;
        $tableData = $this->tableViewDataService->getMeterReadingsData($meterReadings, true);
     return View(
        'admin.CRUD.form',
        compact('mainfilter', 'tableData', 'controller'),
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
    public function create($id = null)
    {

    if($id){
        $unit = Unit::find($id);
        $property = Property::where('id', $unit->property->id)->first();
        $unitcharge = Unitcharge::where('unit_id', $unit->id)
            ->where('charge_type', 'units')
            ->get();
            if ($unitcharge->isEmpty()) {
                return redirect()->back()->with('statuserror', ' Cannot Add Meter reading. No Charge of type units is not attached to this unit.');
            }
        if ($unit) {
        $meterReading = MeterReading::where('unit_id',$unit->id)->latest()->first();
        }
    }else{
        $id = null;
        $property = Property::pluck('property_name', 'id')->toArray();
        $unit = null;
        $unitcharge = null;
        $meterReading = null;
    }
   

        //   dd($latestReading);

        Session::flash('previousUrl', request()->server('HTTP_REFERER'));

        return View('admin.property.create_meterreading', compact('id','property', 'unit', 'unitcharge','meterReading'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
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

        $previousUrl = Session::get('previousUrl');
        return redirect($previousUrl)->with('status', 'Meter Reading Entered Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MeterReading  $meterReading
     * @return \Illuminate\Http\Response
     */
    public function show(MeterReading $meterReading)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\MeterReading  $meterReading
     * @return \Illuminate\Http\Response
     */
    public function edit(MeterReading $meterReading)
    {
        //
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
        //
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
}
