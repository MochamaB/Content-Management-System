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
use App\Models\Setting;
use App\Notifications\LeaseAgreementTextNotification;
use Illuminate\Support\Facades\Log;
use App\Services\FilterService;
use App\Services\CardService;
use App\Services\SmsService;


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
    private $filterService;
    private $cardService;
    protected $smsService;



    public function __construct(
        UpdateNextDateAction $updateNextDateAction,
        UpdateDueDateAction $updateDueDateAction,
        UploadMediaAction $uploadMediaAction,
        DepositService $DepositService,
        InvoiceService $invoiceService,
        RecordTransactionAction $recordTransactionAction,
        TableViewDataService $tableViewDataService,
        FilterService $filterService, CardService $cardService,
        SmsService $smsService
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
        $this->filterService = $filterService;
        $this->cardService = $cardService;
        $this->smsService = $smsService;
    }
    public function index(Request $request)
    {
        //  $user = Auth::user();
         // Clear previousUrl if navigating to a new  method
        session()->forget('previousUrl');
        $filters = $request->except(['tab','_token','_method']);
        $filterdata = $this->filterService->getLeaseFilters($request);
        $baseQuery = lease::with('property', 'unit', 'user')->showSoftDeleted()->ApplyFilters($filters);
        $cardData = $this->cardService->leaseCard($baseQuery->get());

        $tabTitles = ['All'] + Lease::$statusLabels;
        $tabContents = [];
        $tabCounts = [];
        foreach ($tabTitles as $title) {
            $query = clone $baseQuery;
            switch ($title) {
                case 'All':
                    // No additional filtering for 'All'
                    break;
                case 'Active':
                    $query->where('status', Lease::STATUS_ACTIVE);
                    break;
                case 'Terminated':
                    $query->where('status', Lease::STATUS_TERMINATED);
                    break;
                case 'Expired':
                    $query->where('status', Lease::STATUS_EXPIRED);
                    break;
                case 'Pending':
                    $query->where('status', Lease::STATUS_PENDING);
                    break;
                case 'Notice Given':
                    $query->where('status', Lease::STATUS_NOTICE);
                    break;
                default:
                    // Optional: handle any unexpected titles
                    continue;
            }
            $lease = $query->get();
            $count = $lease->count();
            $tableData = $this->tableViewDataService->getLeaseData($lease, false);
            $controller = $this->controller;
            $tabContents[] = view('admin.CRUD.table', [
                'data' => $tableData,
                'controller' => $controller,
            ])->render();
            $tabCounts[$title] = $count;
        }
       
        /// TABLE DATA ///////////////////////////
        $tableData = [
            'headers' => ['LEASE', 'TYPE', 'STATUS', 'ACTIONS'],
            'rows' => [],
        ];

      

        return View(
            'admin.CRUD.form',compact('tabTitles', 'tabContents','controller','filterdata','filters','cardData','tabCounts')
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

        //1. Lease Details ///
        $properties = Property::pluck('property_name', 'id')->toArray();
        $role = 'Tenant'; // Replace with the desired role
        if (!Role::where('name', $role)->exists()) {
            return back()->with('statuserror', 'There no tenant Role in system. Create Role and Tenants First');
        }
        $tenants = User::withoutActiveLease($role)->get(); ///tenantdetailsview
        $lease = $request->session()->get('wizard_lease');

        //2. Tenant Details/Cosigners
        $tenantdetails = $request->session()->get('wizard_tenantdetails');

        //3. Rent Charge
        $rentcharge = $request->session()->get('wizard_rentcharge');
        $existingRentCharge =null;
        $existingSplitRentCharge =null;
        if (!empty($lease)) {
        $existingRentCharge = Unitcharge::where('unit_id', $lease->unit_id)
            ->where('charge_name', 'Rent')
            ->first();

        $existingSplitRentCharge = Unitcharge::where('unit_id', $lease->unit_id)
        ->where('parent_id', $existingRentCharge->id)
        ->get();
        }
        $splitRentcharges = $request->session()->get('wizard_splitRentcharges');

        //4. Deposit Charge /////
        $depositcharge = $request->session()->get('wizard_depositcharge');
        $account = Chartofaccount::whereIn('account_type', ['Income'])->get();
        $accounts = $account->groupBy('account_type');
        $depositaccount = Chartofaccount::whereIn('account_type',['Liability'])->get();
        $depositaccounts = $depositaccount->groupBy('account_type');

        ///5. Utilities ////
        $utilities = Utility::where('property_id', $lease->property_id ?? '')->get();
        $existingUtilityCharge =null;
        if (!empty($lease)) {
        $existingUtilityCharge = Unitcharge::where('unit_id', $lease->unit_id)
            ->whereNull('parent_id')
            ->whereNotIn('charge_name', ['Rent', 'security deposit'])
            ->get();
        }
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
                $stepContents[] = View('wizard.lease.rent', compact('accounts', 'lease', 'rentcharge', 'splitRentcharges','existingRentCharge','existingSplitRentCharge','sessioncharges'))->render();
            } elseif ($title === 'Security Deposit') {
                $stepContents[] = View('wizard.lease.deposit', compact('depositaccounts', 'lease', 'depositcharge'))->render();
            } elseif ($title === 'Utilities') {
                $stepContents[] = View('wizard.lease.utilities', compact('lease', 'rentcharge', 'utilities', 'sessioncharges','existingUtilityCharge'))->render();
            } elseif ($title === 'Lease Agreement') {
                $stepContents[] = View('wizard.lease.leaseagreement')->render();
            }
        }

        return View('admin.Lease.lease', compact('steps', 'stepContents', 'activetab'));
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
            // 2. Check if a rent charge already exists for this unit and property
            $existingCharge = Unitcharge::where('unit_id', $rentcharge->unit_id)
                ->where('charge_name', $rentcharge->charge_name)
                ->first();
    
            if ($existingCharge) {
                // Update the existing charge
                $existingCharge->fill($rentcharge->toArray());
                $existingCharge->updated_at = now();
                $existingCharge->save();
                 // Use the existing charge's ID as the parent_id
                $parentChargeId = $existingCharge->id;

    
                // Log or return feedback for an updated charge
                $statusMessage = 'Existing Rent Charge Updated Successfully.';
            } else {
                // Create a new rent charge
                $rentchargeModel = new Unitcharge();
                $rentchargeModel->fill($rentcharge->toArray());
                $rentchargeModel->save();
                // Use the newly created charge's ID as the parent_id
                $parentChargeId = $rentchargeModel->id;
    
                // Log or return feedback for a new charge
                $statusMessage = 'New Rent Charge Created Successfully.';
            }
        }
        /*
        if (!empty($rentcharge)) {
            $rentchargeModel = new Unitcharge();
            $rentchargeModel->fill($rentcharge->toArray());
            $rentchargeModel->save();

            // Get the ID of the newly created rent charge
            $newRentChargeId = $rentchargeModel->id;
        } */

        ///4. SAVE SPLITRENTCHARGE
        $splitRentCharges = $request->session()->get('wizard_splitRentcharges');
        if (!empty($splitRentCharges)) {
            foreach ($splitRentCharges as $splitRentCharge) {
                // Dynamically add the parent_id to each split rent charge
                $splitRentCharge['parent_id'] = $parentChargeId;
        
                // Use updateOrCreate to update existing charges or create new ones
                Unitcharge::updateOrCreate(
                    [
                        'unit_id' => $splitRentCharge['unit_id'],
                        'charge_name' => $splitRentCharge['charge_name'], // Uniqueness based on unit and charge name
                    ],
                    $splitRentCharge // Fillable data for update or creation
                );
            }
        }
        /*
        if (!empty($splitRentCharges)) {
            foreach ($splitRentCharges as &$splitRentCharge) {
                $splitRentCharge['parent_id'] = $newRentChargeId;
            }
            // Save the updated split rent charges
            Unitcharge::insert($splitRentCharges);
        } */

        //5. SAVE SECURITY DEPOSIT AND GENERATE PAYMENT VOUCHER AND TRANSACTIONS
        $depositcharge = $request->session()->get('wizard_depositcharge');
        if (!empty($depositcharge)) {
            $user = User::find($leasedetails->user_id);
            $depositchargeModel = new Unitcharge();
            $depositchargeModel->fill($depositcharge->toArray());
            $depositchargeModel->save();

            //  Generate Payment Voucher and Transactions
            $this->DepositService->generateDeposit($depositchargeModel, $user);
        }

        //6. SAVE UTILITY CHARGES
        $utilitycharges = $request->session()->get('wizard_utilityCharges');
        if (!empty($utilitycharges)) {
            foreach ($utilitycharges as $charge) {

                unset($charge['id']); // if id exists in the array

                Unitcharge::updateOrCreate(
                    [
                        // Unique identifying fields
                        'unit_id' => $charge['unit_id'],
                        'charge_name' => $charge['charge_name']
                    ],
                    $charge
                );
        }
    }

        //7. ATTACH TENANT USER TO UNIT
        $user = User::find($leasedetails->user_id);
        $unit = Unit::find($leasedetails->unit_id);
        $propertyId = $leasedetails->property_id;
        $unit->users()->attach($user, ['property_id' => $propertyId]);

        //8. SEND EMAIL TO THE TENANT AND THE PROPERTY MANAGERS
     //   $notificationsEnabled = Setting::getSettingForModel(get_class($lease), $lease->id, 'leasenotifications');
        $user = User::find($lease->user_id);
        // Redirect to the lease.create route with a success message
        try {
            if ($request->has('send_welcome_email')) {
                // Send notifications
            $user->notify(new LeaseAgreementNotification($user)); ///// Send Lease Agreement
            }

            if ($request->has('send_welcome_text')) {

                $recipients = collect([$user]);
                $notificationClass = LeaseAgreementTextNotification::class;
                $notificationParams = ['user' => $user];
        
                foreach($recipients as $recipient){
                    $result = $this->smsService->queueSmsNotification($recipient,$notificationClass, $notificationParams);
                    }
         //   $user->notify(new LeaseAgreementTextNotification($user)); ///// Send Lease Agreement
            }
        } catch (\Exception $e) {
            // Log the error or perform any necessary actions
            Log::error('Failed to send payment notification: ' . $e->getMessage());
        }


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
        /// DATA FOR TICKETS TAB
        $tickets = $unit->tickets;
        // dd($payments);
        $ticketTableData = $this->tableViewDataService->getTicketData($tickets);

        ///DATA FOR FILES //////
        $collections = ['picture', 'pdf', 'collection3'];
        $files = $unit->getMedia('Lease-Agreement');
        //   dd($files);
        $mediaTableData = $this->tableViewDataService->getMediaData($files);
        $id = $unit;
        $model = 'units';

         //5. SETTINGS
         $namespace = 'App\\Models\\'; // Adjust the namespace according to your application structure
         // Combine the namespace with the class name
         $modelType = $namespace . 'Lease';
         $individualsetting = $lease->settings;
         $setting = Setting::where('model_type', $modelType)->first();
        
         $settingTableData = $this->tableViewDataService->generateSettingTabContents($modelType, $setting, $individualsetting);
       


        $tabTitles = collect([
            'Summary',
            'Charges and Utilities',
            'Invoices',
            'Payments',
            'Meter Readings',
            'Tickets',
            'Files',
            'Settings'
        ]);
        $tabContents = [];
        foreach ($tabTitles as $title) {
            if ($title === 'Summary') {
                $tabContents[] = View('admin.CRUD.summary', compact('properties', 'lease'))->render();
            } elseif ($title === 'Charges and Utilities') {
                $tabContents[] = View('admin.CRUD.index_show', ['tableData' => $unitChargeTableData, 'controller' => ['unitcharge']], compact('id', 'model'))->render();
            } elseif ($title === 'Invoices') {
                $tabContents[] = View('admin.CRUD.table', ['data' => $invoiceTableData, 'controller' => ['invoice']])->render();
            } elseif ($title === 'Payments') {
                $tabContents[] = View('admin.CRUD.table', ['data' => $paymentTableData, 'controller' => ['payment']])->render();
            } elseif ($title === 'Meter Readings') {
                $tabContents[] = View('admin.CRUD.index_show', ['tableData' => $MeterReadingsTableData, 'controller' => ['meter-reading']], compact('id', 'model'))->render();
            } elseif ($title === 'Tickets') {
                $tabContents[] = View('admin.CRUD.index_show', ['tableData' => $ticketTableData, 'controller' => ['ticket']], compact('id', 'model'))->render();
            } elseif ($title === 'Files') {
                $tabContents[] =  View('admin.CRUD.index_show', ['tableData' => $mediaTableData, 'controller' => ['']], compact('id'))->render();
            }elseif ($title === 'Settings') {
                $tabContents[] = View('admin.CRUD.tabs_horizontal_show', 
                ['tabTitles' => $settingTableData['tabTitles'], 
                'tabContents' => $settingTableData['tabContents'],
                'controller' => ['setting']], 
                compact('model','id'))->render();
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


        return View('admin.Lease.lease', compact('pageheadings', 'properties', 'lease'));
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
        ->where(function ($query) {
            $query->doesntHave('lease')
                ->orWhereHas('lease', function ($subQuery) {
                    $subQuery->where('status', '<>', Lease::STATUS_ACTIVE);
                });
        })
        ->pluck('unit_number', 'id')
        ->toArray();


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
                ->whereNull('parent_id')
                ->exists();

            if ($utilityNameExists || $chargeNameExists) {
                return response()->json(['message' => 'The Charge already exists. Choose another name.'], 422);
            }
        }

        // If the charge name does not exist, return a success response
        //  return response()->json(['message' => 'Success!'], 200);
    }
    /////////// LEASE WIZARD FUNCTIONS/////////////////
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
    public function rent(Request $request)
    {
        ////1. VALIDATE FIELD
        $validationRules = Unitcharge::$validation;
        $validatedData = $request->validate($validationRules);
        $rules = [
            'splitcharge_name.*' => 'required|string|max:255',
            // Add other validation rules for the remaining fields
        ];
        $request->validate($rules);

        ///2. GET VALUE OF START DATE NEXT DATE ON THE CHARGE/////////
        $chargeCycle = $request->input('charge_cycle');
        $startDate = Carbon::parse($request->input('startdate'));
        $chargeType = $request->input('charge_type');
        /// 2.1. Use the action to update the next date
        $result = $this->updateNextDateAction->handle($chargeCycle, $startDate,$chargeType );
        $updatedAt = $result['updatedAt'];
        $nextDate = $result['nextDate'];


        if (empty($request->session()->get('wizard_rentcharge'))) {
            $rentcharge = new Unitcharge();
            $rentcharge->fill($validatedData);
            $rentcharge->nextdate = $nextDate;
            $rentcharge->updated_at = $updatedAt;
            $request->session()->put('wizard_rentcharge', $rentcharge);
            //     $rentcharge->save();
        } else {
            $rentcharge = $request->session()->get('wizard_rentcharge');
            $rentcharge->fill($validatedData);
            $rentcharge->nextdate = $nextDate;
            $rentcharge->updated_at = $updatedAt;
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
                    ->whereNull('parent_id')
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
                    'updated_at' => $rentcharge->updated_at,
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

    public function securitydeposit(StoreUnitChargeRequest $request)
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
                $chargeType = $request->input("charge_type.{$index}");
                /// 2.1. Use the action to update the next date
                $result = $this->updateNextDateAction->handle($chargeCycle, $startDate,$chargeType );
                    $updatedAt = $result['updatedAt'];
                    $nextDate = $result['nextDate'];

                $utilitycharge = [
                    'property_id' => $request->input('property_id'),
                    'unit_id' => $request->input('unit_id'),
                    'chartofaccounts_id' => $request->input("chartofaccounts_id.{$index}"),
                    'utility_id' => $request->input("utility_id.{$index}"),
                    'charge_name' => $chargeName,
                    'charge_cycle' => $request->input("charge_cycle.{$index}"),
                    'charge_type' => $request->input("charge_type.{$index}"),
                    'rate' => $request->input("rate.{$index}"),
                    'parent_id' => $request->input('parent_id'),
                    'recurring_charge' => $request->input('recurring_charge'),
                    'startdate' => $startDate,
                    'nextdate' => $nextDate,
                    'created_at' => now(),
                    'updated_at' => $updatedAt,
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
