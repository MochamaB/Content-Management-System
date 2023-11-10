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

    public function __construct(UserRoleAction $userRoleAction,AttachDetachUserFromUnitAction $attachDetachUserFromUnitAction)
    {
        $this->model = User::class;
        $this->controller = collect([
            '0' => 'user', // Use a string for the controller name
            '1' => 'User',
        ]);

        $this->userRoleAction = $userRoleAction;
        $this->attachDetachUserFromUnitAction = $attachDetachUserFromUnitAction;
    }


    public function index()
    {
        $user = Auth::user();
        if (Gate::allows('view-all', $user)) {
            $tablevalues = $this->model::all();
        } else {
            $tablevalues = $user->filterUsers();
        }
        $mainfilter =  Role::pluck('name')->toArray();
        $viewData = $this->formData($this->model);
        $controller = $this->controller;
        /// TABLE DATA ///////////////////////////
        $tableData = [
            'headers' => ['NAME', 'ROLE', 'EMAIL', 'ACCOUNT STATUS', 'ACTIONS'],
            'rows' => [],
        ];

        foreach ($tablevalues as $item) {
            $showLink = url($this->controller['0'] . 'show' . $item->id);
            $roleNames = $item->roles->pluck('name')->implode(', ');

            $tableData['rows'][] = [
                'id' => $item->id,
                $item->firstname . ' ' . $item->lastname,
                $roleNames ?? 'Super Admin',
                $item->email,
                $item->status,
            ];
        }
        $userviewData = compact('tableData', 'mainfilter', 'viewData', 'controller');

        return View('admin.CRUD.form', compact('mainfilter', 'tableData', 'controller'), $viewData, $userviewData);
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
        if (Gate::allows('view-all', $loggeduser)) {
            $roles = Role::all();
        } else {
            $roles =  $filteredRoles->all();
        }

        $steps = collect([
            'Roles',
            'Contact Information',
            'Property Access',
        ]);
        $activetab = $request->query('active_tab', '0');
        $stepContents = [];
        foreach ($steps as $title) {
            if ($title === 'Roles') {
                $stepContents[] = View('admin.user.user_roles', compact('roles', 'savedRole'))->render();
            } elseif ($title === 'Contact Information') {
                $stepContents[] = View('admin.user.user_contactinfo', compact('user'))->render();
            } elseif ($title === 'Property Access') {
                $stepContents[] = View('admin.user.user_property', compact('propertyaccess', 'savedRole'))->render();
            }
        }

        return View('admin.user.user', compact('steps', 'stepContents', 'activetab'));
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

        return redirect()->route('user.create', ['active_tab' => '2'])
            ->with('status', 'User details added Successfully. Assign properties');
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

        // 3. GET USER ROLE FROM SESSION AND ASSIGN NEW USER////
        $role = $request->session()->get('userRole');
        $this->userRoleAction->assignRole($user, $role);

        //4. GET ASSIGNED UNITS FROM CHECKBOXES AND ASSIGN IN PIVOT UNIT_USER
        $unitIds = $request->input('unit_id', []);
        foreach ($unitIds as $unitId => $selected) {
            if ($selected) {
                // Retrieve the corresponding property_id from the hidden field
                $propertyId = $request->input("property_id.{$unitId}");

                // Attach the unit to the user with the associated property_id
                $user->units()->attach($unitId, ['property_id' => $propertyId]);
            }
        }

        //5. FORGET SESSION DATA
        $request->session()->forget('userRole');
        $request->session()->forget('user');

        //6. SEND NEW USER A WELCOME EMAIL
        $user->notify(new UserCreatedNotification($user)); ///// Send welcome Email


        return redirect('user')->with('status', 'User Added Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //1. GET PAGE HEADINGS FOR SHOW PAGE
        $pageheadings = collect([
            '0' => $user->email,
            '1' => $user->firstname,
            '2' => $user->lastname,
        ]);

        //2. GET THE TITLES FOR THE TABS
        $tabTitles = collect([
            'Profile',
            'Invoices',
            'Payments',
            //    'Maintenance',
            //    'Financials',
            //    'Users',
            //    'Invoices',
            //    'Payments'
            // Add more tab titles as needed
        ]);

        //3. LOAD THE PAGES FOR THE TABS
        $tabContents = [];
        foreach ($tabTitles as $title) {
            if ($title === 'Profile') {
                $tabContents[] = View('admin.user.user_profile')->render();
            } elseif ($title === 'Invoices') {
                $tabContents[] = View('admin.user.user_profile')->render();
            } elseif ($title === 'Payments') {
                $tabContents[] = View('admin.user.user_profile')->render();
            }
        }

        return View('admin.CRUD.form', compact('pageheadings', 'tabTitles', 'tabContents'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $loggeduser = Auth::user();
        $roles = Role::all();
        $userRole = $user->roles->pluck('name');
        $assignedproperties = $user->units->pluck('id')->toArray();
        $pageheadings = collect([
            '0' => $user->email,
            '1' => $user->firstname,
            '2' => $user->lastname,
        ]);
        $propertyaccess = Property::with('units')->get();
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
                $tabContents[] = View('admin.user.user_roles', compact('roles', 'user', 'userRole'))->render();
            } elseif ($title === 'Contact Information') {
                $tabContents[] = View('admin.user.user_contactinfo', compact('user'))->render();
            } elseif ($title === 'Login Access') {
                $tabContents[] = View('admin.user.user_logins', compact('user'))->render();
            } elseif ($title === 'Property Access') {
                $tabContents[] = View('admin.user.user_property', compact('propertyaccess', 'assignedproperties'))->render();
            }
        }

        return View('admin.user.user', compact('pageheadings', 'tabTitles', 'tabContents', 'user'));
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
        if ($request->file('profilepicture')) {
            $file = $request->file('profilepicture');
            $filename = date('YmdHi') . $file->getClientOriginalName();
            $file->move(base_path('resources/uploads/images'), $filename);
            $user['profilepicture'] = $filename;
            $user->update();
        }
        $user->syncRoles($request->get('role'));

        $user->update($request->all());

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
        $user->notify(new UserDeletedNotification($user)); ////// Send Email for deletion.

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
