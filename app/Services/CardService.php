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
            'propertyCount' => ['title' => 'Total Properties', 'value' => $propertyCount, 'amount' => '', 'pecentage' => '', 'links' => '/property'],
            'unitCount' => ['title' => 'Total Units', 'value' => $unitCount, 'amount' => '', 'pecentage' => '', 'links' => '/unit'],
            'invoices' => ['title' => 'Invoiced Amount', 'value' => '', 'amount' => $invoices, 'pecentage' => '', 'links' => '/invoice'],
            'balance' => ['title' => 'Balance', 'value' => '', 'amount' => $balance, 'pecentage' => '', 'links' => '/payment'],
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
            'maintenance' => ['title' => 'No of Tickets', 'value' => $maintenance, 'amount' => '', 'pecentage' => '', 'links' => '/ticket'],
            'invoiceSum' => ['title' => 'Total Invoiced', 'value' => $invoiceSum, 'amount' => '', 'pecentage' => '', 'links' => '/invoice'],
            'paymentSum' => ['title' => 'Total Paid', 'value' => '', 'amount' => $paymentSum, 'pecentage' => '', 'links' => '/invoice'],
            'balance' => ['title' => 'Unpaid Balance', 'value' => '', 'amount' => $balance, 'pecentage' => '', 'links' => '/payment'],
        ];
        return $cards;
    }

    public function propertyCard($property)
    {


        $propertyCount = $property->count();
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
            'propertycount' => ['title' => 'Total Properties', 'value' => $propertyCount, 'amount' => '', 'percentage' => '', 'links' => '/property', 'desc' => 'Active'],
            'unitcount' => ['title' => 'Total Units', 'value' => $unitCount, 'amount' => '', 'percentage' => '', 'links' => '/property', 'desc' => ''],
            'unitOccupied' => ['title' => 'Occupied Units', 'value' => $unitOccupied, 'amount' => '', 'percentage' => '', 'links' => '/unit', 'desc' => ''],
            'occupancyRate' => ['title' => 'Occupancy Rate', 'value' => '', 'amount' => '', 'percentage' => $formattedOccupancyRate, 'links' => '/unit', 'desc' => ''],
            'Residential' => ['title' => 'Total Tickets', 'value' => $ticketcount, 'amount' => '', 'percentage' => '', 'links' => '/ticket', 'desc' => ''],
            'Commercial' => ['title' => 'Pending Tickets', 'value' => $pendingTickets, 'amount' => '', 'percentage' => '', 'links' => '/ticket', 'desc' => ''],
        ];
        return $cards;
    }
    public function unitCard($units)
    {


        $unitCount = $units->count();
        $unitsleased =  $units->filter(function ($unit) {
            return $unit->lease !== null;
        })->count();
        $vacant = $unitCount - $unitsleased;
        //  $paymentCount = $invoicePayments->sum('payments_count');
        // Define the columns for the unit report
        $cards =  [
            'unitCount' => ['title' => 'Total Units', 'icon' => '', 'value' => $unitCount, 'amount' => '', 'pecentage' => '', 'links' => ''],
            'unitsleased' => ['title' => 'Units Leased', 'icon' => '', 'value' => $unitsleased, 'amount' => '', 'pecentage' => '', 'links' => ''],
            'No of Tenants' => ['title' => 'No of Tenants', 'icon' => '', 'value' => $unitsleased, 'amount' => '', 'pecentage' => '', 'links' => ''],
            'Vacant Units' => ['title' => 'Vacant Units', 'icon' => '', 'value' => $vacant, 'amount' => '', 'pecentage' => '', 'links' => ''],
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
            'amountinvoiced' => ['title' => 'Amount Invoiced', 'value' => '', 'amount' => $amountinvoiced, 'pecentage' => '', 'links' => ''],
            'invoicepaid' => ['title' => 'Amount Paid', 'value' => '', 'amount' => $invoicepaid, 'pecentage' => '', 'links' => ''],
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
        $mpesa = $mpesaPayments->sum('totalamount');
        $bank = $bankPayments->sum('totalamount');
        $cheque = $chequePayments->sum('totalamount');
        
     
        $cards =  [
            'paymentcount' => ['title' => 'Total Payments', 'value' => $paymentCount, 'amount' => '', 'percentage' => '', 'links' => ''],
            'totalpay' => ['title' => 'Total Amount', 'value' => '', 'amount' => $totalpay, 'percentage' => '', 'links' => ''],
            'cash' => ['title' => 'Cash Amount', 'value' => '', 'amount' => $cash, 'percentage' => '', 'links' => ''],
            'mpesa' => ['title' => 'M-Pesa Amount', 'value' => '', 'amount' => $mpesa, 'percentage' => '', 'links' => ''],
            'bank' => ['title' => 'Bank Amount', 'value' => '', 'amount' => $bank, 'percentage' => '', 'links' => ''],
            'cheque' => ['title' => 'Cheque Amount', 'value' => '', 'amount' => $cheque, 'percentage' => '', 'links' => ''],
            
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
