<?php

namespace App\Http\Controllers;

use App\Models\Tax;
use Illuminate\Http\Request;
use App\Traits\FormDataTrait;
use App\Services\TableViewDataService;

class TaxController extends Controller
{
    use FormDataTrait;
    protected $controller;
    protected $model;
    private $tableViewDataService;

    public function __construct(TableViewDataService $tableViewDataService)
    {
        $this->model = Tax::class;
        $this->controller = collect([
            '0' => 'tax', // Use a string for the controller name
            '1' => 'Tax',
        ]);

        $this->tableViewDataService = $tableViewDataService;
    }

    public function index(Request $request)
    {
        $filters = $request->except(['tab','_token','_method']);
        $taxes = Tax::with('propertyType')->showSoftDeleted()->ApplyFilters($filters)->get();
        //  $filterData = $this->filterData($this->model);
        $controller = $this->controller;
        $tableData = $this->tableViewDataService->getTaxData($taxes, false);

        return View('admin.CRUD.form', compact('tableData', 'controller'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $viewData = $this->formData($this->model);

        return View('admin.CRUD.form', $viewData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validationRules = Tax::$validation;
        $validatedData = $request->validate($validationRules);
        $tax = new Tax();
        $tax->fill($validatedData);
        $tax->save();

        return redirect('tax')->with('status', 'Tax Added Successfully');
    }
}
