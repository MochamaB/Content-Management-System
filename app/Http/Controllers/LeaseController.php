<?php

namespace App\Http\Controllers;

use App\Models\Chartofaccounts;
use App\Models\lease;
use App\Models\Property;
use App\Models\Unit;
use App\Models\User;
use App\Models\Unitcharge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Traits\FormDataTrait;
use App\Http\Requests\StoreUnitChargeRequest;
use App\Models\Utilities;

class LeaseController extends Controller
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
        $this->model = Lease::class;

        $this->controller = collect([
            '0' => 'lease', // Use a string for the controller name
            '1' => 'New Lease',
        ]);
    }
    public function index()
    {
        $user = Auth::user();
        if (Gate::allows('view-all', $user)) {
            $tablevalues = lease::with('property','unit','user')->get();
        } else {
            $tablevalues =$user->supervisedUnits;
        }

        $mainfilter =  $this->model::pluck('status')->toArray();
        $viewData = $this->formData($this->model);
        $controller = $this->controller;
        /// TABLE DATA ///////////////////////////
        $tableData = [
            'headers' => ['LEASE', 'TYPE', 'STATUS', 'ACTIONS'],
            'rows' => [],
        ];

        foreach ($tablevalues as  $item) {
            $tableData['rows'][] = [
                'id' => $item->id,
                $item,
             //   $item->property->property_name.' - '.$item->unit->unit_number.' * '.$item->user->firstname.' '.$item->user->lastname,
                $item->lease_period,
                $item->status,

            ];
        }

        return View(
            'admin.CRUD.form',
            compact('mainfilter', 'tableData', 'controller'),
            $viewData,
            ['controller' => $this->controller]
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

        if (Gate::allows('view-all', auth()->user())) {
            $properties = Property::pluck('property_name', 'id')->toArray();
        } else {
            $properties = auth()->user()->supervisedUnits->pluck('property.property_name', 'property.id')->toArray();
        }

        $role = 'tenant'; // Replace with the desired role
        $tenants = User::withoutActiveLease($role)->get(); ///tenantdetailsview
        $lease = $request->session()->get('lease');
        $tenantdetails = $request->session()->get('tenantdetails');
        $rentcharge = $request->session()->get('rentcharge');
        $splitRentcharges = $request->session()->get('splitRentcharges');
        $depositcharge = $request->session()->get('depositcharge');
        $account = Chartofaccounts::all();
        $accounts = $account->groupBy('account_type');
        $utilities = Utilities::where('property_id', $lease->property_id ?? '')->get();
        $utilityCharges = $request->session()->get('utilityCharges');

        //  dd($splitcharges);

        //   $viewData = $this->formData($this->model);

        $tabTitles = collect([
            '0' => 'Lease Details',
            '1' => 'Tenant Cosigners',
            '2' => 'Rent',
            '3' => 'Security Deposit',
            '4' => 'Utilities',
            '5' => 'Lease Agreement',
        ]);
        $activetab = $request->query('active_tab', '0');
        $tabContents = [];
        foreach ($tabTitles as $title) {
            if ($title === 'Lease Details') {
                $tabContents[] = View('admin.lease.leasedetails', compact('properties', 'tenants', 'lease'))->render();
            } elseif ($title === 'Tenant Cosigners') {
                $tabContents[] = View('admin.lease.tenantdetails', compact('lease', 'tenantdetails'))->render();
            } elseif ($title === 'Rent') {
                $tabContents[] = View('admin.lease.rent', compact('accounts', 'lease', 'rentcharge', 'splitRentcharges'))->render();
            } elseif ($title === 'Security Deposit') {
                $tabContents[] = View('admin.lease.deposit', compact('accounts', 'lease', 'depositcharge'))->render();
            } elseif ($title === 'Utilities') {
                $tabContents[] = View('admin.lease.utilities', compact('lease', 'rentcharge','utilities', 'utilityCharges'))->render();
            } elseif ($title === 'Lease Agreement') {
                $tabContents[] = View('admin.lease.leaseagreement')->render();
            }
        }

        return View('admin.lease.lease', compact('tabTitles', 'tabContents', 'activetab'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $validatedData = $request->validate([
            'property_id' => 'required',
            'unit_id' => 'required|numeric',
            'user_id' => 'required',
            'lease_period' => 'required',
            'status' => 'required',
            'startdate' => 'required|date',
            'enddate' => 'nullable|date',
        ]);

        if (empty($request->session()->get('lease'))) {
            $lease = new Lease();
            $lease->fill($validatedData);
            $request->session()->put('lease', $lease);
            $lease->save();
        } else {
            $lease = $request->session()->get('lease');
            $lease->fill($validatedData);
            $request->session()->put('lease', $lease);
            $lease->update();
        }

        // Redirect to the lease.create route with a success message

        return redirect()->route('lease.create', ['active_tab' => '1'])
            ->with('status', 'Lease Created Successfully. Enter Tenant Details');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\lease  $lease
     * @return \Illuminate\Http\Response
     */
    public function show(lease $lease)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\lease  $lease
     * @return \Illuminate\Http\Response
     */
    public function edit(lease $lease)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\lease  $lease
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, lease $lease)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\lease  $lease
     * @return \Illuminate\Http\Response
     */
    public function destroy(lease $lease)
    {
        //
    }

    //////// Get Units when property is selected//////
    public function fetchunits(Request $request)
    {
        if (Gate::allows('view-all', auth()->user())) {
            $data = Unit::where('property_id', $request->property_id)
                ->doesntHave('lease')
                ->pluck('unit_number', 'id')->toArray();
        } else {
            $data = auth()->user()->supervisedUnits
                ->where('property_id', $request->property_id)
                ->has('lease')
                ->pluck('unit_number', 'id')->toArray();
        }

        return response()->json($data);
    }

    /////////// lease wizard
    public function rent(StoreUnitChargeRequest $request)
    {
        $validatedData = $request->validated();
        if (empty($request->session()->get('rentcharge'))) {
            $rentcharge = new Unitcharge();
            $rentcharge->fill($validatedData);
            $request->session()->put('rentcharge', $rentcharge);
            $rentcharge->save();
        } else {
            $rentcharge = $request->session()->get('rentcharge');
            $rentcharge->fill($validatedData);
            $request->session()->put('rentcharge', $rentcharge);
            $rentcharge->update();
        }

        $splitRentCharges = [];
    
        if (!empty($request->input('splitcharge_name'))) {
                foreach ($request->input('splitcharge_name') as $index => $chargeName) {
                    $splitRentCharge = [
                        'property_id' => $rentcharge->property_id,
                        'unit_id' => $rentcharge->unit_id,
                        'chartofaccounts_id' => $request->input('splitchartofaccounts_id'),
                        'charge_name' => $chargeName,
                        'charge_cycle' => $rentcharge->charge_cycle,
                        'charge_type' => $request->input('splitcharge_type'),
                        'rate' => $request->input('splitrate'),
                        'parent_utility' => $rentcharge->id,
                        'recurring_charge' => $rentcharge->recurring_charge,
                        'startdate' => $rentcharge->startdate,
                        'nextdate' => $rentcharge->nextdate,
                        // ... Other fields ...
                    ];
                    $splitRentCharges[] = $splitRentCharge;
                }
        }
        if (empty($request->session()->get('splitRentcharges'))) {
            $request->session()->put('splitRentcharges', $splitRentCharges);
        }else{
            $splitRentcharges = $request->session()->get('splitRentcharges');
            $request->session()->put('splitRentcharges', $splitRentcharges);
        }
           
        

        //    Unitcharge::insert($splitCharges);
        //    $request->session()->put('splitRentcharges', $splitRentCharges);
        return redirect()->route('lease.create', ['active_tab' => '3'])
            ->with('status', 'Rent Assigned Successfully. Enter Security Deposit Details');
    }
    public function deposit(StoreUnitChargeRequest $request)
    {
        $validatedData = $request->validated();
        if (empty($request->session()->get('depositcharge'))) {
            $depositcharge = new Unitcharge();
            $depositcharge->fill($validatedData);
            $request->session()->put('depositcharge', $depositcharge);
            //   $charge->save();
        } else {
            $depositcharge = $request->session()->get('depositcharge');
            $depositcharge->fill($validatedData);
            $request->session()->put('depositcharge', $depositcharge);
            //   $charge->update();
        }
        return redirect()->route('lease.create', ['active_tab' => '4'])
            ->with('status', 'Deposit Assigned Successfully. Enter Utility Details');
    }

    public function skiprent()
    {
        return redirect()->route('lease.create', ['active_tab' => '4'])
            ->with('status', 'Rent details skipped. Add Utilities');
    }

    public function assignUtilities(Request $request)
    {

        $utilityCharges = [];
    if (!empty($request->input('charge_name'))) {
        foreach ($request->input('charge_name') as $index => $chargeName) {
            $utilitycharge =[
                'property_id' => $request->input('property_id'),
                'unit_id' => $request->input('unit_id'),
                'chartofaccounts_id' => $request->input('chartofaccounts_id'),
                'charge_name' => $chargeName,
                'charge_cycle' => $request->input('charge_cycle'),
                'charge_type' => $request->input('charge_type'),
                'rate' => $request->input('rate'),
                'parent_utility' => $request->input('parent_utility'),
                'recurring_charge' => $request->input('recurring_charge'),
                'startdate' => $request->input('startdate'),
                'nextdate' => $request->input('nextdate'),
                // ... Other fields ...
            ];


            $utilityCharges[] = $utilitycharge;
        }
    }
        $request->session()->put('utilityCharges', $utilityCharges);

        return redirect()->route('lease.create', ['active_tab' => '5'])
            ->with('status', 'Utilities Assigned Successfully. Accept Terms and Conditions');
    }
    public function saveLease(Request $request)
    {

        $tenantdetails = $request->session()->get('tenantdetails');
        $tenantdetails->save();

        $splitRentcharges = $request->session()->get('splitRentcharges');
        if(!empty($splitRentcharges)){
        Unitcharge::insert($splitRentcharges);
        }
       
        $depositcharge = $request->session()->get('depositcharge');
        if(!empty($depositcharge)){
        $depositcharge->save();
        }

        $utilitycharges = $request->session()->get('utilityCharges');
        if(!empty($utilitycharges)){
        Unitcharge::insert($utilitycharges);
        }


        $request->session()->forget('lease');
        $request->session()->forget('tenantdetails');
        $request->session()->forget('rentcharge');
        $request->session()->forget('splitRentcharges');
        $request->session()->forget('depositcharge');
        $request->session()->forget('utilityCharges');

        return redirect()->route('lease.index');
    }
}
