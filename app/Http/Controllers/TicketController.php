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
use App\Notifications\TicketAddedNotification;
use App\Notifications\TicketAssignNotification;
use App\Notifications\TicketNotification;
use App\Notifications\TicketTextNotification;
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
use App\Services\CardService;
use App\Services\SmsService;


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
    private $cardService;
    protected $smsService;


    public function __construct(
        TableViewDataService $tableViewDataService,
        InvoiceService $invoiceService,
        ExpenseService $expenseService,
        FilterService $filterService,
        CardService $cardService,
        SmsService $smsService
    ) {
        $this->model = Ticket::class;
        $this->controller = collect([
            '0' => 'ticket', // Use a string for the controller name
            '1' => 'Ticket',
        ]);
        $this->tableViewDataService = $tableViewDataService;
        $this->invoiceService = $invoiceService;
        $this->expenseService = $expenseService;
        $this->filterService = $filterService;
        $this->cardService = $cardService;
        $this->smsService = $smsService;
    }
    public function index(Request $request)
    {
        // Clear previousUrl if navigating to a new create method
        session()->forget('previousUrl');
        $user = Auth::user();
        $filters = $request->except(['tab', '_token', '_method']);
        $filterdata = $this->filterService->getTicketFilters($request);
        $baseQuery = Ticket::ApplyDateFilters($filters);
        $cardDashboad = $this->cardService->ticketCard($baseQuery->get());
        $tabTitles = ['All', 'Pending', 'In Progress','Over Due','Completed','On Hold','Cancelled'];
        $tabContents = [];
        $tabCounts = [];
        foreach ($tabTitles as $title) {
            $query = clone $baseQuery;
            switch ($title) {
                case 'Pending':
                    $query->where('status', Ticket::STATUS_PENDING);
                    break;
                case 'In Progress':
                    $query->where('status', Ticket::STATUS_IN_PROGRESS);
                    break;
                case 'Over Due':
                    $query->where('status', Ticket::STATUS_IN_PROGRESS)
                          ->where('duedate', '<', now());
                    break;
                case 'Completed':
                    $query->where('status', Ticket::STATUS_COMPLETED);
                    break;
                case 'On Hold':
                        $query->where('status', Ticket::STATUS_ON_HOLD);
                        break;
                case 'Cancelled':
                    $query->where('status', Ticket::STATUS_CANCELLED);
                    break;
            // 'All' doesn't need any additional filters
            }
            $tickets = $query->get();
            $count = $tickets->count();
            $tableData = $this->tableViewDataService->getTicketData($tickets, true);
            $controller = $this->controller;
            $tabContents[] = view('admin.CRUD.table', [
                'data' => $tableData,
                'controller' => $controller,
            ])->render();
            $tabCounts[$title] = $count;
        }
    

        return View('admin.CRUD.form', compact( 'controller','tabTitles', 'tabContents','filters','filterdata','cardDashboad','tabCounts'));
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

        if (!session()->has('previousUrl')) {
            session()->put('previousUrl', url()->previous());
        }

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
        $sendSms = $request->send_sms;
       
        $validationRules = Ticket::$validation;
        $validatedData = $request->validate($validationRules);
        $ticketData = new Ticket;
        $ticketData->fill($validatedData);
        $ticketData->status = Ticket::STATUS_PENDING;
        $ticketData->user_id = $user->id;
     //   $ticketData->save();


        ///Create Notification for to the Admins and User/Tenant
        // Get notification recipients
        $property = Property::find($request->property_id);
        $attachedUsers = $property->users()
            ->whereDoesntHave('roles', function ($query) {
                $query->where('name', 'tenant');
            })
            ->distinct()
            ->get();
        $recipients = collect();
        $recipients = $user;
       
        try {
           // Always send email notification t the person who created the ticket
           /** @var \App\Models\User $user */
           $user->notify(new TicketNotification($user, $ticketData));

           if ($sendSms == 1) {
          //  $user->notify(new TicketTextNotification($user, $ticketData)); //Text
            // Check if it's a text or email notification based on request
            $notificationClass = TicketTextNotification::class;
            $notificationParams = ['user' => $user, 'ticket' => $ticketData];
            $result = $this->smsService->sendBulkSms($recipients,$notificationClass,$notificationParams);
           // dd($result);
           }
            
        } catch (\Exception $e) {
            // Log the error or perform any necessary actions
            Log::error('Failed to send ticket notification: ' . $e->getMessage());
        }

        // Notify each user (staff/admins)
        foreach ($attachedUsers as $user) {
            try {
            //    $user->notify(new TicketAddedNotification($user, $ticketData));

            //    if ($sendSms == 1) {
             //   $user->notify(new TicketTextNotification($user, $ticketData)); //Text
                
            //    }
            } catch (\Exception $e) {
                Log::error('Failed to send ticket notification to user ' . $user->id . ': ' . $e->getMessage());
            }
        }

        $redirectUrl = session()->pull('previousUrl', $this->controller['0']);

        return redirect($redirectUrl)->with('status', $this->controller['1'] . ' Added Successfully');
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
        $users =  User::ApplyFilterUsers()
            ->whereDoesntHave('roles', function ($query) use ($tenantRole) {
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
        $ticket->status = Ticket::STATUS_IN_PROGRESS;
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

        return redirect('ticket/' . $ticket->id)->with('status', $this->controller['1'] . ' Edited Successfully');
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
        } else if ($request->chartofaccount_id && $request->charged_to === 'property') {
            $this->expenseService->generateExpense($ticket, null, null, null, null);
        }

        ///Create a charge when ticket is completed.
        return redirect()->back()->with('status', 'The ticket has been edited successfully');
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
            'rate' => $ticket->totalamount,
            'parent_id' => null, ///Add temporary uniqueid
            'recurring_charge' => 'no',
            'startdate' => now(),
            'nextdate' =>  null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->invoiceService->generateInvoice($unitcharge, $ticket);
    }

}
