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
        $query = Transaction::with('creditAccount', 'debitAccount');
        $headers = ['DATE', 'ACCOUNT', 'DESC', 'TYPE', 'DEBIT', 'CREDIT', 'BALANCE'];
        $filterdata = $this->filterService->getGeneralLedgerFilters();
        $filters = request()->all();
        $from_date = $request->from_date;
        $to_date =  $request->to_date;
        foreach ($filters as $column => $value) {
            if (!empty($value)) {
                if ($column == 'from_date' || $column == 'to_date') {
                    // Use whereBetween on the created-at column with the date range
                    $query->whereBetween('created_at', [$from_date, $to_date]);
                } else {

                $query->where($column, $value);
                }
            }
        }
        $transactions = $query
            ->where("created_at", ">", Carbon::now()->subMonths(6))
            ->orderBy('created_at', 'desc')
            ->get();
        // Calculate running balance
        $balance = 0;
        $generalLedgerEntries = [];

        foreach ($transactions as $transaction) {
            // Determine if the account is a Debit or Credit based on account type
            if ($transaction->creditAccount->account_type === 'Liability') {
                $debit = null;
                $credit = $transaction->amount;
            } elseif ($transaction->creditAccount->account_type === 'Income') {
                $debit = $transaction->amount;
                $credit = null;
            } elseif ($transaction->creditAccount->account_type === 'Expenses') {
                $debit = null;
                $credit = $transaction->amount;
            } elseif ($transaction->creditAccount->account_type === 'Asset') {
                $debit = null;
                $credit = $transaction->amount;
            }
            // Update balance based on Debit or Credit
            if ($debit !== null) {
                $balance += $transaction->amount;
            } elseif ($credit !== null) {
                $balance -= $transaction->amount;
            }

            // Debit entry
            $entry = [
                'date' => $transaction->created_at->format('Y-m-d'),
                'description' => $transaction->description,
                'account' => $transaction->debitAccount->account_name,
                'charge_name' => $transaction->charge_name,
                'debit' => $debit,
                'credit' => $credit,
                'balance' => $balance,
            ];

            $generalLedgerEntries[] = $entry;
        }

        //  dd($transactions);
        //  $transactionTableData = $this->tableViewDataService->getGeneralLedgerData($transactions, true);
        return View('admin.Accounting.accounting', compact('filterdata', 'headers', 'transactions'), ['generalLedgerEntries' => $generalLedgerEntries]);
    }

    public function incomeStatement(Request $request)
    {
        $sitesettings = WebsiteSetting::all();
        $threeMonths = now()->subMonths(3);
        $incomeQuery = Transaction::whereHas('creditAccount', function ($query) {
            $query->whereBetween('account_number', [40000, 50000]);
        });

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

        return View(
            'admin.Accounting.accounting',
            compact('filterdata', 'incomeTransactions', 'expenseTransactions', 'sitesettings', 'months')
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
