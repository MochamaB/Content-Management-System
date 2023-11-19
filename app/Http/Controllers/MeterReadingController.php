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

    public function __construct()
    {
        $this->model = MeterReading::class;

        $this->controller = collect([
            '0' => 'meter-reading', // Use a string for the controller name
            '1' => 'New Reading',
        ]);
    }

    public function getMeterReadingsData($meterReadings)
    {
        $tableData = [
            'headers' => ['UNIT', 'CHARGE', 'PREVIOUS READING', 'CURRENT', 'USAGE', 'RATE','AMOUNT', 'ACTIONS'],
            'rows' => [],
        ];

        foreach ($meterReadings as $item) {
            $startFormatted = empty($item->startdate) ? 'Not set' : Carbon::parse($item->startdate)->format('Y-m-d');
            $enddateFormatted = empty($item->enddate) ? 'Not set' : Carbon::parse($item->enddate)->format('Y-m-d');
            $usage =  $item->currentreading - $item->lastreading;
            $amount = $usage *  $item->rate_at_reading;
            $tableData['rows'][] = [
                'id' => $item->id,
                $item->unit->unit_number,
                $item->unitcharge->charge_name,
                $item->lastreading . '</br></br><span class="text-muted" style="font-weight:500;font-style: italic">' .
                    $startFormatted . '</span>',
                $item->currentreading . '</br></br><span class="text-muted" style="font-weight:500;font-style: italic">' .
                    $enddateFormatted . '</span>',
                $usage,
                $item->rate_at_reading,
                $amount,
            ];
        }

        return $tableData;
    }

    public function index()
    {
        $meterReadings = MeterReading::all();
        $mainfilter =  $this->model::pluck('unit_id')->toArray();
        $controller = $this->controller;
        $tableData = $this->getMeterReadingsData($meterReadings);

        return View('admin.CRUD.form', compact('mainfilter', 'tableData', 'controller'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id = null)
    {

        $unit = Unit::find($id);
        // session(['unit' => $unit]);
        $property = Property::where('id', $unit->property->id)->first();
        $unitcharge = Unitcharge::where('unit_id', $unit->id)
            ->where('charge_type', 'units')
            ->get();
        if ($unitcharge->isEmpty()) {
            return redirect()->back()->with('statuserror', ' Cannot Add Meter reading. No Charge of type units is not attached to this unit.');
        }
        

        //   dd($latestReading);

        Session::flash('previousUrl', request()->server('HTTP_REFERER'));

        return View('admin.property.create_meterreading', compact('property', 'unit', 'unitcharge'));
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
