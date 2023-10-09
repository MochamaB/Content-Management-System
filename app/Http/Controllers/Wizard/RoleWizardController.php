<?php

namespace App\Http\Controllers\Wizard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;

class RoleWizardController extends Controller
{
    public function rolewizard(){

        $permissions = Permission::orderBy('name', 'asc')->get();

        // Group the permissions by module and then submodule
        $groupedPermissions = $permissions->groupBy('module')->map(function ($modulePermissions) {
            return $modulePermissions->groupBy('submodule');
        });
           
         $checkboxPermissions = ['create', 'destroy', 'edit','index','show','store','update'];
         $steps = collect([
            'Summary',
            'Menu Access',
            'Report Access',
        ]);
        $stepContents = [];
        foreach ($steps as $title) {
            if ($title === 'Summary') {
                $stepContents[] = View('wizard.role.role_summary')->render();
            } elseif ($title === 'Menu Access') {
                $stepContents[] = View('wizard.role.role_permissions',compact('groupedPermissions','checkboxPermissions'))->render();
            } elseif ($title === 'Report Access') {
                $stepContents[] = View('wizard.role.role_reports')->render();
            } 
        }
        return View('wizard.role.role',compact('steps','stepContents'));
    }
}
