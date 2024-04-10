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
use App\Services\TableViewDataService;
use App\Services\CardService;
use Carbon\Carbon;


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

    public function __construct(TableViewDataService $tableViewDataService, CardService $cardService)
    {
        $this->model = Unit::class;
        $this->controller = collect([
            '0' => 'dashboard', // Use a string for the controller name
            '1' => ' Dashboard',
        ]);

        $this->tableViewDataService = $tableViewDataService;
        $this->cardService = $cardService;
    }

    public function index(Request $request)
    {
        $controller = $this->controller;
        $user = auth()->user();

        $properties = Property::with('units', 'leases', 'invoices')->get();
        $units = Unit::with('property', 'lease', 'invoices','tickets')->get();

       // dd($user->roles);

        if ($user && $user->id !== 1 && $user->roles->first()->name === "Tenant") {
            $cardData = $this->cardService->tenantTopCard($properties, $units);
        } else {
            $cardData = $this->cardService->topCard($properties, $units);
        }

        $invoiceData = Invoice::selectRaw('MONTH(created_at) as month, SUM(totalamount) as total')
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get()
            ->pluck('total', 'month')
            ->all();


        // Example query to get payment data (adjust the query to fit your needs)
        $paymentData = Payment::selectRaw('MONTH(created_at) as month, SUM(totalamount) as total')
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get()
            ->pluck('total', 'month')
            ->all();

        return View('admin.Report.dashboard', compact('cardData', 'controller', 'invoiceData', 'paymentData'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */


    private function getAdminCardData($month, $year)
    {
        $invoiceQuery = Invoice::query();
        $paymentQuery = Payment::query();
        $this->tableViewDataService->applyDateRangeFilter($invoiceQuery, $month, $year);
        $this->tableViewDataService->applyDateRangeFilter($paymentQuery, $month, $year);

        $propertyCount = Property::count();
        $unitCount = Unit::count();
        $leaseCount = Lease::count();
        $percentage = ($unitCount > 0) ? round(($leaseCount / $unitCount) * 100) : 0;
        $informationCardInfo = 'Total Units';

        $invoiceCount = $invoiceQuery->count();
        $paymentCount = $paymentQuery->count();
        $paymentSum = $paymentQuery->sum('totalamount');
        $invoiceSum = $invoiceQuery->sum('totalamount');
        $totalCardInfo1 = 'Generated';
        $totalCardInfo2 = 'Paid';
        //   dd($invoiceSum);
        // Structure the data with card type information.
        $cards = [
            'All Properties' => 'information',
            'Leases' => 'progress',
            'Invoices' => 'total',
            'Payments' => 'cash'
            // Add other card types for admin role.
        ];

        $data = [
            'All Properties' => [
                'modelCount' => $propertyCount,
                'modeltwoCount' => $unitCount,
                'informationCardInfo' => $informationCardInfo,
            ],
            'Leases' => [
                'modelCount' => $unitCount,
                'modeltwoCount' => $leaseCount,
                'percentage' => $percentage,
                // Add other data points related to maintenanceCount card.
            ],
            'Invoices' => [
                'modelCount' => $invoiceCount,
                'modeltwoCount' => $paymentCount,
                'totalCardInfo1' => $totalCardInfo1,
                'totalCardInfo2' => $totalCardInfo2,
                // Add other data points related to maintenanceCount card.
            ],
            'Payments' => [
                'modelCount' => $paymentSum,
                'modeltwoCount' => $invoiceSum,
                'totalCardInfo1' => $totalCardInfo1,
                'totalCardInfo2' => $totalCardInfo2,
                // Add other data points related to maintenanceCount card.
            ],
            // Add other card data for admin role.
        ];

        return ['cards' => $cards, 'data' => $data];
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
