<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\WebsiteSetting;
use Illuminate\Http\Request;
use App\Services\FilterService;
use App\Services\TableViewDataService;
use Carbon\Carbon;

class TransactionController extends Controller
{

    private $filterService;
    private $tableViewDataService;
    protected $controller;
    protected $model;


    public function __construct(FilterService $filterService, TableViewDataService $tableViewDataService)
    {

        $this->model = Transaction::class;
        $this->controller = collect([
            '0' => 'transaction', // Use a string for the controller name
            '1' => ' Transaction',
        ]);
        $this->filterService = $filterService;
        $this->tableViewDataService = $tableViewDataService;
    }

    public function index()
    {


        return View('admin.Accounting.transaction');

    }

  

   
    public function create()
    {
        return View('admin.Lease.transaction');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        dd('reached');
    }
}
