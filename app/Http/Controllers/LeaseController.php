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
use App\Actions\UpdateDueDateAction;
use App\Actions\RecordTransactionAction;
use App\Services\DepositService;
use App\Services\InvoiceService;
use App\Services\TableViewDataService;
use App\Actions\UploadMediaAction;

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
    private $updateDueDateAction;
    protected $uploadMediaAction;
    private $DepositService;
    private $invoiceService;
    private $recordTransactionAction;
    private $tableViewDataService;



    public function __construct(
        UpdateNextDateAction $updateNextDateAction,
        UpdateDueDateAction $updateDueDateAction,
        UploadMediaAction $uploadMediaAction,
        DepositService $DepositService,
        InvoiceService $invoiceService,
        RecordTransactionAction $recordTransactionAction,
        TableViewDataService $tableViewDataService
    ) {
        $this->model = Lease::class;

        $this->controller = collect([
            '0' => 'lease', // Use a string for the controller name
            '1' => ' Lease',
        ]);

        $this->updateNextDateAction = $updateNextDateAction;
        $this->updateDueDateAction = $updateDueDateAction;
        $this->uploadMediaAction = $uploadMediaAction;
        $this->DepositService = $DepositService;
        $this->invoiceService = $invoiceService;
        $this->recordTransactionAction = $recordTransactionAction;
        $this->tableViewDataService = $tableViewDataService;
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
                'Suspended' => 'badge-danger',
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
        $lease = $request->session()->get('wizard_lease');
        $tenantdetails = $request->session()->get('wizard_tenantdetails');
        $rentcharge = $request->session()->get('wizard_rentcharge');
        $splitRentcharges = $request->session()->get('wizard_splitRentcharges');
        $depositcharge = $request->session()->get('wizard_depositcharge');
        $account = Chartofaccount::whereIn('account_type', ['Income', 'Liability'])->get();
        $accounts = $account->groupBy('account_type');
        $utilities = Utility::where('property_id', $lease->property_id ?? '')->get();
        $sessioncharges = $request->session()->get('wizard_utilityCharges');

        //  dd($utilityCharges);

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
                $stepContents[] = View('wizard.lease.utilities', compact('lease', 'rentcharge', 'utilities', 'sessioncharges'))->render();
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
        $request->validate([
            'leaseagreement' => 'required|mimes:csv,txt,pdf|max:2048',
        ]);
        
        ///1. SAVE LEASE DETAILS
        $leasedetails = $request->session()->get('wizard_lease');
        if (!empty($leasedetails)) {
            $lease = new Lease();
            $lease->fill($leasedetails->toArray());
            $lease->save();
        }


        ///2. SAVE TENANT COSIGNER DETAILS
        $tenantdetails = $request->session()->get('wizard_tenantdetails');
        if (!empty($tenantdetails)) {
            $tenantdetailsModel = new Tenantdetails();
            $tenantdetailsModel->fill($tenantdetails->toArray());
            $tenantdetailsModel->save();
        }

        //3. SAVE RENT CHARGE
        $rentcharge = $request->session()->get('wizard_rentcharge');
        if (!empty($rentcharge)) {
            $rentchargeModel = new Unitcharge();
            $rentchargeModel->fill($rentcharge->toArray());
            $rentchargeModel->save();

            // Get the ID of the newly created rent charge
            $newRentChargeId = $rentchargeModel->id;
        }

        ///4. SAVE SPLITRENTCHARGE
        $splitRentCharges = $request->session()->get('splitRentcharges');
        if (!empty($splitRentCharges)) {
            foreach ($splitRentCharges as &$splitRentCharge) {
                $splitRentCharge['parent_id'] = $newRentChargeId;
            }
            // Save the updated split rent charges
            Unitcharge::insert($splitRentCharges);
        }

        //5. SAVE SECURITY DEPOSIT AND GENERATE PAYMENT VOUCHER AND TRANSACTIONS
        $depositcharge = $request->session()->get('wizard_depositcharge');
        if (!empty($depositcharge)) {
            $user = User::find($leasedetails->user_id);
            $depositchargeModel = new Unitcharge();
            $depositchargeModel->fill($depositcharge->toArray());
            $depositchargeModel->save();

            //  Generate Payment Voucher and Transactions
           $this->DepositService->generateDeposit($depositchargeModel,$user);

        }

        //6. SAVE UTILITY CHARGES
        $utilitycharges = $request->session()->get('wizard_utilityCharges');
        if (!empty($utilitycharges)) {
            Unitcharge::insert($utilitycharges);
        }

        //7. ATTACH TENANT USER TO UNIT
        $user = User::find($leasedetails->user_id);
        $unit = Unit::find($leasedetails->unit_id);
        $propertyId = $leasedetails->property_id;
        $unit->users()->attach($user, ['property_id' => $propertyId]);

         //8. SEND EMAIL TO THE TENANT AND THE PROPERTY MANAGERS
         $user = User::find($lease->user_id);
         // Redirect to the lease.create route with a success message
         $user->notify(new LeaseAgreementNotification($user)); ///// Send Lease Agreement
 

        //8. UPLOAD LEASE AGREEMENT
      //  $unit
        //    ->addMediaFromRequest('leaseagreement')
         //   ->withProperties(['unit_id' => $unit->id, 'property_id' => $propertyId])
         //   ->toMediaCollection('Lease-Agreement');
        $this->uploadMediaAction->handle($unit, 'leaseagreement', 'Lease-Agreement', $request);

        

       
        //10. CREATE SETTING FOR THE DUEDATE
        //pass the lease instance to the action
       // $this->updateDueDateAction->duedate($lease);

        //11. FORGET SESSION DATA
        $request->session()->forget('wizard_lease');
        $request->session()->forget('wizard_tenantdetails');
        $request->session()->forget('wizard_rentcharge');
        $request->session()->forget('wizard_splitRentcharges');
        $request->session()->forget('wizard_depositcharge');
        $request->session()->forget('wizard_utilityCharges');

        return redirect()->route('lease.index')->with('status', 'Lease Created Successfully');
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
        $charges = $unit->unitcharges()->whereNull('parent_id')->get();  ///data for utilities page
        $unitChargeTableData = $this->tableViewDataService->getUnitChargeData($charges);

        /// DATA FOR INVOICES TAB
        $invoices = $unit->invoices;
      //  dd($unit->invoices);
        $invoiceTableData = $this->tableViewDataService->getInvoiceData($invoices);

        $payments = $unit->payments;
        // dd($payments);
         $paymentTableData = $this->tableViewDataService->getPaymentData($payments);

        $meterReadings = $unit->meterReadings;
        $MeterReadingsTableData = $this->tableViewDataService->getMeterReadingsData($meterReadings);
        $id = $unit;

        $tabTitles = collect([
            'Summary',
            'Charges and Utilities',
            'Invoices',
            'Payments',
            'Meter Readings',
            'Maintenance Tasks',
            'Files',
        ]);
        $tabContents = [];
        foreach ($tabTitles as $title) {
            if ($title === 'Summary') {
                $tabContents[] = View('admin.CRUD.summary', compact('properties', 'lease'))->render();
            } elseif ($title === 'Charges and Utilities') {
                $tabContents[] = View('admin.CRUD.index_show', ['tableData' => $unitChargeTableData, 'controller' => ['unitcharge']])->render();
            } elseif ($title === 'Invoices') {
                $tabContents[] = View('admin.CRUD.index_show', ['tableData' => $invoiceTableData, 'controller' => ['invoice']])->render();
            } elseif ($title === 'Payments') {
                $tabContents[] = View('admin.CRUD.index_show',['tableData' => $paymentTableData, 'controller' => ['payment']])->render();
            } elseif ($title === 'Meter Readings') {
                $tabContents[] = View(
                    'admin.CRUD.index_show',
                    ['tableData' => $MeterReadingsTableData, 'controller' => ['meter-reading']],
                    compact('id')
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
    /////////// lease wizard/////////////////
    public function leasedetails(Request $request)
    {

        $validationRules = Lease::$validation;
        $validatedData = $request->validate($validationRules);

        if (empty($request->session()->get('wizard_lease'))) {
            $lease = new Lease();
            $lease->fill($validatedData);
            $request->session()->put('wizard_lease', $lease);
            //      $lease->save();
        } else {
            $lease = $request->session()->get('wizard_lease');
            $lease->fill($validatedData);
            $request->session()->put('wizard_lease', $lease);
            //     $lease->update();
        }


        return redirect()->route('lease.create', ['active_tab' => '1'])
            ->with('status', 'Lease Created Successfully. Enter Tenant Details');
    }

    public function cosigner(Request $request)
    {

        $validatedData = $request->validate([
            'user_id' => 'required',
            'user_relationship' => 'required',
            'emergency_name' => 'required',
            'emergency_number' => 'required',
            'emergency_email' => 'required|email',
        ]);

        if (empty($request->session()->get('wizard_tenantdetails'))) {
            $tenantdetails = new Tenantdetails();
            $tenantdetails->fill($validatedData);
            $request->session()->put('wizard_tenantdetails', $tenantdetails);
        } else {
            $tenantdetails = $request->session()->get('wizard_tenantdetails');
            $tenantdetails->fill($validatedData);
            $request->session()->put('wizard_tenantdetails', $tenantdetails);
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


        if (empty($request->session()->get('wizard_rentcharge'))) {
            $rentcharge = new Unitcharge();
            $rentcharge->fill($validatedData);
            $rentcharge->nextdate = $nextDate;
            $request->session()->put('wizard_rentcharge', $rentcharge);
            //     $rentcharge->save();
        } else {
            $rentcharge = $request->session()->get('wizard_rentcharge');
            $rentcharge->fill($validatedData);
            $rentcharge->nextdate = $nextDate;
            $request->session()->put('wizard_rentcharge', $rentcharge);
            //      $rentcharge->update();
        }
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
                    return redirect()->back()->with('statuserror', 'Charge Name ' . $chargeName . ' already in the list of Utilities for the property/unit.');
                }
            }
        }

        // Call the splitRentcharges function 
        $this->splitRentcharges($request);


        return redirect()->route('lease.create', ['active_tab' => '3'])
            ->with('status', 'Rent Assigned Successfully. Enter Security Deposit Details');
    }

    public function splitRentcharges(Request $request)
    {
        //1. GET SESSION DATA
        $rentcharge = $request->session()->get('wizard_rentcharge');
        $rentchargeIdentifier = uniqid('rentcharge_', true);

        ///2. ADD SPLITRENTCHARGES TO THE SESSION
        $splitRentCharges = [];

        if (!empty($request->input('splitcharge_name'))) {
            foreach ($request->input('splitcharge_name') as $index => $chargeName) {
             //   dd($request->input("splitcharge_type.{$index}"));
                $splitRentCharge = [
                    'property_id' => $rentcharge->property_id,
                    'unit_id' => $rentcharge->unit_id,
                    'chartofaccounts_id' => $request->input("splitchartofaccounts_id.{$index}"),
                    'charge_name' => $chargeName,
                    'charge_cycle' => $rentcharge->charge_cycle, ///Charge cycle is same to the rent cycle
                    'charge_type' => $request->input("splitcharge_type.{$index}"),
                    'rate' => $request->input("splitrate.{$index}"),
                    'parent_id' => $rentchargeIdentifier, ///Add temporary uniqueid
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

        if (empty($request->session()->get('wizard_splitRentcharges'))) {
            $request->session()->put('wizard_splitRentcharges', $splitRentCharges);
        } else {
            $request->session()->put('wizard_splitRentcharges', $splitRentCharges);
        }
    }

    public function deposit(StoreUnitChargeRequest $request)
    {
        $validatedData = $request->validated();
        if (empty($request->session()->get('wizard_depositcharge'))) {
            $depositcharge = new Unitcharge();
            $depositcharge->fill($validatedData);
            $request->session()->put('wizard_depositcharge', $depositcharge);
            //   $charge->save();
        } else {
            $depositcharge = $request->session()->get('wizard_depositcharge');
            $depositcharge->fill($validatedData);
            $request->session()->put('wizard_depositcharge', $depositcharge);
            //   $charge->update();
        }

        return redirect()->route('lease.create', ['active_tab' => '4'])
            ->with('status', 'Deposit Assigned Successfully. Enter Utility Details');
    }

    public function skiprent(Request $request)
    {
        /// FORGET THE RENT CHARGE SESSION IF THIS IS SKIPPED
        $request->session()->forget('wizard_rentcharge');
        $request->session()->forget('wizard_splitRentcharges');
        $request->session()->forget('wizard_depositcharge');
        return redirect()->route('lease.create', ['active_tab' => '3'])
            ->with('status', 'Rent details skipped. Add Security Deposit');
    }
    public function skipdeposit(Request $request)
    {
        /// FORGET THE RENT CHARGE SESSION IF THIS IS SKIPPED
        $request->session()->forget('wizard_rentcharge');
        $request->session()->forget('wizard_splitRentcharges');
        $request->session()->forget('wizard_depositcharge');
        return redirect()->route('lease.create', ['active_tab' => '4'])
            ->with('status', 'Deposit details skipped. Add Utilities');
    }

    public function assignUtilities(Request $request)
    {
        $utilityCharges = [];
        if (!empty($request->input('charge_name'))) {
           

            foreach ($request->input('charge_name') as $index => $chargeName) {

                $chargeCycle = $request->input("charge_cycle.{$index}");
                $startDate = Carbon::parse($request->input("startdate.{$index}"));
                /// 2.1. Use the action to update the next date
                $nextDate = $this->updateNextDateAction->handle($chargeCycle, $startDate);

                $utilitycharge = [
                    'property_id' => $request->input('property_id'),
                    'unit_id' => $request->input('unit_id'),
                    'chartofaccounts_id' => $request->input("chartofaccounts_id.{$index}"),
                    'charge_name' => $chargeName,
                    'charge_cycle' => $request->input("charge_cycle.{$index}"),
                    'charge_type' => $request->input("charge_type.{$index}"),
                    'rate' => $request->input("rate.{$index}"),
                    'parent_id' => $request->input('parent_id'),
                    'recurring_charge' => $request->input('recurring_charge'),
                    'startdate' => $startDate,
                    'nextdate' => $nextDate,
                    'created_at' => now(),
                    'updated_at' => now(),
                    // ... Other fields ...
                ];


                $utilityCharges[] = $utilitycharge;
            }
        }
        $request->session()->put('wizard_utilityCharges', $utilityCharges);

        return redirect()->route('lease.create', ['active_tab' => '5'])
            ->with('status', 'Utilities Assigned Successfully. Accept Terms and Conditions');
    }
}
