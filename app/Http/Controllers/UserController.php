<?php

namespace App\Http\Controllers;

use App\Events\UserCreate;
use App\Models\User;
use App\Models\Unit;
use App\Models\Property;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Traits\FormDataTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\Auth\StoreUserRequest;
use App\Http\Requests\Auth\UpdateUserRequest;
use App\Mail\WelcomeMail;
use App\Notifications\UserCreatedNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use App\Notifications\UserDeletedNotification;
use App\Actions\UserRoleAction;
use App\Actions\AttachDetachUserFromUnitAction;
use App\Actions\UploadMediaAction;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\TableViewDataService;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    use FormDataTrait;
    protected $controller;
    protected $model;
    private $userRoleAction;
    protected $attachDetachUserFromUnitAction;
    protected $uploadMediaAction;
    private $tableViewDataService;

    public function __construct(
        UserRoleAction $userRoleAction,
        AttachDetachUserFromUnitAction $attachDetachUserFromUnitAction,
        UploadMediaAction $uploadMediaAction,
        TableViewDataService $tableViewDataService
    ) {
        $this->model = User::class;
        $this->controller = collect([
            '0' => 'user', // Use a string for the controller name
            '1' => 'User',
        ]);

        $this->userRoleAction = $userRoleAction;
        $this->attachDetachUserFromUnitAction = $attachDetachUserFromUnitAction;
        $this->uploadMediaAction = $uploadMediaAction;
        $this->tableViewDataService = $tableViewDataService;
    }


    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (Gate::allows('view-all', $user)) {
        
                // Superadmin can see all users
                $users = $this->model::all();
          
        }else if(Gate::allows('admin', $user))  {
            $users = $this->model::where('id', '<>', 1)
            ->where('id', '<>', $user->id)->get();
        }else {
            $users = $user->filterUsers();
        }
        $mainfilter =  Role::pluck('name')->toArray();
        $filterData = $this->filterData($this->model);
        $controller = $this->controller;
        $tableData = $this->tableViewDataService->getUserData($users, false);
        // $userviewData = compact('tableData', 'mainfilter', 'controller');

        return View('admin.CRUD.form', compact('mainfilter', 'tableData', 'controller'), $filterData);
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $userRole = $request->session()->get('userRole');
        $user = $request->session()->get('user');
        $savedRole = Role::where('id', $userRole)->first();

        $loggeduser = Auth::user();
        $loggeduserRoles = $loggeduser->roles;
        $loggedUserPermissions = $loggeduserRoles->flatMap(function ($role) {
            return $role->permissions;
        });
        // Retrieve all roles and their associated permissions
        $allRoles = Role::with('permissions')->get();
        // Filter roles with lesser permissions
        $filteredRoles = $allRoles->filter(function ($role) use ($loggedUserPermissions) {
            $rolePermissions = $role->permissions;

            // Compare the number of permissions in the role with the logged-in user's permissions
            return $rolePermissions->count() < $loggedUserPermissions->count();
        });
        $propertyaccess = Property::with('units')->get();
        $assignedUnits = User::role($userRole)->with('units')->get()->pluck('units')->flatten()->pluck('id')->toArray();

        if (Gate::allows('view-all', $loggeduser)) {
            $roles = Role::all();
        }else if (Gate::allows('admin', $loggeduser)){
            $roles = $filteredRoles->reject(function ($role) {
                return $role->id === 1;
            })->all();
        }else {
            $roles =  $filteredRoles->all();
        }

        $steps = collect([
            'Roles',
            'Contact Information',
            'Property Access',
            'Review Details',
        ]);
        $activetab = $request->query('active_tab', '0');
        $stepContents = [];
        foreach ($steps as $title) {
            if ($title === 'Roles') {
                $stepContents[] = View('admin.User.user_roles', compact('roles', 'savedRole'))->render();
            } elseif ($title === 'Contact Information') {
                $stepContents[] = View('admin.User.user_contactinfo', compact('user'))->render();
            } elseif ($title === 'Property Access') {
                $stepContents[] = View('admin.User.user_property', compact('propertyaccess', 'savedRole', 'assignedUnits'))->render();
            } elseif ($title === 'Review Details') {
                $stepContents[] = View('admin.User.user_reviewdetails', compact('propertyaccess', 'savedRole', 'assignedUnits'))->render();
            }
        }

        return View('admin.User.user', compact('steps', 'stepContents', 'activetab'));
    }

    public function roleuser(Request $request)
    {
        if (empty($request->session()->get('userRole'))) {
            $userRole = $request->role;
            $request->session()->put('userRole', $userRole);
        } else {
            $userRole = $request->session()->get('userRole');
            $role = $request->role;
            $request->session()->put('userRole', $role);
        }
        return redirect()->route('user.create', ['active_tab' => '1'])
            ->with('status', 'Role Picked Successfully. Enter user details');
    }

    public function userinfo(StoreUserRequest $request)
    {

        $validatedData = $request->validated();

        if (empty($request->session()->get('user'))) {
            $user = new User();
            $user->fill($validatedData);
            $request->session()->put('user', $user);
        } else {
            $user = $request->session()->get('user');
            $user->fill($validatedData);
            $request->session()->put('user', $user);
        }
        $role = $request->session()->get('userRole');
        $rolename = Role::find($role);
        if ($rolename->name === 'Tenant') {
            return redirect()->route('user.create', ['active_tab' => '3'])
                ->with('status', 'Properties assigned Successfully. Review and confirm');
        } else {

            return redirect()->route('user.create', ['active_tab' => '2'])
                ->with('status', 'User details added Successfully. Assign properties');
        }
    }

    public function assignProperties(Request $request)
    {

        $unitIds = $request->input('unit_id', []);
        $properties = [];

        foreach ($unitIds as $unitId => $selected) {
            if ($selected) {
                // Retrieve the corresponding property_id from the hidden field
                $propertyId = $request->input("property_id.{$unitId}");

                // Store the unit and property IDs
                $properties[] = ['unit_id' => $unitId, 'property_id' => $propertyId];
            }
        }

        $request->session()->put('properties', $properties);

        return redirect()->route('user.create', ['active_tab' => '3'])
            ->with('status', 'Properties assigned Successfully. Review and confirm');
    }



    public function store(Request $request)
    {

        // 1. GET USER INFO FROM WIZARD SESSION 
        $newuser = $request->session()->get('user')->toArray();

        // 2. SAVE NEW USER
        $user = new User();
        $user->fill($newuser);
        $user->password = 'property123';
        $user->save();
        $this->uploadMediaAction->handle($user, 'profilepicture', 'avatar', $request);

        // 3. GET USER ROLE FROM SESSION AND ASSIGN NEW USER////
        $role = $request->session()->get('userRole');
        $this->userRoleAction->assignRole($user, $role);

        //4. GET ASSIGNED UNITS FROM SESSION AND ASSIGN IN PIVOT UNIT_USER
        $properties = $request->session()->get('properties');
        if ($properties) {
            foreach ($properties as $property) {
                $user->units()->attach($property['unit_id'], ['property_id' => $property['property_id']]);
            }
        }
        //5. FORGET SESSION DATA
        $request->session()->forget('userRole');
        $request->session()->forget('user');
        $request->session()->forget('properties');

        //6. SEND NEW USER A WELCOME EMAIL
        try {
            $user->notify(new UserCreatedNotification($user)); ///// Send welcome Email
               } catch (\Exception $e) {
            // Log the error or perform any necessary actions
                   Log::error('Failed to send payment notification: ' . $e->getMessage());
               }
     


        return redirect('user')->with('status', 'User Added Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show($user)
    {
        
        $showUser = User::with('units.invoices')->findOrFail($user);
        //1. GET PAGE HEADINGS FOR SHOW PAGE
        $pageheadings = collect([
            '0' => $showUser->email,
            '1' => $showUser->firstname,
            '2' => $showUser->lastname,
        ]);

        //2. GET THE TITLES FOR THE TABS
        $tabTitles = collect([
            'Invoices',
            'Payments',
            //    'Maintenance',
            //    'Financials',
            //    'Users',
            //    'Invoices',
            //    'Payments'
            // Add more tab titles as needed
        ]);

        /// DATA FOR INVOICES TAB
        $invoices = $showUser->units->flatMap(function ($unit) {
            return $unit->invoices;
        });
        $invoiceTableData = $this->tableViewDataService->getInvoiceData($invoices);

        /// DATA FOR PAYMENTS TAB
        $payments = $showUser->units->flatMap(function ($unit) {
            return $unit->payments;
        });
        $paymentTableData = $this->tableViewDataService->getPaymentData($payments);

        //3. LOAD THE PAGES FOR THE TABS
        $tabContents = [];
        foreach ($tabTitles as $title) {
            if ($title === 'Invoices') {
                $tabContents[] = View('admin.User.user_tabs', ['tableData' => $invoiceTableData, 'controller' => ['invoice']])->render();
            } elseif ($title === 'Payments') {
                $tabContents[] = View('admin.User.user_tabs', ['tableData' => $paymentTableData, 'controller' => ['payment']])->render();
            } elseif ($title === 'Vouchers') {
                $tabContents[] = View('admin.User.test')->render();
            }
        }

        return View('admin.User.user_profile', compact('pageheadings', 'tabTitles', 'tabContents', 'showUser'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $editUser = $user;
        $roles = Role::all();
        $userRole = $editUser->roles->pluck('name');
        $assignedproperties = $editUser->units->pluck('id')->toArray();
       
        //// Role of user being edited
        //   $savedRole = Role::where('id', $userRole)->first();

        $pageheadings = collect([
            '0' => $editUser->email,
            '1' => $editUser->firstname,
            '2' => $editUser->lastname,
        ]);
        $propertyaccess = Property::with('units')->get();
        $assignedUnits = User::role($userRole)->with('units')->get()->pluck('units')->flatten()->pluck('id')->toArray();
        // dd($assignedUnits);
        $tabTitles = collect([
            'Contact Information',
            'Roles',
            'Login Access',
            'Property Access',
            //    'Financials',
            //    'Users',
            //    'Invoices',
            //    'Payments'
            // Add more tab titles as needed
        ]);
        $tabContents = [];
        foreach ($tabTitles as $title) {
            if ($title === 'Roles') {
                $tabContents[] = View('admin.User.user_roles', compact('roles', 'editUser', 'userRole'))->render();
            } elseif ($title === 'Contact Information') {
                $tabContents[] = View('admin.User.user_contactinfo', compact('editUser'))->render();
            } elseif ($title === 'Login Access') {
                $tabContents[] = View('admin.User.user_logins', compact('editUser'))->render();
            } elseif ($title === 'Property Access') {
                $tabContents[] = View('admin.User.user_property', compact('userRole', 'propertyaccess', 'assignedproperties', 'assignedUnits'))->render();
            }
        }

        return View('admin.User.user', compact('pageheadings', 'tabTitles', 'tabContents'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        // Get the list of fillable fields from the model


        $user->syncRoles($request->get('role'));

        $user->update($request->all());
        $this->uploadMediaAction->handle($user, 'profilepicture', 'avatar', $request);


        $unitIds = $request->input('unit_id', []);
        $this->attachDetachUserFromUnitAction->assignFromView($user, $unitIds, $request);



        return redirect($this->controller['0'])->with('status', $this->controller['1'] . ' Edited Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $role = $user->getRoleNames()->first();

        if ($user->id == 1) {
            return redirect()->back()->with('statuserror', 'Super Admin user cannot be deleted');
        }


        $user->units()->detach();   /////// Remove assigned units
        $user->removeRole($role);
        $user->delete();   //// Delete User          //////// Remove Role
        try {
            $user->notify(new UserDeletedNotification($user)); ////// Send Email for deletion.
               } catch (\Exception $e) {
            // Log the error or perform any necessary actions
                   Log::error('Failed to senduser deleted notification: ' . $e->getMessage());
               }
     

        return redirect()->back()->with('status', 'User deleted successfully.');
    }

    public function profpic(Request $request, $id)
    {
        $user = User::find($id);

        if ($request->file('profilepicture')) {
            $file = $request->file('profilepicture');
            $filename = date('YmdHi') . $file->getClientOriginalName();
            $file->move(base_path('resources/uploads/images'), $filename);
            $user['profilepicture'] = $filename;
        }
        $user->update();

        return back()->with('status', 'Profile picture updated successfully.');
    }


    ///////////user creation Wizard/////////
    public function role(Request $request)
    {
        if (empty($request->session()->get('role'))) {
            $role = $request->role;
            $request->session()->put('rentcharge', $role);
        } else {
            $role = $request->session()->get('role');
            $request->session()->put('role', $role);
        }
    }
}
