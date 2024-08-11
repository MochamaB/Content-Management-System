<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Workorder;
use App\Models\WorkorderExpense;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Actions\CalculateInvoiceTotalAmountAction;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorCategory;
use App\Notifications\TicketWorkOrderNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role as ModelsRole;

class WorkOrderController extends Controller
{
    private $calculateTotalAmountAction;

    public function __construct(CalculateInvoiceTotalAmountAction $calculateTotalAmountAction)
    {
        $this->calculateTotalAmountAction = $calculateTotalAmountAction;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $user = Auth::user();
        $tickets = Ticket::with('assigned')->find($id);

        if (is_null($tickets->assigned)) {
            return redirect()->back()->with('statuserror', ' First assign this ticket to create work orders.');
        }

        $statuses = ['completed', 'closed', 'cancelled'];
        if (in_array($tickets->status, $statuses)) {
            return redirect()->back()->with('statuserror', ' Change status to in-progress to add a workorder.');
        }
        $modelrequests = Ticket::find($id);
        $vendorcategory = VendorCategory::all();
        $vendorcategories = $vendorcategory->groupBy('vendor_category');
        $vendors = Vendor::all();
        // Get the "tenant" role
        $tenantRole = ModelsRole::where('name', 'tenant')->first();

        // Get all users except those with the "tenant" role
        $users = $user->filterUsers();
        //  $users = User::whereDoesntHave('roles', function ($query) use ($tenantRole) {
        //      $query->where('role_id', $tenantRole->id);
        //  })->get();

        Session::flash('previousUrl', request()->server('HTTP_REFERER'));
        return View('admin.Maintenance.create_workorder', compact('tickets', 'vendorcategory', 'vendors', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        // $user = Auth::user();
        $validationRules = Workorder::$validation;
        $validatedData = $request->validate($validationRules);
        $workOrder = new Workorder();
        $workOrder->fill($validatedData);
        $workOrder->save();

        //// send Notification
        $workOrder->load('tickets','users');
        $assignedUser = $workOrder->tickets->assigned;
        $ticketUser = $workOrder->users;
        $users = [$assignedUser, $ticketUser]; // Array of users to notify
        try {
            Notification::send($users, new TicketWorkOrderNotification($users, $workOrder->ticket, $workOrder));
        } catch (\Exception $e) {
            // Log the error or perform any necessary actions
            Log::error('Failed to send payment notification: ' . $e->getMessage());
        }

        $previousUrl = Session::get('previousUrl');
        if ($previousUrl) {
            return redirect($previousUrl)->with('status', 'Your Work Order Item has been saved successfully');
        } else {
            return redirect('ticket')->with('status', ' WorkOrder Added Successfully');
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
        //
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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    public function expense($id)
    {
        $ticket = Ticket::find($id);
        $statuses = ['completed', 'closed', 'cancelled'];
        if (in_array($ticket->status, $statuses)) {
            return redirect()->back()->with('statuserror', ' Change status to in-progress to add a workorder.');
        }
        Session::flash('previousUrl', request()->server('HTTP_REFERER'));
        return View('admin.Maintenance.expense', compact('ticket'));
    }

    public function postexpense(Request $request)
    {
        $ticket = Ticket::find($request->ticket_id);
        $validationRules = WorkorderExpense::$validation;
        $validator = Validator::make($request->all(), $validationRules);

        // Check if validation fails
        if ($validator->fails()) {
            // Validation failed, return the validation errors
            return redirect()->back()->withInput()->with('statuserror', 'Check on errors'); // Adjust the response as needed
        }
        $expenses = [];
        if (!empty($request->input('ticket_id'))) {
            foreach ($request->input('item') as $index => $expense) {

                $expense = [
                    'ticket_id' => $request->ticket_id,
                    'quantity' => $request->input("quantity.{$index}"),
                    'item' => $request->input("item.{$index}"),
                    'price' => $request->input("price.{$index}"),
                    'amount' => $request->input("amount.{$index}"),
                    'created_at' => now(), // Add the current timestamp for created_at
                    'updated_at' => now(),
                    // ... Other fields ...
                ];
                $expenses[] = $expense;
            }
        }
        WorkorderExpense::insert($expenses);
        //Update total amount in ticket ////////
        $this->calculateTotalAmountAction->ticket($ticket);

        $previousUrl = Session::get('previousUrl');

        return redirect($previousUrl)->with('status', 'Your Expenses have been saved successfully');
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
}
