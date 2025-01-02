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


    /////// DASHBOARD CARDS
    public function propertyCard($property)
    {


        $propertyCount = $property->count();
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

        // Calculate the occupancy rate
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
            'propertycount' => ['title' => 'Total Properties', 'value' => $propertyCount, 'amount' => '', 'percentage' => '', 'links' => '', 'desc' => 'Active'],
            'Residential' => ['title' => 'Residential', 'value' => $residentialCount, 'amount' => '', 'percentage' => '', 'links' => '', 'desc' => 'Active'],
            'Commercial' => ['title' => 'Commercial', 'value' => $commercialCount, 'amount' => '', 'percentage' => '', 'links' => '', 'desc' => 'Active'],
            'unitcount' => ['title' => 'Total Units', 'value' => $unitCount, 'amount' => '', 'percentage' => '', 'links' => '', 'desc' => ''],
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


}
