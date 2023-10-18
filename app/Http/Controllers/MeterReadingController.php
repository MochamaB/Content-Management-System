<?php

namespace App\Http\Controllers;

use App\Models\MeterReading;
use Illuminate\Http\Request;
use App\Traits\FormDataTrait;

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
            '0' => 'meterreading', // Use a string for the controller name
            '1' => 'New Reading',
        ]);
    }

    public function getMeterReadingsData($meterReadings)
    {
        $tableData = [
            'headers' => ['UNIT', 'CHARGE', 'LAST READING', 'RATE AT READING', 'ACTIONS'],
            'rows' => [],
        ];

        foreach ($meterReadings as $item) {
           
            $tableData['rows'][] = [
                'id' => $item->id,
                $item,
                $item,
                $item,
                $item,
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

        return View('admin.CRUD.form', compact('mainfilter','tableData', 'controller'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
}
