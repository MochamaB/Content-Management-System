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

class CardService
{
    /////// DASHBOARD CARDS
    public function topCard($properties, $units)
    {
         // Get the start and end dates of the current month
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $propertyCount = $properties->count();
        $unitCount = $units->count();
        // Filter invoices for the current month and sum their total amount
        $invoices = $units->flatMap(function ($unit) use ($startOfMonth, $endOfMonth) {
            return $unit->invoices->filter(function ($invoice) use ($startOfMonth, $endOfMonth) {
                return $invoice->created_at >= $startOfMonth && $invoice->created_at <= $endOfMonth;
            });
        })->sum('totalamount');
       // Filter payments for the current month and sum their total amount
        $payments = $units->flatMap(function ($unit) use ($startOfMonth, $endOfMonth) {
            return $unit->payments->filter(function ($payment) use ($startOfMonth, $endOfMonth) {
                return $payment->created_at >= $startOfMonth && $payment->created_at <= $endOfMonth;
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
            'propertyCount' => ['title' => 'Total Properties', 'value' => $propertyCount, 'amount' => '', 'pecentage' => '', 'links' => '/property'],
            'unitCount' => ['title' => 'Total Units', 'value' => $unitCount, 'amount' => '', 'pecentage' => '', 'links' => '/unit'],
            'invoices' => ['title' => 'Invoiced This Month', 'value' => '', 'amount' => $invoices, 'pecentage' => '', 'links' => '/invoice'],
            'balance' => ['title' => 'Balance This Month', 'value' => '', 'amount' => $balance, 'pecentage' => '', 'links' => '/payment'],
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


    public function invoiceCard($invoices)
    {


        $invoiceCount = $invoices->count();
        $amountinvoiced = $invoices->sum('totalamount');
        $invoicepaid = $invoices->flatMap(function ($invoice) {
            return $invoice->payments;
        })->sum('totalamount');
        $balance = $amountinvoiced - $invoicepaid;
        //   $invoicepaid =  $invoices->filter(function ($invoice) {
        //        return $invoice->payments !== null;
        //   })->sum('payments.totalamount');
        //  $invoicePayments = $invoices->withCount('payments')->get();
        //  $paymentCount = $invoicePayments->sum('payments_count');
        // Define the columns for the unit report
        $cards =  [
            'invoicecount' => ['title' => 'Total Invoices This Month', 'value' => $invoiceCount, 'amount' => '', 'pecentage' => '', 'links' => ''],
            'amountinvoiced' => ['title' => 'Amount Invoiced', 'value' => '', 'amount' => $amountinvoiced, 'pecentage' => '', 'links' => ''],
            'invoicepaid' => ['title' => 'Amount Paid', 'value' => '', 'amount' => $invoicepaid, 'pecentage' => '', 'links' => ''],
            'balance' => ['title' => 'Balance Not Paid', 'value' => '', 'amount' => $balance, 'pecentage' => '', 'links' => ''],
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
}
