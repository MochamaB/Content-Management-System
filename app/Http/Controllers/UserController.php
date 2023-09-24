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

    public function __construct()
    {
        $this->model = User::class;
        $this->controller = collect([
            '0' => 'user', // Use a string for the controller name
            '1' => 'New user',
        ]);
    }

    public function index()
    {
        $user = Auth::user();
  


        if (Gate::allows('view-all', $user)) {
            $tablevalues = $this->model::all();
        }else{

            $tablevalues = $user->filterUsers();
          //  $tablevalues = $user->units; // Users with roles having lesser permissions
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
    public function create()
    {
        $user = Auth::user();
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

        if (Gate::allows('view-all', $user)) {
            $propertyaccess = Unit::viewallunits();
            $roles = Role::all();
        }else{
            $propertyaccess = $user->assignedunits();
            $roles =  $filteredRoles->all();
        }
        
        $tabTitles = collect([
            'Roles',
            'Contact Information',
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
                $tabContents[] = View('admin.user.user_roles', compact('roles'))->render();
            } elseif ($title === 'Contact Information') {
                $tabContents[] = View('admin.user.user_contactinfo')->render();
            } elseif ($title === 'Login Access') {
                $tabContents[] = View('admin.user.user_logins')->render();
            } elseif ($title === 'Property Access') {
                $tabContents[] = View('admin.user.user_property',compact('propertyaccess'))->render();
            }
        }

        return View('admin.user.user', compact('tabTitles','tabContents'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request)
    {
       

        $role = $request->role;
        $user =  User::create(array_merge($request->validated(), [
            'status' => 'Active',
            'profilepicture' => 'avatar.png',
        ]));
        //// assign role////
        $user->assignRole($role);
        
        $unitIds = $request->input('unit_id', []);  
        foreach ($unitIds as $unitId => $selected) {
            if ($selected) {
                // Retrieve the corresponding property_id from the hidden field
                $propertyId = $request->input("property_id.{$unitId}");
    
                // Attach the unit to the user with the associated property_id
                $user->units()->attach($unitId, ['property_id' => $propertyId]);
            }
        }
        $user->notify(new UserCreatedNotification($user)); ///// Send welcome Email


        return redirect('user')->with('status','User Added Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $pageheadings = collect([
            '0' => $user->email,
            '1' => $user->firstname,
            '2' => $user->lastname,
        ]);
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
        $userRole =$user->roles->pluck('name');
        $assignedproperties = $user->supervisedUnits->pluck('id')->toArray();
        $pageheadings = collect([
            '0' => $user->email,
            '1' => $user->firstname,
            '2' => $user->lastname,
        ]);

       
        if (Gate::allows('view-all', $loggeduser)) {  
            $propertyaccess = Unit::viewallunits();
        }else{   
            $propertyaccess = $loggeduser->assignedunits();           
        }
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
                $tabContents[] = View('admin.user.user_roles', compact('roles','user','userRole'))->render();
            } elseif ($title === 'Contact Information') {
                $tabContents[] = View('admin.user.user_contactinfo',compact('user'))->render();
            } elseif ($title === 'Login Access') {
                $tabContents[] = View('admin.user.user_logins',compact('user'))->render();
            } elseif ($title === 'Property Access') {
                $tabContents[] = View('admin.user.user_property',compact('propertyaccess','assignedproperties'))->render();
            }
        }

        return View('admin.user.user', compact('pageheadings','tabTitles','tabContents','user'));
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
        $model = User::find($id);
        // Get the list of fillable fields from the model
       if($request->file('profilepicture')){
            $file= $request->file('profilepicture');
            $filename= date('YmdHi').$file->getClientOriginalName();
            $file-> move(base_path('resources/uploads/images'), $filename);
            $model['profilepicture']= $filename;
            $model->update();
        }
            $model->syncRoles($request->get('role'));
        
      
            $model->update($request->all());
            $unitIds = $request->input('unit_id', []);
            $model->units()->detach();  
            foreach ($unitIds as $unitId => $selected) {
                if ($selected) {
                    // Retrieve the corresponding property_id from the hidden field
                    $propertyId = $request->input("property_id.{$unitId}");
        
                    // Attach the unit to the user with the associated property_id
                    $model->units()->attach($unitId, ['property_id' => $propertyId]);
                }
            }
         
                // Attach the relationship with pivot data
              //  dd($propertyId);
        //        $model->supervisedUnits()->sync($unitIds);
        
        
        
        
      
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
        
        if($user->id == 1 ){
            return redirect()->back()->with('statuserror','Super Admin user cannot be deleted');
        }
        
     
        $user->supervisedUnits()->detach();   /////// Remove assigned units
        $user->removeRole($role);  
        $user->delete();   //// Delete User          //////// Remove Role
        $user->notify(new UserDeletedNotification($user)); ////// Send Email for deletion.

    return redirect()->back()->with('status','User deleted successfully.');
    }

    public function profpic(Request $request, $id)
    {
        $user = User::find($id);

        if($request->file('profilepicture')){
            $file= $request->file('profilepicture');
            $filename= date('YmdHi').$file->getClientOriginalName();
            $file-> move(base_path('resources/uploads/images'), $filename);
            $user['profilepicture']= $filename;
              
        }
        $user->update();

        return back()->with('status','Profile picture updated successfully.');
    }
}
