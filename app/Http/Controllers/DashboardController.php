<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Unit;
use App\Models\Property;
use App\Models\Lease;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PropertyType;
use App\Models\Tax;
use App\Models\Ticket;
use App\Services\TableViewDataService;
use App\Services\CardService;
use App\Services\FilterService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $controller;
    protected $model;
    private $tableViewDataService;
    private $cardService;
    private $filterService;

    public function __construct(TableViewDataService $tableViewDataService, CardService $cardService,
    FilterService $filterService)
    {
        $this->model = Unit::class;
        $this->controller = collect([
            '0' => 'dashboard', // Use a string for the controller name
            '1' => ' Dashboard',
        ]);

        $this->tableViewDataService = $tableViewDataService;
        $this->cardService = $cardService;
        $this->filterService = $filterService;
    }

    public function index(Request $request)
    {
        $tabTitles = collect([
            'Overview',
            'Properties',
            'Financials',
        ]);
        $tabIcons = collect([
            'Overview' => '', 
            'Properties' => '',
            'Financials' => '',
        ]);
        $controller = $this->controller;
        $user = auth()->user();
        $filters = $request->except(['tab','_token','_method']);
        $dashboardFilterData = $this->filterService->getDashboardFilters();
        $properties = Property::with('units', 'leases', 'invoices')->applyFilters($filters)->get();
        $units = Unit::with('property', 'lease', 'invoices','tickets')->get();



       ///1. Dashboard Tab
        if ($user && $user->id !== 1 && $user->roles->first()->name === "Tenant") {
            $cardData = $this->cardService->tenantTopCard($properties, $units, $filters);
        } else {
            $cardData = $this->cardService->topCard($properties, $units,$filters);
        }
            ///1.2 CHART DATA
            $chartData = $this->getInvoiceChartData($filters);
            //1.3 TICKET DATA ////
            $tickets = Ticket::latest()->take(3)->get();
            //1.4 TOTAL TAXES
            $taxSummary = $this->showTaxesWidget($filters);
            //1.5 PAYMENT TYPES
            $paymentType = $this->paymentType($filters);
        // dd($taxesByCategory);

        //2. Property Tab
        $propertyCard = $this->cardService->propertyCard($properties);

            $tabContents = [];
            foreach ($tabTitles as $title) {
                if ($title === 'Overview') {
                    $tabContents[] = View('admin.Dashboard.dashboardall',
                    compact('properties','cardData','chartData','tickets','taxSummary','paymentType'))->render();
                } elseif ($title === 'Properties') {
                    $tabContents[] = View('admin.Dashboard.dashboardproperties' ,compact('propertyCard'))->render();
                } elseif ($title === 'Financials') {
                    $tabContents[] = View('admin.Dashboard.dashboardfinancials', compact('properties'))->render();
                }
            }

        return View('admin.Dashboard.dashboard', compact('cardData', 'controller','tabTitles', 'tabContents','tabIcons','dashboardFilterData'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    private function getInvoiceChartData($filters)
    {
        // Collect all invoices from all properties
            $invoices = Invoice::selectRaw('DATE_FORMAT(created_at, "%b %Y") as month, SUM(totalamount) as totalamount')
            ->ApplyDateFilters($filters)
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();
        
            $payments = Payment::selectRaw('DATE_FORMAT(created_at, "%b %Y") as month, SUM(totalamount) as totalamount')
            ->ApplyDateFilters($filters)
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();
        return [
            'labels' => $invoices->pluck('month'),
            'firstLabel' => 'Invoices',
            'firstData' => $invoices->pluck('totalamount'),
            'secondLabel' => 'Payments',
            'secondData' => $payments->pluck('totalamount'),
            'firstTotal' => $invoices->sum('totalamount'),
            'secondTotal' => $payments->sum('totalamount'),
            'percentage' => $invoices->sum('totalamount') > 0
                            ? ($payments->sum('totalamount') / $invoices->sum('totalamount')) * 100
                            : 0
        ];
    }


public function showTaxesWidget($filters)
{
    // Fetch PropertyTypes with their properties and related payments
    $propertyTypes = PropertyType::with(['property.payments'])
        ->get()
        ->groupBy('property_category'); // Group by the property_category column

    $taxSummary = [];

    foreach ($propertyTypes as $category => $types) {
        $categorySummary = [
            'category' => $category,
            'taxes' => []
        ];

        $totalTaxAmount = 0;

        foreach ($types as $propertyType) {
            foreach ($propertyType->property as $property) {
                 // Apply the date filters to the payments and sum the tax amounts
                 $filteredPayments = $property->payments()->ApplyDateOnlyFilters($filters)->get();
                // Sum all tax amounts from the payments of the property
                $propertyTaxAmount = $filteredPayments->sum('taxamount');
                $totalTaxAmount += $propertyTaxAmount;
            }

            // Prepare a summary for each PropertyType within the category
            foreach ($propertyType->taxes as $tax) {
                $categorySummary['taxes'][] = [
                    'tax_name' => $tax->name ?? 'No Tax', // Tax name from the Tax model
                    'tax_amount' => $totalTaxAmount // Total tax amount for properties in this category
                ];
            }
        }

        $categorySummary['total_tax_amount'] = $totalTaxAmount;
        $taxSummary[] = $categorySummary;
    }

    // Pass the tax summary to the view
    return $taxSummary;
}
public function paymentType($filters)
{
    // Get the invoices and apply filters (e.g., date range)
    $invoices = Invoice::with('payments')->ApplyDateFilters($filters)->get();

    // Group the invoices by type and calculate the total payments for each type
    $invoiceSummary = $invoices->groupBy('name')->map(function ($group) {
        $totalPayments = $group->sum(function ($invoice) {
            return $invoice->payments->sum('totalamount'); // Assuming 'amount' is the payment column
        });

        return [
            'name' => $group->first()->name, // Get the invoice type from the first invoice in the group
            'total_payments' => $totalPayments
        ];
    })
    ->take(4); // Limit to 3 records

    return $invoiceSummary; // Pass this data to the view
}


    


    public function create()
    {
        //
    }

    public function cards()
    {
        return View('admin.CRUD.cards_template');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
