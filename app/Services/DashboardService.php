<?php

// app/Services/Reports/PropertyReportService.php

namespace App\Services;

use App\Models\Chartofaccount;
use App\Models\Lease;
use App\Models\Property;
use App\Models\SmsCredit;
use App\Models\Ticket;
use App\Models\Unit;
use App\Models\Unitcharge;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Repositories\InvoiceRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\UnitRepository;

class DashboardService
{
    private $invoiceRepository;
    private $paymentRepository;
    private $unitRepository;

    protected $statusClasses = [

        //Lease Status
        'Active' => 'active',
        'Expired' => 'warning',
        'Terminated' => 'error',
        'Suspended' => 'dark',

        // Task statuses
        'Completed' => 'active',
        'New' => 'warning',
        'OverDue' => 'error',
        'In Progress' => 'information',
        'Assigned' => 'dark',

        // Priority levels
        'critical' => 'error',
        'high' => 'warning',
        'normal' => 'active',
        'low' => 'dark',

        // Payment statuses
        'Paid' => 'active',
        'Unpaid' => 'warning',
        'Over Due' => 'danger',
        'Partially Paid' => 'dark',
        'Over Paid' => 'light',
    ];

    public function __construct(
        InvoiceRepository $invoiceRepository,
        PaymentRepository $paymentRepository,
        UnitRepository $unitRepository
    ) {
        $this->invoiceRepository = $invoiceRepository;
        $this->paymentRepository = $paymentRepository;
        $this->unitRepository = $unitRepository;
    }

    public function getStatusClass($status)
    {
        return $this->statusClasses[$status] ?? 'active';
    }


    /////// PROPERTY DASHBOARD CARDS
    public function propertyCard($property)
    {


        $propertyCount = $property->count();
        $activeProperty = $property->filter(function ($property) {
            return $property->property_status === 0;
        });
        $residentialProperties = $property->filter(function ($property) {
            return $property->propertyType->property_category === 'Residential';
        });
        $residentialCount = $residentialProperties->count();
        $commercialProperties = $property->filter(function ($property) {
            return $property->propertyType->property_category === 'Commercial';
        });
        $commercialCount = $commercialProperties->count();
        $unitCount =  $property->flatMap(function ($property) {
            return $property->units;
        })->count();
        $unitOccupied = $property->flatMap(function ($property) {
            return $property->units->filter(function ($unit) {
                return $unit->lease && $unit->lease->status == 'Active'; // Example condition
            });
        })->count();

        // Count units in residential properties
        $residentialUnitCount = $residentialProperties->flatMap(function ($property) {
            return $property->units;
        })->count();

        // Count units in commercial properties
        $commercialUnitCount = $commercialProperties->flatMap(function ($property) {
            return $property->units;
        })->count();

        // Calculate the occupancy rate and number
        $availableunits = $unitCount - $unitOccupied;
        $occupancyRate = $unitCount > 0 ? ($unitOccupied / $unitCount) * 100 : 0;

        // Format the occupancy rate (optional)
        $formattedOccupancyRate = number_format($occupancyRate, 1);
        $ticketcount =  $property->flatMap(function ($property) {
            return $property->tickets;
        })->count();

        $pendingTickets = $property->flatMap(function ($property) {
            return $property->tickets->filter(function ($ticket) {
                return $ticket->status !== 'completed'; // Filter out tickets with the status "completed"
            });
        })->count();


        $cards =  [
            'propertycount' => ['title' => 'Total Properties', 'value' => $propertyCount, 'amount' => '', 'percentage' => '', 'links' => '', 'desc' => 'Active', 'icon' => 'mdi mdi-city', 'count' => $propertyCount, 'countname' => 'Active'],
            'Residential' => ['title' => 'Residential', 'value' => $residentialCount, 'amount' => '', 'percentage' => '', 'links' => '', 'desc' => 'Active', 'icon' => 'mdi mdi-home', 'count' => $residentialUnitCount, 'countname' => 'Total units'],
            'Commercial' => ['title' => 'Commercial', 'value' => $commercialCount, 'amount' => '', 'percentage' => '', 'links' => '', 'desc' => 'Active', 'icon' => 'mdi mdi-office-building', 'count' => $commercialUnitCount, 'countname' => 'Total spaces'],
            'unitcount' => ['title' => 'Total Units', 'value' => $unitCount, 'amount' => '', 'percentage' => '', 'links' => '', 'desc' => '', 'icon' => 'mdi mdi-door', 'count' => $availableunits, 'countname' => 'Available Units'],
            //  'unitOccupied' => ['title' => 'Occupied Units', 'value' => $unitOccupied, 'amount' => '', 'percentage' => '', 'links' => '', 'desc' => ''],
            //  'occupancyRate' => ['title' => 'Occupancy Rate', 'value' => '', 'amount' => '', 'percentage' => $formattedOccupancyRate, 'links' => '', 'desc' => ''],

        ];
        return $cards;
    }
    public function propertyOccupancyRate($property)
    {
        $unitCount =  $property->flatMap(function ($property) {
            return $property->units;
        })->count();
        $unitOccupied = $property->flatMap(function ($property) {
            return $property->units->filter(function ($unit) {
                return $unit->lease && $unit->lease->status == 'Active'; // Example condition
            });
        })->count();
        // Calculate the occupancy rate
        $occupancyRate = $unitCount > 0 ? ($unitOccupied / $unitCount) * 100 : 0;

        return $occupancyRate;
    }
    

