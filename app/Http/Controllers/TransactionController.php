<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Services\FilterService;
use App\Services\TableViewDataService;

class TransactionController extends Controller
{

    private $filterService;
    private $tableViewDataService;


    public function __construct(FilterService $filterService ,TableViewDataService $tableViewDataService)
    {
       
        $this->filterService = $filterService;
        $this->tableViewDataService = $tableViewDataService;
       
    }

    public function index()
    {

    }

    public function ledger()
    {
        $query = Transaction::with('property', 'units');
        $filterdata = $this->filterService->getGeneralLedgerFilters();
        $filters = request()->all();
        foreach ($filters as $column => $value) {
            if (!empty($value)) {
                $query->where($column, $value);
            }
        }
        $transactions = $query->get();
       // dd($transactions);
        ///Data for utilities page
        $transactionTableData = $this->tableViewDataService->getGeneralLedgerData($transactions, true);
        return View('admin.Accounting.ledger', compact('filterdata'),
        ['tableData' => $transactionTableData,'controller' => ['media']]
    );
    }
    public function create()
    {
        return View('admin.lease.transaction');
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
