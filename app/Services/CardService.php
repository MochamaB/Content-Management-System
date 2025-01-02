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

class CardService
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
    public function topCard($properties, $units, $filters)
    {
        $propertyCount = $properties->count();
        $unitCount = $units->count();
        // Filter invoices for the current month and sum their total amount
        $invoices = $units->flatMap(function ($unit) use ($filters) {
            return $unit->invoices()
                ->ApplyCurrentMonthFilters($filters)
                ->get();
        })->sum('totalamount');
        // Filter payments that are specifically linked to the invoices for the current month
        $payments = $units->flatMap(function ($unit) use ($filters) {
                return $unit->invoices() // Filter payments through invoices
                ->with(['payments' => function ($query) use ($filters) {
                    $query->ApplyCurrentMonthFilters($filters);
                }])
                ->get()
                ->flatMap(function ($invoice) {
                    return $invoice->payments;
                });
            })->sum('totalamount');

        $balance = $invoices - $payments;
        //   $invoicepaid =  $invoices->filter(function ($invoice) {
        //        return $invoice->payments !== null;
        //   })->sum('payments.totalamount');
        //  $invoicePayments = $invoices->withCount('payments')->get();
        //  $paymentCount = $invoicePayments->sum('payments_count');
        // Define the columns for the unit report
        $cards =  [
            'propertyCount' => ['title' => 'Total Properties', 'value' => $propertyCount, 'amount' => '', 'percentage' => '', 'links' => '/property'],
            'unitCount' => ['title' => 'Total Units', 'value' => $unitCount, 'amount' => '', 'percentage' => '', 'links' => '/unit'],
            'invoices' => ['title' => 'Amount Invoiced', 'value' => '', 'amount' => $invoices, 'percentage' => '', 'links' => '/invoice'],
            'payments' => ['title' => 'Amount Paid', 'value' => '', 'amount' => $payments, 'percentage' => '', 'links' => '/payments'],
            'balance' => ['title' => 'Balance', 'value' => '', 'amount' => $balance, 'percentage' => '', 'links' => '/payment'],
        ];
        return $cards;
    }

    public function tenantTopCard($properties, $units)
    {

        $invoiceCount =  $units->flatMap(function ($units) {
            return $units->invoices;
        })->count();
        $maintenance = $units->flatMap(function ($units) {
            return $units->tickets;
        })->count();
        $invoiceSum =  $units->flatMap(function ($units) {
            return $units->invoices;
        })->sum('totalamount');
        $paymentSum = $units->flatMap(function ($units) {
            return $units->payments;
        })->sum('totalamount');
        $balance = $invoiceSum - $paymentSum;
        $cards =  [
            'maintenance' => ['title' => 'No of Tickets', 'value' => $maintenance, 'amount' => '', 'percentage' => '', 'links' => '/ticket'],
            'invoiceSum' => ['title' => 'Total Invoiced', 'value' => $invoiceSum, 'amount' => '', 'percentage' => '', 'links' => '/invoice'],
            'paymentSum' => ['title' => 'Total Paid', 'value' => '', 'amount' => $paymentSum, 'percentage' => '', 'links' => '/invoice'],
            'balance' => ['title' => 'Unpaid Balance', 'value' => '', 'amount' => $balance, 'percentage' => '', 'links' => '/payment'],
        ];
        return $cards;
    }

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
            'unitCount' => ['title' => 'Total Units', 'icon' => '', 'value' => $unitCount, 'amount' => '', 'percentage' => '', 'links' => ''],
            'forRent' => ['title' => 'For Rent', 'icon' => '', 'value' => $forRent, 'amount' => '', 'percentage' => '', 'links' => ''],
            'forSale' => ['title' => 'For Sale', 'icon' => '', 'value' => $forSale, 'amount' => '', 'percentage' => '', 'links' => ''],
            'unitsleased' => ['title' => 'Active Leases', 'icon' => '', 'value' => $unitsleased, 'amount' => '', 'percentage' => '', 'links' => ''],
            'Vacant Units' => ['title' => 'Vacant Units', 'icon' => '', 'value' => $vacant, 'amount' => '', 'percentage' => '', 'links' => ''],
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
    public function LeaseSummary($lease)
    {
        /// TABLE DATA ///////////////////////////
        $tableData = [
            'headers' => [' PROPERTY', 'TENANT', 'PERIOD', 'STATUS','START DATE'],
            'rows' => [],
        ];
        
          //  dd($leaseData);
            $startDateFormatted = empty($lease->created_at) ? 'Not set' : Carbon::parse($lease->created_at)->format('Y-m-d');
            $endDateFormatted = empty($lease->enddate) ? 'Not set' : Carbon::parse($lease->enddate)->format('Y-m-d');
            // Calculate the number of days left on the lease (if end date is available)
            $daysLeft = ($lease && $lease->enddate) ? Carbon::parse($lease->enddate)->diffInDays(Carbon::now()) : null;
           
            // Get the CSS class for the current status, default to 'badge-secondary' if not found
            $status = $lease->getStatusLabel();
            $statusClass =$this->getStatusClass($status) ?? 'active';
            // Generate the status badge
            $statusBadge = '<span class="badge badge-' . $statusClass . '">' . $status . '</span>';
            $isDeleted = $lease->deleted_at !== null;
        
            

            $tableData['rows'][] = [
                $lease->property->property_name,
                $lease->user->firstname.' '.$lease->user->lastname,
                $lease->lease_period,
                $statusBadge,
                $startDateFormatted

            ];
        


        return $tableData;
    }

    public function RentHistory($lease)
    {
        /// TABLE DATA ///////////////////////////
        $tableData = [
            'headers' => [' RENT DATE', 'AMOUNT', 'STATUS'],
            'rows' => [],
        ];
        
          //  dd($leaseData);
           
        
            

            $tableData['rows'][] = [
                

            ];
        


        return $tableData;
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
            'total' => ['title' => 'Total Invoices', 'value' => '', 'amount' => $amountinvoiced, 'percentage' => '', 'count' => $invoiceCount. ' invoices'],
            'invoicepaid' => ['title' => 'Paid', 'value' => '', 'amount' => $invoicepaid, 'percentage' => '', 'count' => $paymentCount. ' invoices'],
            'balance' => ['title' => 'Balance', 'value' => '', 'amount' => $balance, 'percentage' => '', 'count' => $balanceCount. ' invoices'],
        ];
        return $cards;
    }

    public function paymentCard($payments)
    {
        $paymentCount = $payments->count();
        $totalpay = $payments->sum('totalamount');
        // Filter payments by payment method name (e.g., "cash")
        $cashPayments = $payments->filter(function ($payment) {
            return $payment->PaymentMethod->name === 'cash';
        });
        $mpesaPayments = $payments->filter(function ($payment) {
            return $payment->PaymentMethod->name === 'm-pesa';
        });
        $bankPayments = $payments->filter(function ($payment) {
            return $payment->PaymentMethod->name === 'bank';
        });
        $chequePayments = $payments->filter(function ($payment) {
            return $payment->PaymentMethod->name === 'cheque';
        });
        $cash = $cashPayments->sum('totalamount');
        $cashCount = $cashPayments->count();

        $mpesa = $mpesaPayments->sum('totalamount');
        $mpesaCount = $mpesaPayments->count();

        $bank = $bankPayments->sum('totalamount');
        $bankCount = $bankPayments->count();

        $cheque = $chequePayments->sum('totalamount');
        $chequeCount = $chequePayments->count();


        $cards =  [
            'paymentcount' => ['title' => 'Total Payments', 'value' => $paymentCount, 'amount' => '', 'percentage' => '', 'links' => ''],
            'totalpay' => ['title' => 'Total Amount ', 'value' => '', 'amount' => $totalpay, 'percentage' => '', 'links' => ''],
            'cash' => ['title' => 'Cash (' . $cashCount . ')', 'value' => '', 'amount' => $cash, 'percentage' => '', 'links' => ''],
            'mpesa' => ['title' => 'M-Pesa (' . $mpesaCount . ')', 'value' => '', 'amount' => $mpesa, 'percentage' => '', 'links' => ''],
            'bank' => ['title' => 'Bank (' . $bankCount . ')', 'value' => '', 'amount' => $bank, 'percentage' => '', 'links' => ''],
            'cheque' => ['title' => 'Cheque (' . $chequeCount . ')', 'value' => '', 'amount' => $cheque, 'percentage' => '', 'links' => ''],

        ];
        return $cards;
    }


    public function unitchargeCard($unitcharge)
    {



        // Get the count of reccuring charges that are for sale
        $recurring = $unitcharge->filter(function ($unitcharge) {
            return $unitcharge->recurring_charge === 'yes';
        })->count();
        // Get the count of units that are for sale
        $fixedRate = $unitcharge->filter(function ($unitcharge) {
            return $unitcharge->charge_type === 'fixed';
        })->count();
        $unitRate = $unitcharge->filter(function ($unitcharge) {
            return $unitcharge->charge_type === 'units';
        })->count();

        //  $paymentCount = $invoicePayments->sum('payments_count');
        // Define the columns for the unit report
        $cards =  [
            'recurring' => ['title' => 'Total Recurring Charges', 'icon' => '', 'value' => $recurring, 'amount' => '', 'percentage' => '', 'links' => ''],
            'fixedRate' => ['title' => 'Charges Per Fixed Rate', 'icon' => '', 'value' => $fixedRate, 'amount' => '', 'percentage' => '', 'links' => ''],
            'unitRate' => ['title' => 'Charges Per Unit Rate', 'icon' => '', 'value' => $unitRate, 'amount' => '', 'percentage' => '', 'links' => ''],
        ];
        return $cards;
    }

    public function meterReadingCard($reading, $filters)
    {
        // Convert the dates to Carbon instances
        $fromDate = isset($filters['from_date']) ? Carbon::parse($filters['from_date']) : null;
        $toDate = isset($filters['to_date']) ? Carbon::parse($filters['to_date']) : null;

        // Check if both dates are valid before calculating the difference in months
        if ($fromDate && $toDate) {
            $months = $fromDate->diffInMonths($toDate) + 1;
        } else {
            // Handle the case where one or both dates are not provided
            $months = 1;
        }

        $expectedReadings = Unitcharge::where('charge_type', 'units')->count();
        $totalExpectedReadings = $expectedReadings * $months;
        // Get the count of reccuring charges that are for sale
        $totalReadings = $reading->count();
        $difference = $totalExpectedReadings - $totalReadings;
        $cards =  [
            'expectedReadings' => ['title' => 'Expected Readings', 'icon' => '', 'value' => $totalExpectedReadings, 'amount' => '', 'percentage' => '', 'links' => ''],
            'totalReadings' => ['title' => 'Actual Readings', 'icon' => '', 'value' => $totalReadings, 'amount' => '', 'percentage' => '', 'links' => ''],
            'unitRate' => ['title' => 'Missing Readings', 'icon' => '', 'value' => $difference, 'amount' => '', 'percentage' => '', 'links' => ''],
        ];
        return $cards;
    }

    public function expenseCard($expense)
    {

        $expenseCount = $expense->count();
        $totaldue = $expense->sum('totalamount');
        // Get the count of reccuring charges that are for sale
        $totalPaid =  $expense->flatMap(function ($expense) {
            return $expense->payments;
        })->sum('totalamount');
        $difference = $totaldue - $totalPaid;
        $cards =  [
            'expenseCount' => ['title' => 'Total Expenses', 'icon' => '', 'value' => $expenseCount, 'amount' => '', 'percentage' => '', 'links' => ''],
            'totaldue' => ['title' => 'Total Amount Due', 'icon' => '', 'value' => '', 'amount' => $totaldue, 'percentage' => '', 'links' => ''],
            'totalPaid' => ['title' => 'Total Amount Paid', 'icon' => '', 'value' => '', 'amount' => $totalPaid, 'percentage' => '', 'links' => ''],
            'difference' => ['title' => 'Balance', 'icon' => '', 'value' => '', 'amount' => $difference, 'percentage' => '', 'links' => ''],
        ];
        return $cards;
    }

    public function depositCard($deposit)
    {

        $depositCount = $deposit->count();
        $totaldue = $deposit->sum('totalamount');
        // Get the count of reccuring charges that are for sale
        $totalPaid =  $deposit->flatMap(function ($deposit) {
            return $deposit->payments;
        })->sum('totalamount');
        $difference = $totaldue - $totalPaid;
        $cards =  [
            'depositCount' => ['title' => 'Total Deposits', 'icon' => '', 'value' => $depositCount, 'amount' => '', 'percentage' => '', 'links' => ''],
            'totaldue' => ['title' => 'Total Amount Received', 'icon' => '', 'value' => '', 'amount' => $totaldue, 'percentage' => '', 'links' => ''],
            'totalPaid' => ['title' => 'Total Amount Paid Offs', 'icon' => '', 'value' => '', 'amount' => $totalPaid, 'percentage' => '', 'links' => ''],
            'difference' => ['title' => 'Balance', 'icon' => '', 'value' => '', 'amount' => $difference, 'percentage' => '', 'links' => ''],
        ];
        return $cards;
    }



    public function invoiceChart($invoices = null)
    {
        $chart = new \stdClass();
        // Assign properties to the $card object
        $chart->title = "Invoice Chart Title"; // Example title
        $chart->description = "This is the description of the invoice chart."; // Example description
        return $chart;
    }
    public function userCard($users)
    {

        $userCount = $users->count();
        $active = $users->filter(function ($user) {
            return $user->status === 'Active';
        })->count();
        $inactive = $users->filter(function ($user) {
            return $user->status !== 'Active';
        })->count();
        // Get the count of reccuring charges that are for sale
        $tenants = $users->filter(function ($user) {
            return $user->hasRole('Tenant'); // Check if the user has the 'tenant' role
        })->count();
        $cards =  [
            'userCount' => ['title' => 'Total Users', 'icon' => '', 'value' => $userCount, 'amount' => '', 'percentage' => '', 'links' => ''],
            'tenants' => ['title' => 'Total Tenants', 'icon' => '', 'value' => $tenants, 'amount' => '', 'percentage' => '', 'links' => ''],
            'active' => ['title' => 'Active Users', 'icon' => '', 'value' => $active, 'amount' =>'' , 'percentage' => '', 'links' => ''],
            'inActive' => ['title' => 'In Active', 'icon' => '', 'value' => $inactive, 'amount' =>'' , 'percentage' => '', 'links' => ''],
        ];
        return $cards;
    }

    public function ticketCard($tickets)
    {
        $totalTicketCount = $tickets->count();
        $completed = $tickets->filter(function ($ticket) {
            return $ticket->status === Ticket::STATUS_COMPLETED;
        })->count();
        $inProgress = $tickets->filter(function ($ticket) {
            return $ticket->status === Ticket::STATUS_IN_PROGRESS;
        })->count();
        $pending = $tickets->filter(function ($ticket) {
            return $ticket->status === Ticket::STATUS_PENDING;
        })->count();
        $onhold = $tickets->filter(function ($ticket) {
            return $ticket->status === Ticket::STATUS_ON_HOLD;
        })->count();
       
        // Calculate percentages for each status
    $inProgressPercentage = $totalTicketCount > 0 ? ($inProgress / $totalTicketCount) * 100 : 0;
    $pendingPercentage = $totalTicketCount > 0 ? ($pending / $totalTicketCount) * 100 : 0;
    $completedPercentage = $totalTicketCount > 0 ? ($completed / $totalTicketCount) * 100 : 0;
    $onholdPercentage = $totalTicketCount > 0 ? ($onhold / $totalTicketCount) * 100 : 0;
        $cards =  [
            'totalCount' => ['title' =>'Total Tickets', 'total' =>$totalTicketCount],
            'completed' => ['title' => 'Completed', 'class' => 'success', 'value' => $completed, 'amount' =>'', 'percentage' => round($completedPercentage), 'links' => '', 'tooltip' => round($completedPercentage)."% of tickets are completed"],
            'inProgress' => ['title' => 'In Progress', 'class' => 'primary', 'value' => $inProgress, 'amount' => '', 'percentage' =>  round($inProgressPercentage), 'links' => '', 'tooltip' => round($inProgressPercentage)."% of tickets in progress"],
            'pending' => ['title' => 'Pending', 'class' => 'warning', 'value' => $pending, 'amount' =>'', 'percentage' => round($pendingPercentage), 'links' => '', 'tooltip' => round($pendingPercentage)."% of tickets are pending"],
            'onhold' => ['title' => 'On Hold', 'class' => 'dark', 'value' => $onhold, 'amount' =>'', 'percentage' => round($onholdPercentage), 'links' => '', 'tooltip' => round($onholdPercentage)."% of tickets are on hold"],
        ];
        return $cards;
    }



}
