<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


class PermissionController extends Controller
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
             '0' => 'permission', // Use a string for the controller name
             '1' => ' Permission',
         ]);
     }
    public function index()
    {
        $tablevalues = Permission::with('roles')->orderBy('name', 'ASC')->get();
        $mainfilter = Permission::pluck('name')->map(function ($name) {
            return explode('.', $name)[0];
        })->unique()->toArray();
        $controller = $this->controller;
        /// TABLE DATA ///////////////////////////
        $tableData = [
            'headers' => ['PERMISSION','MODULE', 'ROLES ASSIGNED','ACTIONS'],
            'rows' => [],
        ];

        foreach ($tablevalues as $item) {
            $showLink = url($this->controller['0'] . 'show' . $item->id);
            $rolesHtml = '';
            foreach ($item->roles as $key => $role) {
                $rolesHtml .= '<li class=""> ' . ($key + 1) . '. ' . $role->name . '</li>';
            }
            $isDeleted = $item->deleted_at !== null;
            $tableData['rows'][] = [
                'id' => $item->id,
                $item->name,
                $item->module,
                 $rolesHtml,
                'isDeleted' => $isDeleted,

            ];
        }
    

        return View('admin.CRUD.form', compact('mainfilter', 'tableData', 'controller'));
}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       
        $module =  Permission::pluck('module')->unique()->toArray();
        $submodule =  Permission::pluck('submodule')->unique()->toArray();
        $action =  Permission::pluck('action')->unique()->toArray();

        return View('admin.Setting.permission',compact('module','submodule','action'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(Permission::where('name',$request->get('name'))->exists()){
            return redirect()->route('permission.index')
            ->with('statuserror','Permission is already in the system');
        }

        Permission::create([
            'name' => $request->input('name'),
            'model' => $request->input('model'),
            'submodule' => $request->input('submodule'),
            'action' => $request->input('action'), // Add the 'model' field
        ]);

        return redirect('permission')
            ->with('status','Permission Added Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function show(Permission $permission)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function edit(Permission $permission)
    {
        $module =  Permission::pluck('module')->unique()->toArray();
        $submodule =  Permission::pluck('submodule')->unique()->toArray();
        $action =  Permission::pluck('action')->unique()->toArray();
        return view('admin.Setting.permission', [
            'permission' => $permission,
            'module' =>$module,
            'submodule' =>$submodule,
            'action' =>$action
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name,'.$permission->id
        ]);

        $permission->update($request->all());

        return redirect('permission')
        ->with('status','Permission Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function destroy(Permission $permission)
    {
        $permission->roles()->detach();

        // Delete the permission
        $permission->delete();

        return redirect('permission')
        ->with('status','Permission Deleted Successfully');

    }
}
