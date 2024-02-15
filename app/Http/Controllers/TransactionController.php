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


    public function __construct(FilterService $filterService, TableViewDataService $tableViewDataService)
    {

        $this->filterService = $filterService;
        $this->tableViewDataService = $tableViewDataService;
    }

    public function index()
    {
    }

    public function ledger(Request $request)
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
        return View(
            'admin.Accounting.ledger',
            compact('filterdata'),
            ['tableData' => $transactionTableData, 'controller' => ['media']]
        );
    }

    public function incomeStatement(Request $request)
    {
        $sitesettings = WebsiteSetting::all();
        // $sixMonths = now()->subMonths(3);
        $incomeQuery = Transaction::whereHas('creditAccount', function ($query) {
            $query->whereBetween('account_number', [40000, 50000]);
        });
        $expenseQuery = Transaction::whereHas('creditAccount', function ($query) {
            $query->whereBetween('account_number', [40000, 50000]);
        });

        $filterdata = $this->filterService->getIncomeStatementFilters();
        $filters = request()->all();
        foreach ($filters as $column => $value) {
            if (!empty($value)) {
                $incomeQuery->where($column, $value);
                $expenseQuery->where($column, $value);
            }
        }
        $incomeTransactions = $incomeQuery->get();
        $groupedTransactions = $incomeTransactions->groupBy(function ($date) {
            return Carbon::parse($date->created_at)->format('Y-m');
        })->map(function ($groupedByDate) {
            return $groupedByDate->groupBy('creditaccount_id');
        });
        $expenseTransactions = $expenseQuery->get();
        // The total income
        $totalIncome = $incomeQuery->sum('amount');
        // The total expenses
        $totalExpenses = $expenseQuery->sum('amount');

        // The net profit or loss
        $netProfit = $totalIncome - $totalExpenses;


        //  $transactionTableData = $this->tableViewDataService->getincomeStatementData($incomeTransactions, false);
        return View(
            'admin.Accounting.ledger',
            compact('filterdata', 'incomeTransactions', 'expenseTransactions','totalIncome','totalExpenses','netProfit','sitesettings')
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
