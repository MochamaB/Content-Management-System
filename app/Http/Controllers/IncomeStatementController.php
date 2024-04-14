<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\WebsiteSetting;
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
        $sitesettings = WebsiteSetting::all();
        $filterdata = $this->filterService->getIncomeStatementFilters();
        $filters = $request->except(['tab', '_token', '_method']);
        
       // $threeMonths = now()->subMonths(3);
        $incomeQuery = Transaction::whereHas('creditAccount', function ($query) {
            $query->whereBetween('account_number', [40000, 50000]);
        });

        $expenseQuery = Transaction::whereHas('creditAccount', function ($query) {
            $query->whereBetween('account_number', [31000, 32000]);
        });

      //  dd($expenseQuery);
       
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

            $months = $incomeTransactions->concat($expenseTransactions)->pluck('month')->unique()->sortBy(function ($date) {
                return Carbon::parse($date)->timestamp;
            })->values();
            

        return View(
            'admin.Accounting.accounting',
            compact('filterdata', 'incomeTransactions', 'expenseTransactions', 'sitesettings', 'months')
        );
    }
}
