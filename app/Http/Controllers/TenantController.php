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
use App\Services\FilterService;
use App\Services\CardService;

class TenantController extends Controller
{
    use FormDataTrait;
    protected $controller;
    protected $model;
    private $tableViewDataService;
    protected $filterService;
    private $cardService;

    public function __construct(TableViewDataService $tableViewDataService, FilterService $filterService, CardService $cardService)
    {
        $this->model = User::class;
        $this->controller = collect([
            '0' => 'user', // Use a string for the controller name
            '1' => 'Tenant',
        ]);
        $this->tableViewDataService = $tableViewDataService;
        $this->filterService = $filterService;
        $this->cardService = $cardService;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        if (Gate::allows('view-all', $user) || Gate::allows('admin', $user)) {
            $tenants =User::role('tenant')->get();
        } else {
            $tenants = User::Tenants($user)->get();
          //  $tablevalues = $user->filterUsers();
        }
        $filters = $request->except(['tab','_token','_method']);
        $filterData =  $this->filterService->getPropertyFilters($request);
        $controller = $this->controller;
        $tableData = $this->tableViewDataService->getUserData($tenants,true);
        
        return View('admin.CRUD.form', compact('filterData', 'tableData', 'controller'));
    }

}
