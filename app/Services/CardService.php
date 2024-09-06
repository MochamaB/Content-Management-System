<?php

// app/Services/Reports/PropertyReportService.php

namespace App\Services;

use App\Models\Chartofaccount;
use App\Models\Lease;
use App\Models\Property;
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

    public function __construct(InvoiceRepository $invoiceRepository, PaymentRepository $paymentRepository,
    UnitRepository $unitRepository)
    {
        $this->invoiceRepository = $invoiceRepository;
        $this->paymentRepository = $paymentRepository;
        $this->unitRepository = $unitRepository;
    }
    /////// DASHBOARD CARDS
    public function topCard($properties, $units, $filters)
    {
        $propertyCount = $properties->count();
        $unitCount = $units->count();
        // Filter invoices for the current month and sum their total amount
        $invoices = $units->flatMap(function ($unit) use ($filters) {
            return $unit->invoices()
                ->ApplyDateFilters($filters)
                ->get();
        })->sum('totalamount');
       // Filter payments for the current month and sum their total amount
       $payments = $units->flatMap(function ($unit) use ($filters) {
        return $unit->payments()
            ->ApplyDateFilters($filters)
            ->get();
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
            'invoices' => ['title' => 'Invoiced Amount', 'value' => '', 'amount' => $invoices, 'percentage' => '', 'links' => '/invoice'],
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
            'unitOccupied' => ['title' => 'Occupied Units', 'value' => $unitOccupied, 'amount' => '', 'percentage' => '', 'links' => '', 'desc' => ''],
            'occupancyRate' => ['title' => 'Occupancy Rate', 'value' => '', 'amount' => '', 'percentage' => $formattedOccupancyRate, 'links' => '', 'desc' => ''],
           
        ];
        return $cards;
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
            return $lease->status === 'Active';
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
        $paymentRate = $invoiceCount > 0 ? ($paymentCount / $invoiceCount) * 100 : 0;
        $payRate = number_format($paymentRate, 1);
        //   $invoicepaid =  $invoices->filter(function ($invoice) {
        //        return $invoice->payments !== null;
        //   })->sum('payments.totalamount');
        //  $invoicePayments = $invoices->withCount('payments')->get();
        //  $paymentCount = $invoicePayments->sum('payments_count');
        // Define the columns for the unit report
        $cards =  [
            'invoicecount' => ['title' => 'Total Invoices', 'value' => $invoiceCount, 'amount' => '', 'percentage' => '', 'links' => ''],
            'paymentCount' => ['title' => 'Total Payments', 'value' => $paymentCount, 'amount' => '', 'percentage' => '', 'links' => ''],
            'amountinvoiced' => ['title' => 'Amount Invoiced', 'value' => '', 'amount' => $amountinvoiced, 'percentage' => '', 'links' => ''],
            'invoicepaid' => ['title' => 'Amount Paid', 'value' => '', 'amount' => $invoicepaid, 'percentage' => '', 'links' => ''],
            'balance' => ['title' => 'Balance', 'value' => '', 'amount' => $balance, 'percentage' => '', 'links' => ''],
            'payRate' => ['title' => 'Payment Percentage', 'value' => '', 'amount' => '', 'percentage' => $payRate, 'links' => ''],
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

    public function meterReadingCard($reading,$filters)
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

       $expectedReadings = Unitcharge::where('charge_type','units')->count();
       $totalExpectedReadings = $expectedReadings * $months;
        // Get the count of reccuring charges that are for sale
        $totalReadings = $reading->count();
        $difference = $totalExpectedReadings - $totalReadings;
        $cards =  [
            'expectedReadings' => ['title' => 'Expected Readings', 'icon' => '', 'value' => $totalExpectedReadings, 'amount' => '', 'percentage' => '', 'links' => ''],
            'totalReadings' => ['title' => 'Actual Readings', 'icon' => '', 'value' => $totalReadings, 'amount' => '', 'percentage' => '', 'links' => ''],
            'unitRate' => ['title' => 'Charges Without Readings', 'icon' => '', 'value' => $difference, 'amount' => '', 'percentage' => '', 'links' => ''],
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
    
}
