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
            'admin.Accounting.accounting',
            compact('filterdata'),
            ['tableData' => $transactionTableData, 'controller' => ['media']]
        );
    }

    public function incomeStatement(Request $request)
    {
        $sitesettings = WebsiteSetting::all();
        $threeMonths = now()->subMonths(3);
        $incomeQuery = Transaction::whereHas('creditAccount', function ($query) {
            $query->whereBetween('account_number', [40000, 50000]);
        })
        ;
    
        $expenseQuery = Transaction::whereHas('creditAccount', function ($query) {
            $query->whereBetween('account_number', [90000, 100000]);
        });

        $filterdata = $this->filterService->getIncomeStatementFilters();
        $filters = request()->all();
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        foreach ($filters as $column => $value) {
            if (!empty($value)) {
                // Check if the column is from-date or to-date
                if ($column == 'from_date' || $column == 'to_date') {
                    // Use whereBetween on the created-at column with the date range
                    $incomeQuery->whereBetween('created_at', [$from_date, $to_date]);
                    $expenseQuery->whereBetween('created_at', [$from_date, $to_date]);
                } else {
                    // Use where on the other columns
                    $incomeQuery->where($column, $value);
                    $expenseQuery->where($column, $value);
                }
            }
        }
        $incomeTransactions = $incomeQuery
        ->selectRaw('creditaccount_id, sum(amount) as total, MAX(description) as description, DATE_FORMAT(created_at, "%M %Y") as month')
        ->where("created_at", ">", Carbon::now()->subMonths(6))
        ->groupByRaw('creditaccount_id, month')
        ->orderBy('creditaccount_id')->get();

        $expenseTransactions = $expenseQuery
        ->selectRaw('creditaccount_id, sum(amount) as total, MAX(description) as description, DATE_FORMAT(created_at, "%M %Y") as month')
        ->where("created_at", ">", Carbon::now()->subMonths(6))
        ->groupByRaw('creditaccount_id, month')
        ->orderBy('creditaccount_id')->get();

        $months = $incomeTransactions->pluck('month')->unique()->sortBy(function ($date) {
            return Carbon::parse($date)->timestamp;
        })->values();
        // The total income
      //  $totalIncome = $incomeQuery->sum('amount');
        // The total expenses
      //  $totalExpenses = $expenseQuery->sum('amount');

        // The net profit or loss
   //     $netProfit = $totalIncome - $totalExpenses;


        //  $transactionTableData = $this->tableViewDataService->getincomeStatementData($incomeTransactions, false);
        return View(
            'admin.Accounting.accounting',
            compact('filterdata', 'incomeTransactions', 'expenseTransactions','sitesettings','months')
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
