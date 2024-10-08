<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Website;
use Illuminate\Http\Request;

use App\Services\FilterService;
use App\Services\TableViewDataService;
use Carbon\Carbon;

class IncomeStatementController extends Controller
{
    private $filterService;
    private $tableViewDataService;
    protected $controller;
    protected $model;

    public function __construct(FilterService $filterService, TableViewDataService $tableViewDataService)
    {

        $this->model = Transaction::class;
        $this->controller = collect([
            '0' => 'income-statement', // Use a string for the controller name
            '1' => ' Income-Statement',
        ]);
        $this->filterService = $filterService;
        $this->tableViewDataService = $tableViewDataService;
    }

    public function index(Request $request)
    {
        $sitesettings = Website::all();
        $filterdata = $this->filterService->getIncomeStatementFilters();
        $filters = $request->except(['tab', '_token', '_method']);

        // $threeMonths = now()->subMonths(3);
        $incomeQuery = Transaction::where(function ($query) {
            $query->whereHas('creditAccount', function ($subQuery) {
                $subQuery->whereBetween('account_number', [40000, 50000]);
            })->orWhereHas('debitAccount', function ($subQuery) {
                $subQuery->whereBetween('account_number', [40000, 50000]);
            });
        });

        $expenseQuery = Transaction::where(function ($query) {
            $query->whereHas('creditAccount', function ($subQuery) {
                $subQuery->whereBetween('account_number', [91000, 100000]);
            })->orWhereHas('debitAccount', function ($subQuery) {
                $subQuery->whereBetween('account_number', [91000, 100000]);
            });
        });

        $incomeTransactions = $incomeQuery
            ->selectRaw('IFNULL(creditaccount_id, debitaccount_id) as account_id, sum(amount) as total, MAX(description) as description, DATE_FORMAT(created_at, "%M %Y") as month')
            ->where("created_at", ">", Carbon::now()->subMonths(6))
            ->groupByRaw('account_id, month')
            ->orderBy('account_id')
            ->applyFilters($filters)
            ->get();

        $expenseTransactions = $expenseQuery
            ->selectRaw('IFNULL(creditaccount_id, debitaccount_id) as account_id, sum(amount) as total, MAX(description) as description, DATE_FORMAT(created_at, "%M %Y") as month')
            ->where("created_at", ">", Carbon::now()->subMonths(6))
            ->groupByRaw('account_id, month')
            ->orderBy('account_id')
            ->applyFilters($filters)
            ->get();

        $months = $incomeTransactions->concat($expenseTransactions)
            ->pluck('month')
            ->unique()
            ->sortBy(function ($date) {
                return Carbon::parse($date)->timestamp;
            })
            ->values();



        return View(
            'admin.Accounting.accounting',
            compact('filterdata', 'incomeTransactions', 'expenseTransactions', 'sitesettings', 'months')
        );
    }
}
