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
    }

    public function ledger(Request $request)
    {

        $headers = ['DATE', 'ACCOUNT', 'PROPERTY', 'DESCRIPTION', 'DEBIT', 'CREDIT', 'BALANCE'];
        $filterdata = $this->filterService->getGeneralLedgerFilters();
        $filters = $request->except(['tab', '_token', '_method']);
        $transactions = $this->model::with('creditAccount', 'debitAccount')->applyFilters($filters)->get();

        // Calculate running balance
        $balance = 0;
        $generalLedgerEntries = [];

        foreach ($transactions as $transaction) {
            // Determine if the account is a Debit or Credit based on account type
            
            $creditAccountType = $transaction->creditAccount->account_type;
            $debitAccountType = $transaction->debitAccount->account_type;

            // Initialize debit and credit amounts
            $debit = null;
            $credit = null;

            // Determine debit or credit based on account types
            if ($creditAccountType === 'Liability' || $creditAccountType === 'Income' || $creditAccountType === 'Expenses') {
                $credit = $transaction->amount;
            } elseif ($debitAccountType === 'Asset') {
                $debit = $transaction->amount;
            }
            // Update balance based on Debit or Credit
            if ($debit !== null) {
                $balance += $transaction->amount;
            } elseif ($credit !== null) {
                $balance -= $transaction->amount;
            }

            $model = $transaction->transactionable;
            $modelName = strtolower(class_basename($model));
            $referenceno  = $model->id . '-' . $model->referenceno;
            $link = url($modelName, ['id' => $model->id]);
            // Debit entry
            $entry = [
                'date' => $transaction->created_at->format('Y-m-d'),
                'description' => $transaction->property->property_name . ' - ' . $transaction->units->unit_number,
                'account' => $transaction->description,
                'charge_name' => $transaction->charge_name . ' -  <a href="' . $link . '">' . $modelName . '-' . $referenceno . '</a> ',
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
        $filterdata = $this->filterService->getIncomeStatementFilters();
        $filters = $request->except(['tab', '_token', '_method']);
        
       // $threeMonths = now()->subMonths(3);
        $incomeQuery = Transaction::whereHas('creditAccount', function ($query) {
            $query->whereBetween('account_number', [40000, 50000]);
        });

        $expenseQuery = Transaction::whereHas('creditAccount', function ($query) {
            $query->whereBetween('account_number', [90000, 100000]);
        });

       
        $incomeTransactions = $incomeQuery
            ->selectRaw('creditaccount_id, sum(amount) as total, MAX(description) as description, DATE_FORMAT(created_at, "%M %Y") as month')
            ->where("created_at", ">", Carbon::now()->subMonths(6))
            ->groupByRaw('creditaccount_id, month')
            ->orderBy('creditaccount_id')
            ->applyFilters($filters)->get();

        $expenseTransactions = $expenseQuery
            ->selectRaw('creditaccount_id, sum(amount) as total, MAX(description) as description, DATE_FORMAT(created_at, "%M %Y") as month')
            ->where("created_at", ">", Carbon::now()->subMonths(6))
            ->groupByRaw('creditaccount_id, month')
            ->orderBy('creditaccount_id')
            ->applyFilters($filters)->get();

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
