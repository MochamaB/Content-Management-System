<?php

namespace App\Http\Controllers;

use App\Events\AssignUserToUnit;
use App\Models\Chartofaccount;
use App\Models\lease;
use App\Models\Property;
use App\Models\Unit;
use App\Models\Tenantdetails;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\Unitcharge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Traits\FormDataTrait;
use App\Http\Requests\StoreUnitChargeRequest;
use App\Models\Utility;
use App\Notifications\LeaseAgreementNotification;
use Carbon\Carbon;
use App\Actions\UpdateNextDateAction;

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
    protected $updateNextDateAction;


    public function __construct(UpdateNextDateAction $updateNextDateAction)
    {
        $this->model = Lease::class;

        $this->controller = collect([
            '0' => 'lease', // Use a string for the controller name
            '1' => 'New Lease',
        ]);

        $this->updateNextDateAction = $updateNextDateAction;
    }
    public function index()
    {
        //  $user = Auth::user();
        $tablevalues = lease::with('property', 'unit', 'user')->get();
        $mainfilter =  $this->model::pluck('status')->toArray();
        $viewData = $this->formData($this->model);
        $controller = $this->controller;
        /// TABLE DATA ///////////////////////////
        $tableData = [
            'headers' => ['LEASE', 'TYPE', 'STATUS', 'ACTIONS'],
            'rows' => [],
        ];

        foreach ($tablevalues as  $item) {
            $endDateFormatted = empty($item->enddate) ? 'Not set' : Carbon::parse($item->enddate)->format('Y-m-d');
            // Calculate the number of days left on the lease (if end date is available)
            $daysLeft = ($item->enddate) ? Carbon::parse($item->enddate)->diffInDays(Carbon::now()) : null;
            $statusClasses = [
                'Active' => 'badge-active',
                'Draft' => 'badge-warning',
                'Expired' => 'badge-danger',
                'Terminated' => 'badge-secondary',
            ];
            // Get the CSS class for the current status, default to 'badge-secondary' if not found
            $statusClass = $statusClasses[$item->status] ?? 'badge-secondary';
            // Generate the status badge
            $statusBadge = '<span class="badge ' . $statusClass . '">' . $item->status . '</span>';
            $tableData['rows'][] = [
                'id' => $item->id,
                //  $item,
                $item->property->property_name . ' - ' . $item->unit->unit_number . ' . ' . $item->user->firstname . ' ' . $item->user->lastname,
                $item->lease_period . '     ' .  ($daysLeft !== null ? '<span class="badge badge-information" style="margin-left:20px">' . $daysLeft . ' days left</span>' : '') .
                    '</br></br><span class="text-muted" style="font-weight:500;font-style: italic">' .
                    Carbon::parse($item->startdate)->format('Y-m-d') . ' - ' . $endDateFormatted . '</span>',
                $statusBadge,

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


        $properties = Property::pluck('property_name', 'id')->toArray();
        $role = 'tenant'; // Replace with the desired role
        if (!Role::where('name', $role)->exists()) {
            return back()->with('statuserror', 'There no tenant Role in system. Create Role and Tenants First');
        }
        $tenants = User::withoutActiveLease($role)->get(); ///tenantdetailsview
        $lease = $request->session()->get('lease');
        $tenantdetails = $request->session()->get('tenantdetails');
        $rentcharge = $request->session()->get('rentcharge');
        $splitRentcharges = $request->session()->get('splitRentcharges');
        $depositcharge = $request->session()->get('depositcharge');
        $account = Chartofaccount::all();
        $accounts = $account->groupBy('account_type');
        $utilities = Utility::where('property_id', $lease->property_id ?? '')->get();
        $utilityCharges = $request->session()->get('utilityCharges');

        //  dd($splitcharges);

        //   $viewData = $this->formData($this->model);

        $steps = collect([
            'Lease Details',
            'Tenant Cosigners',
            'Rent',
            'Security Deposit',
            'Utilities',
            'Lease Agreement',
        ]);
        $activetab = $request->query('active_tab', '0');
        $stepContents = [];
        foreach ($steps as $title) {
            if ($title === 'Lease Details') {
                $stepContents[] = View('wizard.lease.leasedetails', compact('properties', 'tenants', 'lease'))->render();
            } elseif ($title === 'Tenant Cosigners') {
                $stepContents[] = View('wizard.lease.tenantdetails', compact('lease', 'tenantdetails'))->render();
            } elseif ($title === 'Rent') {
                $stepContents[] = View('wizard.lease.rent', compact('accounts', 'lease', 'rentcharge', 'splitRentcharges'))->render();
            } elseif ($title === 'Security Deposit') {
                $stepContents[] = View('wizard.lease.deposit', compact('accounts', 'lease', 'depositcharge'))->render();
            } elseif ($title === 'Utilities') {
                $stepContents[] = View('wizard.lease.utilities', compact('lease', 'rentcharge', 'utilities', 'utilityCharges'))->render();
            } elseif ($title === 'Lease Agreement') {
                $stepContents[] = View('wizard.lease.leaseagreement')->render();
            }
        }

        return View('admin.lease.lease', compact('steps', 'stepContents', 'activetab'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $validationRules = Lease::$validation;
        $validatedData = $request->validate($validationRules);

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

        $user = User::find($lease->user_id);
        // Redirect to the lease.create route with a success message
        $lease->notify(new LeaseAgreementNotification($user, $lease)); ///// Send Lease Agreement

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
        $loggeduser = Auth::user();
        $properties = Property::pluck('property_name', 'id')->toArray();
        $tenantdetails = Tenantdetails::where('user_id', $lease->user_id)->first();

        $rentcharge = Unitcharge::where('unit_id', $lease->unit_id)->where('charge_name', 'rent')->first();
        if ($rentcharge !== null) {
            $splitRentcharges = Unitcharge::where('parent_id', $rentcharge->id)->get();
        }
        $depositcharge = Unitcharge::where('unit_id', $lease->unit_id)->where('charge_name', 'security_deposit')->first();
        $account = Chartofaccount::all();
        $accounts = $account->groupBy('account_type');
        $utilities = Utility::where('property_id', $lease->property_id ?? '')->get();
        //     $utilityCharges = $request->session()->get('utilityCharges');



        $pageheadings = collect([
            '0' => $lease->property->property_name,
            '1' => $lease->unit->unit_number,
            '2' => $lease->user->firstname . ' ' . $lease->user->lastname,
        ]);
        $unit = Unit::where('id', $lease->unit_id)->first();
        $charges = $unit->unitcharges; ///data for utilities page
        $unitChargeController = new UnitChargeController();
        $unitChargeTableData = $unitChargeController->getUnitChargeData($charges);



        $meterReadings = $unit->meterReadings;
        $meterReaderController = new MeterReadingController();
        $MeterReadingsTableData = $meterReaderController->getMeterReadingsData($meterReadings);
        $parentmodel = $unit;

        $tabTitles = collect([
            'Summary',
            'Charges and Utilities',
            'Deposits and Payments',
            'Meter Readings',
            'Maintenance Tasks',
            'Files',
        ]);
        $tabContents = [];
        foreach ($tabTitles as $title) {
            if ($title === 'Summary') {
                $tabContents[] = View('wizard.lease.leasedetails', compact('properties', 'lease'))->render();
            } elseif ($title === 'Charges and Utilities') {
                $tabContents[] = View('admin.CRUD.show_index', ['tableData' => $unitChargeTableData, 'controller' => 'unitcharge'])->render();
            } elseif ($title === 'Deposits and Payments') {
                $tabContents[] = View('wizard.lease.deposit', compact('accounts', 'lease', 'depositcharge'))->render();
            } elseif ($title === 'Meter Readings') {
                $tabContents[] = View(
                    'admin.CRUD.show_index',
                    ['tableData' => $MeterReadingsTableData, 'controller' => 'meter-reading'],
                    compact('parentmodel')
                )->render();
            } elseif ($title === 'Maintenance Tasks') {
                $tabContents[] = View('wizard.lease.utilities', compact('lease', 'rentcharge', 'utilities'))->render();
            } elseif ($title === 'Files') {
                $tabContents[] = View('wizard.lease.leaseagreement')->render();
            }
        }

        return View('admin.CRUD.form', compact('pageheadings', 'tabTitles', 'tabContents', 'lease'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\lease  $lease
     * @return \Illuminate\Http\Response
     */
    public function edit(lease $lease)
    {
        $loggeduser = Auth::user();
        $properties = Property::pluck('property_name', 'id')->toArray();
        //     $utilityCharges = $request->session()->get('utilityCharges');


        $pageheadings = collect([
            '0' => $lease->property->property_name,
            '1' => $lease->unit->unit_number,
            '2' => $lease->user->firstname . ' ' . $lease->user->lastname,
        ]);


        return View('admin.lease.lease', compact('pageheadings', 'properties', 'lease'));
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
        $leasedata = Lease::find($lease);
        $tenantdetails = Tenantdetails::where('user_id', $lease->user_id)->first();

        $lease->update($request->all());
        $tenantdetails->update($request->all());

        return redirect($this->controller['0'])->with('status', $this->controller['1'] . ' Edited Successfully');
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

        $data = Unit::where('property_id', $request->property_id)
            ->doesntHave('lease')
            ->pluck('unit_number', 'id')->toArray();


        return response()->json($data);
    }

    public function checkchargename(Request $request)
    {
        $splitchargeNames = $request->input('splitcharge_name', []);
        foreach ($splitchargeNames as $chargeName) {
            $utilityNameExists = Utility::where('property_id', $request->property_id)
                ->where('utility_name', $splitchargeNames)
                ->exists();
            $chargeNameExists = Unitcharge::where('unit_id', $request->unit_id)
                ->where('charge_name', $splitchargeNames)
                ->exists();

            if ($utilityNameExists || $chargeNameExists) {
                return response()->json(['message' => 'The Charge already exists. Choose another name.'], 422);
            }
        }

        // If the charge name does not exist, return a success response
        //  return response()->json(['message' => 'Success!'], 200);
    }
    /////////// lease wizard
    public function cosigner(Request $request)
    {

        $validatedData = $request->validate([
            'user_id' => 'required',
            'user_relationship' => 'required',
            'emergency_name' => 'required',
            'emergency_number' => 'required',
            'emergency_email' => 'required|email',
        ]);

        if (empty($request->session()->get('tenantdetails'))) {
            $tenantdetails = new Tenantdetails();
            $tenantdetails->fill($validatedData);
            $request->session()->put('tenantdetails', $tenantdetails);
        } else {
            $tenantdetails = $request->session()->get('tenantdetails');
            $tenantdetails->fill($validatedData);
            $request->session()->put('tenantdetails', $tenantdetails);
        }

        // Redirect to the lease.create route with a success message

        return redirect()->route('lease.create', ['active_tab' => '2'])
            ->with('status', 'Tenant Details Created Successfully. Enter Rent Details');
    }
    public function rent(StoreUnitChargeRequest $request)
    {
        ////1. VALIDATE FIELD
        $validatedData = $request->validated();
        $rules = [
            'splitcharge_name.*' => 'required|string|max:255',
            // Add other validation rules for the remaining fields
        ];
        $request->validate($rules);

        ///2. GET VALUE OF NEXT DATE ON THE CHARGE/////////
        $chargeCycle = $request->input('charge_cycle');
        $startDate = Carbon::parse($request->input('startdate'));
        /// 2.1. Use the action to update the next date
        $nextDate = $this->updateNextDateAction->handle($chargeCycle, $startDate);

        if (empty($request->session()->get('rentcharge'))) {
            $rentcharge = new Unitcharge();
            $rentcharge->fill($validatedData);
            $rentcharge->nextdate = $nextDate;
            $request->session()->put('rentcharge', $rentcharge);
            $rentcharge->save();
        } else {
            $rentcharge = $request->session()->get('rentcharge');
            $rentcharge->fill($validatedData);
            $rentcharge->nextdate = $nextDate;
            $request->session()->put('rentcharge', $rentcharge);
            $rentcharge->update();
        }
        ///////////3. Check if charge name exists
        $splitchargeNames = $request->input('splitcharge_name', []);

        if (!empty($splitchargeNames)) {
            foreach ($splitchargeNames as $chargeName) {
                $utilityNameExists = Utility::where('property_id', $request->property_id)
                    ->where('utility_name', $chargeName)
                    ->exists();
                $chargeNameExists = Unitcharge::where('unit_id', $request->unit_id)
                    ->where('charge_name', $chargeName)
                    ->exists();

                if ($utilityNameExists || $chargeNameExists) {
                    return redirect()->back()->with('statuserror', 'Charge Name ' . $chargeName . ' already defined in system.');
                }
            }
        }

        $splitRentCharges = [];

        if (!empty($request->input('splitcharge_name'))) {
            foreach ($request->input('splitcharge_name') as $index => $chargeName) {
                $splitRentCharge = [
                    'property_id' => $rentcharge->property_id,
                    'unit_id' => $rentcharge->unit_id,
                    'chartofaccounts_id' => $request->input('splitchartofaccounts_id'),
                    'charge_name' => $chargeName,
                    'charge_cycle' => $rentcharge->charge_cycle, ///Charge cycle is same to the rent cycle
                    'charge_type' => $request->input('splitcharge_type'),
                    'rate' => $request->input('splitrate'),
                    'parent_id' => $rentcharge->id,
                    'recurring_charge' => $rentcharge->recurring_charge,
                    'startdate' => $rentcharge->startdate,
                    'nextdate' => $rentcharge->nextdate,
                    'created_at' => now(),
                    'updated_at' => now(),
                    // ... Other fields ...
                ];
                $splitRentCharges[] = $splitRentCharge;
            }
        }

        if (empty($request->session()->get('splitRentcharges'))) {
            $request->session()->put('splitRentcharges', $splitRentCharges);
        } else {
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
        $chargeCycle = $request->input('charge_cycle');
        $startDate = Carbon::parse($request->input('startdate'));
         /// 2.1. Use the action to update the next date
         $nextDate = $this->updateNextDateAction->handle($chargeCycle, $startDate);

        if (!empty($request->input('charge_name'))) {
            foreach ($request->input('charge_name') as $index => $chargeName) {
                $utilitycharge = [
                    'property_id' => $request->input('property_id'),
                    'unit_id' => $request->input('unit_id'),
                    'chartofaccounts_id' => $request->input('chartofaccounts_id'),
                    'charge_name' => $chargeName,
                    'charge_cycle' => $request->input('charge_cycle'),
                    'charge_type' => $request->input('charge_type'),
                    'rate' => $request->input('rate'),
                    'parent_id' => $request->input('parent_id'),
                    'recurring_charge' => $request->input('recurring_charge'),
                    'startdate' => $request->input('startdate'),
                    'nextdate' => $nextDate,
                    'created_at' => now(),
                    'updated_at' => now(),
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
        $leasedetails = $request->session()->get('lease');

        ///Attach user to Unit
        $user = User::find($leasedetails->user_id);
        $unit = Unit::find($leasedetails->unit_id);
        $propertyId = $leasedetails->property_id;

        $unit->users()->attach($user, ['property_id' => $propertyId]);

        //   event(new AssignUserToUnit($user, $unitId));


        $tenantdetails = $request->session()->get('tenantdetails');
        $tenantdetails->save();

        $splitRentcharges = $request->session()->get('splitRentcharges');
        if (!empty($splitRentcharges)) {
            Unitcharge::insert($splitRentcharges);
        }

        $depositcharge = $request->session()->get('depositcharge');
        if (!empty($depositcharge)) {
            $depositcharge->save();
        }

        $utilitycharges = $request->session()->get('utilityCharges');
        if (!empty($utilitycharges)) {
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
