<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;


class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
   
    protected $controller;
    public function __construct()
    {
        $this->controller = collect([
            '0' => 'role', // Use a string for the controller name
            '1' => 'Role',
        ]);
    }


    public function index()
    {
                $tablevalues = Role::orderBy('id','DESC')->paginate(10);
                $mainfilter =  Role::pluck('name')->toArray();
                $controller = $this->controller;
                /// TABLE DATA ///////////////////////////
                $tableData = [
                    'headers' => ['ROLE', 'NUMBER OF USERS','ACTIONS'],
                    'rows' => [],
                ];

                foreach ($tablevalues as $item) {
                    $showLink = url($this->controller['0'] . 'show' . $item->id);
                    $userCount = $item->users->count();
                    $tableData['rows'][] = [
                        'id' => $item->id,
                         $item->name. '</br></br><span class="text-muted" style="font-weight:500;font-style: italic">'.
                        $item->description.'</span>',
                        $userCount ,
            
                    ];
                }
               
                return View('admin.CRUD.form', compact('mainfilter','tableData', 'controller'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permissions = Permission::orderBy('name', 'asc')->get();

        // Group the permissions by module and then submodule
        $groupedPermissions = $permissions->groupBy('module')->map(function ($modulePermissions) {
            return $modulePermissions->groupBy('submodule');
        });
           
         $checkboxPermissions = ['create', 'destroy', 'edit','index','show','store','update'];
         $tabTitles = collect([
            'Summary',
            'Menu Access',
            'Report Access',
        ]);
        $tabContents = [];
        foreach ($tabTitles as $title) {
            if ($title === 'Summary') {
                $tabContents[] = View('admin.user.role_summary')->render();
            } elseif ($title === 'Menu Access') {
                $tabContents[] = View('admin.user.role_permissions',compact('groupedPermissions','checkboxPermissions'))->render();
            } elseif ($title === 'Report Access') {
                $tabContents[] = View('admin.user.role_reports')->render();
            } 
        }
       
        return View('admin.user.role',compact('tabTitles','tabContents'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        if (Role::where('name', $request->get('name'))->exists()) {
            return redirect('role')->with('statuserror','Role is already in the system');
         }
      
        $role = Role::create(
            ['name' => $request->get('name'),
            'description' => $request->get('description'),
        'guard_name'=> 'web' 
            ]);

            
        $role->syncPermissions($request->get('permission'));
    
        return redirect('role')
                        ->with('status','Role created successfully');
    }

    public function assignpermissions(Request $request){

        $role->syncPermissions($request->get('permission'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $role)
    {
        $pageheadings = collect([
            '0' => $role->name,
            '1' => $role->guard_name,
            '2' => $role->description,
        ]);
        $user = Auth::user();
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        $permissions = Permission::orderby('name', 'ASC')->get();
        // Group the permissions by module and then submodule
        $groupedPermissions = $permissions->groupBy('module')->map(function ($modulePermissions) {
            return $modulePermissions->groupBy('submodule');
        });
           
         $checkboxPermissions = ['create', 'destroy', 'edit','index','show','store','update'];
        $tabTitles = collect([
            'Summary',
            'Menu Access',
            'Report Access',
        ]);
        $tabContents = [];
        foreach ($tabTitles as $title) {
            if ($title === 'Summary') {
                $tabContents[] = View('admin.user.role_summary', compact('role'))->render();
            } elseif ($title === 'Menu Access') {
                $tabContents[] = View('admin.user.role_permissions',compact('groupedPermissions','checkboxPermissions','rolePermissions'))->render();
            } elseif ($title === 'Report Access') {
                $tabContents[] = View('admin.user.role_reports',compact('role'))->render();
            } 
        }

        return View('admin.user.role', compact('pageheadings','tabTitles','tabContents','role'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        $model = Role::find($id);
        $model->update($request->all());
        $model->syncPermissions($request->get('permission'));
     
       
      
        return redirect($this->controller['0'])->with('status', $this->controller['1'] . ' Edited Successfully');
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        $rolePermissions = $role->permissions;
        if ($role->users()->count() > 0) {

            return redirect()->back()->with('statuserror', 'The role has active users. Delete the users before deleting this role.');
        }
        if($role->id == 1 ){
            return redirect()->back()->with('statuserror','Admin Role cannot be deleted');
        }
        // Detach the role from users
        $role->users()->detach();
        $role->revokePermissionTo($rolePermissions);
        $role->delete();
        
        return redirect()->back()->with('status','Role deleted successfully.');
    }
}