    /////////////////


    public function unitCard($units)
    {


        $unitCount = $units->count();
        // Get the count of units that are for sale
        $forRent = $units->filter(function ($unit) {
            return $unit->unit_type === 'rent';
        })->count();
        // Get the count of units that are for sale
        $forSale = $units->filter(function ($unit) {
            return $unit->unit_type === 'sale';
        })->count();
        $unitsleased =  $units->filter(function ($unit) {
            return $unit->lease !== null;
        })->count();
        $vacant = $unitCount - $unitsleased;
        //  $paymentCount = $invoicePayments->sum('payments_count');
        // Define the columns for the unit report
        $cards =  [
            'unitCount' => ['title' => 'Total Units', 'icon' => 'mdi mdi-door', 'value' => $unitCount, 'amount' => '', 'percentage' => '', 'links' => ''],
            'forRent' => ['title' => 'For Rent', 'icon' => 'fa fa-key', 'value' => $forRent, 'amount' => '', 'percentage' => '', 'links' => ''],
            'forSale' => ['title' => 'For Sale', 'icon' => 'fa fa-money', 'value' => $forSale, 'amount' => '', 'percentage' => '', 'links' => ''],
            'unitsleased' => ['title' => 'Leases', 'icon' => 'fa fa-handshake-o', 'value' => $unitsleased, 'amount' => '', 'percentage' => '', 'links' => ''],
            'Vacant Units' => ['title' => 'Vacant Units', 'icon' => 'fa fa-minus-square-o', 'value' => $vacant, 'amount' => '', 'percentage' => '', 'links' => ''],
        ];
        return $cards;
    }
    public function unitOccupancyRate($unit)
    {
        $unitCount = $unit->count();
        $unitOccupied =  $unit->filter(function ($unit) {
            return $unit->lease && $unit->lease->status == 'Active';
        })->count();
        // Calculate the occupancy rate
        $occupancyRate = $unitCount > 0 ? ($unitOccupied / $unitCount) * 100 : 0;

        return $occupancyRate;
    }

