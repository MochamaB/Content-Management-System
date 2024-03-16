<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorCategory;
use Illuminate\Http\Request;
use App\Traits\FormDataTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Services\TableViewDataService;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Contracts\Role;
use Spatie\Permission\Models\Role as ModelsRole;
use Spatie\Permission\Traits\HasRoles;

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

    public function __construct(TableViewDataService $tableViewDataService)
    {
        $this->model = Ticket::class;
        $this->controller = collect([
            '0' => 'ticket', // Use a string for the controller name
            '1' => 'Ticket',
        ]);
        $this->tableViewDataService = $tableViewDataService;
    }
    public function index()
    {
        $user = Auth::user();
        
        if($user->hasRole('Tenant')){
            $tickets = $user->tickets;
        } else{
            $tickets = Ticket::all();

        }
      
        $mainfilter =  Ticket::pluck('category')->toArray();
        //   $filterData = $this->filterData($this->model);
        $controller = $this->controller;
        $tableData = $this->tableViewDataService->getTicketData($tickets, false);

        return View(
            'admin.CRUD.form',
            compact('mainfilter', 'tableData', 'controller'),
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
    public function create($id = null)
    {
        $property = Property::find($id);
        $properties = Property::all();
        $viewData = $this->formData($this->model);

        Session::flash('previousUrl', request()->server('HTTP_REFERER'));

        return View('admin.maintenance.create_request', compact('id', 'property', 'properties'), $viewData);
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
        $requestData = new Ticket;
        $requestData->fill($validatedData);
        $requestData->status = 'New';
        $requestData->user_id = $user->id;
        $requestData->save();


        ///Create Notification for to the User/Tenant

        $previousUrl = Session::get('previousUrl');
        if($previousUrl){
        return redirect($previousUrl)->with('status', 'Your request has been sent successfully');
        }else{
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
        $modelrequests = Ticket::find($id);
        //   $unit->load('property', 'unitSupervisors');
        $pageheadings = collect([
            '0' => $modelrequests->category,
            '1' => $modelrequests->property->property_name,
            '2' => $modelrequests->subject,
        ]);
        $viewData = $this->formData($this->model, $modelrequests);

        ///Data for Summary page

        //   $requestTableData = $this->tableViewDataService->getTicketData($modelrequests);

        $tabTitles = collect([
            'Summary',
            'Work Order',
            'Expenses'
        ]);

        $tabContents = [];
        foreach ($tabTitles as $title) {
            if ($title === 'Summary') {
                $tabContents[] = View('admin.maintenance.summary_request', $viewData, compact('modelrequests'))->render();
            } elseif ($title === 'Work Order') {
                $tabContents[] = View('admin.maintenance.workorder', compact('modelrequests'))->render();
            } elseif ($title === 'Expenses') {
                $tabContents[] = View('admin.maintenance.workorder', compact('modelrequests'))->render();
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

        return View('admin.maintenance.assign', compact('vendorcategories', 'modelrequests', 'vendors','users'));
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
        $validatedData = $request->validate([
            'assigned_type' => 'required',
            'assigned_id' => 'required',
           
        ]);
       // dd($request->all());
        $modelrequests = Ticket::find($id);
        $modelrequests->fill($validatedData);
        $modelrequests->status = 'Assigned';
        $modelrequests->update();

        return redirect($this->controller['0'])->with('status', $this->controller['1'] . ' Edited Successfully');
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
