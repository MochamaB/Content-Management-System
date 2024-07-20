<?php

namespace App\Http\Controllers;

use App\Models\Chartofaccount;
use App\Models\Property;
use App\Models\Ticket;
use App\Models\Unit;
use App\Models\Unitcharge;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorCategory;
use App\Models\Workorder;
use App\Notifications\AdminTicketNotification;
use App\Notifications\TicketAssignNotification;
use App\Notifications\TicketNotification;
use Illuminate\Http\Request;
use App\Traits\FormDataTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Services\TableViewDataService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Contracts\Role;
use Spatie\Permission\Models\Role as ModelsRole;
use Spatie\Permission\Traits\HasRoles;
use App\Services\InvoiceService;
use App\Services\ExpenseService;
use App\Services\FilterService;
class TicketController extends Controller
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
    private $invoiceService;
    private $expenseService;
    private $filterService;


    public function __construct(TableViewDataService $tableViewDataService,InvoiceService $invoiceService,
    ExpenseService $expenseService, FilterService $filterService)
    {
        $this->model = Ticket::class;
        $this->controller = collect([
            '0' => 'ticket', // Use a string for the controller name
            '1' => 'Ticket',
        ]);
        $this->tableViewDataService = $tableViewDataService;
        $this->invoiceService = $invoiceService;
        $this->expenseService = $expenseService;
        $this->filterService = $filterService;
    }
    public function index(Request $request)
    {
        $user = Auth::user();
        $filters = $request->except(['tab','_token','_method']);
        if ($user->hasRole('Tenant')) {
            $tickets = $user->tickets->applyFilters($filters)->get();
        } else {
            $tickets = Ticket::applyFilters($filters)->get();
        }
        $filterdata = $this->filterService->getUnitChargeFilters($request);
        $mainfilter =  Ticket::pluck('category')->toArray();
        //   $filterData = $this->filterData($this->model);
        $controller = $this->controller;
        $tableData = $this->tableViewDataService->getTicketData($tickets, false);

        return View(
            'admin.CRUD.form',
            compact('filterdata', 'tableData', 'controller'),
            //  $filterData,
            [
                //   'cardData' => $cardData,
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
        } elseif ($model === 'units') {
            $unit = Unit::find($id);
            $property = Property::where('id', $unit->property->id)->first();
        }
        $viewData = $this->formData($this->model);

        Session::flash('previousUrl', request()->server('HTTP_REFERER'));

        return View('admin.Maintenance.create_ticket', compact('id', 'property', 'unit', 'model'), $viewData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $validationRules = Ticket::$validation;
        $validatedData = $request->validate($validationRules);
        $ticketData = new Ticket;
        $ticketData->fill($validatedData);
        $ticketData->status = 'New';
        $ticketData->user_id = $user->id;
        $ticketData->save();


        ///Create Notification for to the User/Tenant
        $property = Property::find($request->property_id);
        //     $attachedUsers = $property->users()->whereDoesntHave('roles', function ($query) {
        //        $query->whereIn('name', ['staff', 'tenant']);
        //     })->get();
        $loggeduser = User::find($ticketData->user_id);
        try {
            $loggeduser->notify(new TicketNotification($user, $ticketData));
        } catch (\Exception $e) {
            // Log the error or perform any necessary actions
            Log::error('Failed to send ticket notification: ' . $e->getMessage());
        }


        //   foreach ($attachedUsers as $individualUser) {
        //       $individualUser->notify(new AdminTicketNotification($individualUser, $ticketData));
        //    }

        $previousUrl = Session::get('previousUrl');
        if ($previousUrl) {
            return redirect($previousUrl)->with('status', 'Your request has been sent successfully');
        } else {
            return redirect($this->controller['0'])->with('status', $this->controller['1'] . ' Added Successfully');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //  dd($id);
        $tickets = Ticket::with('workorders', 'workorderExpenses', 'assigned')->find($id);
        $workorders = $tickets->workorders;
        //   $unit->load('property', 'unitSupervisors');
        $pageheadings = collect([
            '0' => $tickets->category,
            '1' => $tickets->property->property_name,
            '2' => $tickets->subject,
        ]);
        $viewData = $this->formData($this->model, $tickets);

        ///Data for Expenses page
        $workorderexpenses = $tickets->workorderExpenses;
        $expensestableData = $this->tableViewDataService->getWorkOrderExpenseData($workorderexpenses, false);

        $incomeAccount = Chartofaccount::whereIn('account_type', ['Income'])->get();
        $incomeAccounts = $incomeAccount->groupBy('account_type');

        $expenseAccount = Chartofaccount::whereIn('account_type', ['Expenses'])->get();
        $expenseAccounts = $expenseAccount->groupBy('account_type');

        //   $requestTableData = $this->tableViewDataService->getTicketData($modelrequests);

        $tabTitles = collect([
            'Summary',
            'Work Order',
            'Expenses'
        ]);

        $tabContents = [];
        foreach ($tabTitles as $title) {
            if ($title === 'Summary') {
                $tabContents[] = View('admin.Maintenance.summary_request', $viewData, compact('tickets', 'incomeAccounts', 'expenseAccounts'))->render();
            } elseif ($title === 'Work Order') {
                $tabContents[] = View('admin.Maintenance.workorder', compact('tickets', 'workorders'))->render();
            } elseif ($title === 'Expenses') {
                $tabContents[] = View('admin.CRUD.index_show', ['tableData' => $expensestableData, 'controller' => ['workorder-expense']], compact('id'));
            }
        }

        return View('admin.CRUD.form', compact('pageheadings', 'tabTitles', 'tabContents'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    public function assign($id)
    {
        $modelrequests = Ticket::find($id);
        $vendorcategory = VendorCategory::all();
        $vendorcategories = $vendorcategory->groupBy('vendor_category');
        $vendors = Vendor::all();
        // Get the "tenant" role
        $tenantRole = ModelsRole::where('name', 'tenant')->first();

        // Get all users except those with the "tenant" role
        $users = User::whereDoesntHave('roles', function ($query) use ($tenantRole) {
            $query->where('role_id', $tenantRole->id);
        })->get();

        Session::flash('previousUrl', request()->server('HTTP_REFERER'));

        return View('admin.Maintenance.assign', compact('vendorcategories', 'modelrequests', 'vendors', 'users'));
    }

    public function updateassign(Request $request, $id)
    {
        $validatedData = $request->validate([
            'assigned_type' => 'required',
            'assigned_id' => 'required',

        ]);

        $ticket = Ticket::find($id);
        $ticket->fill($validatedData);
        $ticket->status = 'Assigned';
        $ticket->update();

        ///Send email on update or Assign
        if ($request->assigned_type === "App\\Models\\User") {
            $user = User::find($request->assigned_id);
        } else {
            $user = Vendor::find($request->assigned_id);
        }
        try {
            $user->notify(new TicketAssignNotification($user, $ticket));
        } catch (\Exception $e) {
            // Log the error or perform any necessary actions
            Log::error('Failed to send payment notification: ' . $e->getMessage());
        }
       

        ///Create a charge when ticket is completed.
      
            return redirect('ticket/'.$ticket->id)->with('status', $this->controller['1'] . ' Edited Successfully');
        
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        // dd($request->all());
        $ticket = Ticket::find($id);
        $ticket->fill($request->all());
        $ticket->update();

        /// When thre ticket is marked as completed
        /// If charge is to tenant create unit charge and generate invoice
        if ($request->chartofaccount_id && $request->charged_to === 'tenant') {
            $this->createCharge($ticket);
        }else if($request->chartofaccount_id && $request->charged_to === 'property'){
            $this->expenseService->generateExpense($ticket,null,null,null,null);
        }
        
        ///Create a charge when ticket is completed.
        return redirect()->back()->with('status','The ticket has been edited successfully');
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function createCharge(Ticket $ticket)
    {
        $unitcharge = Unitcharge::create([
            'property_id' => $ticket->property_id,
            'unit_id' => $ticket->unit_id ?? 0,
            'chartofaccounts_id' => $ticket->chartofaccount_id,
            'charge_name' => $ticket->category,
            'charge_cycle' => 'once', ///Charge cycle is same to the rent cycle
            'charge_type' => 'fixed',
            'rate' =>$ticket->totalamount,
            'parent_id' => null, ///Add temporary uniqueid
            'recurring_charge' => 'no',
            'startdate' => now(),
            'nextdate' =>  null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->invoiceService->generateInvoice($unitcharge,$ticket);
    }

    public function createExpense()
    {

    }
}