    public function utilityCard($utilities)
    {
        $totalUtilities = $utilities->count();
        $recurringCharges = $utilities->where('is_recurring_by_default', true)->count();
        $fixedCharges = $utilities->where('utility_type', 'fixed amount')->count();
        $rateBasedCharges = $utilities->where('utility_type', 'by rate')->count();
        $highestRate = $utilities->max('default_rate');
        $lowestRate = $utilities->min('default_rate');
        $averageRate = $utilities->avg('default_rate');

        // Optional: Group by charge cycles
        $chargeCycles = $utilities->groupBy('default_charge_cycle')
            ->map(fn($group) => $group->count());

        $cards = [
            'totalUtilities' => ['title' => 'Total Utilities','icon' => 'mdi mdi-water','value' => $totalUtilities],
            'recurringCharges' => ['title' => 'Recurring Utilities','icon' => 'mdi mdi-refresh','value' => $recurringCharges],
            'fixedCharges' => ['title' => 'Fixed-Based Utilities','icon' => 'mdi mdi-currency-usd','value' => $fixedCharges],
            'rateBasedCharges' => ['title' => 'Rate-Based Utilities','icon' => 'mdi mdi-chart-line','value' => $rateBasedCharges],

        ];

        return $cards;
    }

    public function leaseCard($lease)
    {


        $leaseCount = $lease->count();
        // Get the count of units that are for sale
        $activeleases = $lease->filter(function ($lease) {
            return $lease->status === Lease::STATUS_ACTIVE;
        })->count();
        // Get the count of units that are for sale
        $open = $lease->filter(function ($lease) {
            return $lease->lease_period === 'open';
        })->count();
        $fixed = $lease->filter(function ($lease) {
            return $lease->lease_period === 'fixed';
        })->count();
        $inactive = $leaseCount - $activeleases;
        //  $paymentCount = $invoicePayments->sum('payments_count');
        // Define the columns for the unit report
        $cards =  [
            'leaseCount' => ['title' => 'Total Leases', 'icon' => '', 'value' => $leaseCount, 'amount' => '', 'percentage' => '', 'links' => ''],
            'activeleases' => ['title' => 'Active', 'icon' => '', 'value' => $activeleases, 'amount' => '', 'percentage' => '', 'links' => ''],
            'open' => ['title' => 'Open Leases', 'icon' => '', 'value' => $open, 'amount' => '', 'percentage' => '', 'links' => ''],
            'fixed' => ['title' => 'Closed Leases', 'icon' => '', 'value' => $fixed, 'amount' => '', 'percentage' => '', 'links' => ''],
            'inactive Units' => ['title' => 'Inactive / Suspended', 'icon' => '', 'value' => $inactive, 'amount' => '', 'percentage' => '', 'links' => ''],
        ];
        return $cards;
    }


    public function invoiceCard($invoices)
    {


        $invoiceCount =  $this->invoiceRepository->getInvoiceCount($invoices);
        $amountinvoiced = $this->invoiceRepository->getAmountinvoiced($invoices);
        $invoicepaid = $this->invoiceRepository->getInvoicepaidAmount($invoices);
        $paymentCount =  $this->invoiceRepository->getInvoicePaymentCount($invoices);
        $balance = $amountinvoiced - $invoicepaid;
        $balanceCount = $invoiceCount - $paymentCount;
        $paymentRate = $invoiceCount > 0 ? ($paymentCount / $invoiceCount) * 100 : 0;
        $payRate = number_format($paymentRate, 1);
        //   $invoicepaid =  $invoices->filter(function ($invoice) {
        //        return $invoice->payments !== null;
        //   })->sum('payments.totalamount');
        //  $invoicePayments = $invoices->withCount('payments')->get();
        //  $paymentCount = $invoicePayments->sum('payments_count');
        // Define the columns for the unit report
        $cards =  [
            'total' => ['title' => 'Amount Invoiced', 'value' => '', 'amount' => $amountinvoiced, 'percentage' => '', 'count' => $invoiceCount, 'countname' => 'Total No'],
            'invoicepaid' => ['title' => 'Paid', 'value' => '', 'amount' => $invoicepaid, 'percentage' => '', 'count' => $paymentCount, 'countname' => 'No of paid'],
            'balance' => ['title' => 'Balance', 'value' => '', 'amount' => $balance, 'percentage' => '', 'count' => $balanceCount, 'countname' => 'No of unpaid'],
        ];
        return $cards;
    }
}
