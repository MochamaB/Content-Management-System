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
            'Dashboard',
            'Properties',
            'Financials',
        ]);
        $tabIcons = collect([
            'Dashboard' => 'icon-chart', 
            'Properties' => 'icon-home',
            'Financials' => 'icon-calculator',
        ]);
        $controller = $this->controller;
        $user = auth()->user();
        $filters = $request->except(['tab','_token','_method']);
        $filterdata = $this->filterService->getDashboardFilters();
        $properties = Property::with('units', 'leases', 'invoices')->applyFilters($filters)->get();
        $units = Unit::with('property', 'lease', 'invoices','tickets')->get();


      $tax = Tax::with('taxable')->get();
      //  dd($tax);

       ///1. Dashboard Tab
        if ($user && $user->id !== 1 && $user->roles->first()->name === "Tenant") {
            $cardData = $this->cardService->tenantTopCard($properties, $units, $filters);
        } else {
            $cardData = $this->cardService->topCard($properties, $units,$filters);
        }
        /// CHART DATA
        $chartData = $this->getInvoiceChartData($filters);
        // TICKET DATA ////
        $tickets = Ticket::latest()->take(3)->get();
         //TOTAL TAXES
         $taxSummary = $this->showTaxesWidget();
        // dd($taxesByCategory);

        //2. Property Tab
        $propertyCard = $this->cardService->propertyCard($properties);

            $tabContents = [];
            foreach ($tabTitles as $title) {
                if ($title === 'Dashboard') {
                    $tabContents[] = View('admin.Dashboard.dashboardall',
                    compact('properties','cardData','chartData','tickets','taxSummary'))->render();
                } elseif ($title === 'Properties') {
                    $tabContents[] = View('admin.Dashboard.dashboardproperties' ,compact('propertyCard'))->render();
                } elseif ($title === 'Financials') {
                    $tabContents[] = View('admin.Dashboard.dashboardfinancials', compact('properties'))->render();
                }
            }

        return View('admin.Dashboard.dashboard', compact('cardData', 'controller','tabTitles', 'tabContents','tabIcons','filterdata'));
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

    public function showTaxesByCategory()
{
    // Fetch PropertyTypes with their properties and related payments
    $propertyTypes = PropertyType::with(['property.payments'])->get();

    $taxSummary = [];

    foreach ($propertyTypes as $propertyType) {
        $totalTaxAmount = 0;

        foreach ($propertyType->property as $property) {
            // Sum all tax amounts from the payments of the property
            $propertyTaxAmount = $property->payments->sum('taxamount');
            $totalTaxAmount += $propertyTaxAmount;
        }

        // Prepare a summary for each PropertyType
        $taxSummary[] = [
            'category' => $propertyType->property_category,
            'taxes' => $propertyType->taxes->map(function ($tax) use ($totalTaxAmount) {
                return [
                    'tax_name' => $tax->name,  // Tax name from the Tax model
                    'tax_amount' => $totalTaxAmount  // Total tax amount for the properties in this category
                ];
            }),
            'total_tax_amount' => $totalTaxAmount
        ];
    }

    // Pass the tax summary to the view
        return $taxSummary;
}

public function showTaxesWidget()
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
                // Sum all tax amounts from the payments of the property
                $propertyTaxAmount = $property->payments->sum('taxamount');
                $totalTaxAmount += $propertyTaxAmount;
            }

            // Prepare a summary for each PropertyType within the category
            foreach ($propertyType->taxes as $tax) {
                $categorySummary['taxes'][] = [
                    'tax_name' => $tax->name, // Tax name from the Tax model
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
