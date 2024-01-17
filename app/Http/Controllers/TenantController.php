<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Unit;
use App\Models\Property;
use Spatie\Permission\Models\Role;
use App\Traits\FormDataTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Services\TableViewDataService;

class TenantController extends Controller
{
    use FormDataTrait;
    protected $controller;
    protected $model;
    private $tableViewDataService;

    public function __construct(TableViewDataService $tableViewDataService)
    {
        $this->model = User::class;
        $this->controller = collect([
            '0' => 'user', // Use a string for the controller name
            '1' => 'Tenant',
        ]);
        $this->tableViewDataService = $tableViewDataService;
    }

    public function index()
    {
        $user = Auth::user();
        if (Gate::allows('view-all', $user)) {
            $tenants =User::role('tenant')->get();
        } else {
            $tenants = User::Tenants($user)->get();
          //  $tablevalues = $user->filterUsers();
        }
        $mainfilter =  User::pluck('email')->toArray();
        $filterData = $this->filterData($this->model);
        $controller = $this->controller;
        $tableData = $this->tableViewDataService->getUserData($tenants,true);
        
        return View('admin.CRUD.form', compact('mainfilter', 'tableData', 'controller'),
        $filterData,
        [
         //   'cardData' => $cardData,
        ]);
    }

}
