<?php

namespace App\Http\Controllers;

use App\Models\unitcharge;
use Illuminate\Http\Request;
use App\Traits\FormDataTrait;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\Lease;
use App\Models\Chartofaccount;
use Carbon\Carbon;

class UnitChargeController extends Controller
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
        $this->model = unitcharge::class;
        $this->controller = collect([
            '0' => 'unitcharge', // Use a string for the controller name
            '1' => 'New Unit Charge',
        ]);
    }

    public function getUnitChargeData($unitchargedata)
    {
        /// TABLE DATA ///////////////////////////
        $tableData = [
            'headers' => ['CHARGE', 'CYCLE', 'TYPE', 'RATE', 'RECURRING', 'LAST BILLED', 'NEXT BILL DATE', 'ACTIONS'],
            'rows' => [],
        ];

        foreach ($unitchargedata as $item) {
            $nextDateFormatted = empty($item->nextdate) ? 'Charged Once' : Carbon::parse($item->nextdate)->format('Y-m-d');

            $tableData['rows'][] = [
                'id' => $item->id,
                $item->charge_name,
                $item->charge_cycle,
                $item->charge_type,
                $item->rate,
                $item->recurring_charge,
                \Carbon\Carbon::parse($item->startdate)->format('d M Y'),
                $nextDateFormatted,

            ];
                // If the current charge has child charges, add them to the table
                if ($item->childrencharge->isNotEmpty()) {
                    foreach ($item->childrencharge as $child) {
                        $nextDateFormattedChild = empty($child->nextdate) ? 'Charged Once' : Carbon::parse($child->nextdate)->format('Y-m-d');

                        $tableData['rows'][] = [
                            'id' => $child->id,
                            '  <i class=" mdi mdi-subdirectory-arrow-right mdi-24px text-warning"> ' . $child->charge_name, // Indent child charges for clarity
                            $child->charge_cycle,
                            $child->charge_type,
                            $child->rate,
                            $child->recurring_charge,
                            \Carbon\Carbon::parse($child->startdate)->format('d M Y'),
                            $nextDateFormattedChild,
                        ];
                    }
                }
        }

        return $tableData;
    }

    public function index()
    {
        $unitdata = $this->model::with('property')->get();
        $mainfilter =  $this->model::pluck('charge_type')->toArray();
        $viewData = $this->formData($this->model);
        $controller = $this->controller;
        $tableData = $this->getUnitChargeData($unitdata);

        return View('admin.CRUD.form', compact('mainfilter', 'tableData', 'controller'));
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
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\unitcharge  $unitcharge
     * @return \Illuminate\Http\Response
     */
    public function show(unitcharge $unitcharge)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\unitcharge  $unitcharge
     * @return \Illuminate\Http\Response
     */
    public function edit(unitcharge $unitcharge)
    {

        // Check if the parent_id is not 0
        if ($unitcharge->parent_id !== null) {
            // If parent_id is not 0, open the parent utility and pass it as the $unitcharge
            $parentUtility = Unitcharge::find($unitcharge->parent_id);
            // Pass the parent utility as the $unitcharge
            $unitcharge = $parentUtility;
        }

      
        $pageheadings = collect([
            '0' => $unitcharge->charge_name,
            '1' => $unitcharge->unit->unit_number,
            '2' => $unitcharge->property->property_name,
        ]);



        $lease = Lease::where('unit_id', $unitcharge->unit_id);
        $rentcharge = $unitcharge;
        $splitRentcharges = Unitcharge::where('parent_id', $rentcharge->id)->get();
        $account = Chartofaccount::all();
        $accounts = $account->groupBy('account_type');

        session::flash('previousUrl', request()->server('HTTP_REFERER'));
        return View('admin.lease.charges', compact('pageheadings', 'accounts', 'lease', 'rentcharge', 'splitRentcharges', 'unitcharge'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\unitcharge  $unitcharge
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, unitcharge $unitcharge)
    {
        $unitchargedata = Unitcharge::findOrFail($unitcharge->id);
        $unitchargedata->update($request->all());

        if (!empty($request->input('splitcharge_name'))) {
            foreach ($request->input('splitcharge_name') as $index => $chargeName) {
                Unitcharge::UpdateOrCreate(
                    // Conditions to find the record (in this case, using charge_name and parent_id)
                    [
                        'property_id' => $unitcharge->property_id,
                        'unit_id' => $unitcharge->unit_id,
                        'charge_name' => $chargeName,
                        'parent_id' => $unitcharge->id,
                    ],
                    // Data to update or create
                    [
                        'property_id' => $unitcharge->property_id,
                        'unit_id' => $unitcharge->unit_id,
                        'chartofaccounts_id' => $request->input('splitchartofaccounts_id'),
                        'charge_name' => $chargeName,
                        'charge_cycle' => $unitcharge->charge_cycle,
                        'charge_type' => $request->input('splitcharge_type'),
                        'rate' => $request->input('splitrate'),
                        'parent_id' => $unitcharge->id,
                        'recurring_charge' => $unitcharge->recurring_charge,
                        'startdate' => $unitcharge->startdate,
                        'nextdate' => $unitcharge->nextdate,
                        // ... Other fields ...
                    ]
                );
            }
        }

        $previousUrl = Session::get('previousUrl');
        return redirect($previousUrl)->with('status', 'Charge Edited Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\unitcharge  $unitcharge
     * @return \Illuminate\Http\Response
     */
    public function destroy(unitcharge $unitcharge)
    {
        //
    }
}
