<?php

// app/Services/Reports/PropertyReportService.php

namespace App\Services;

use App\Models\Chartofaccount;
use App\Models\Lease;
use App\Models\Property;
use App\Models\Unit;
use App\Models\Unitcharge;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CardService
{
    /////// DASHBOARD CARDS
    public function topCard($properties,$units)
    {
        $propertyCount = $properties->count();
        $unitCount = $units->count();
        $invoices =  $units->flatMap(function ($units) {
            return $units->invoices;
        })->sum('totalamount');
        $payments = $units->flatMap(function ($units) {
            return $units->payments;
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
            'unitCount' => ['title' => 'Total Units', 'value' => $unitCount, 'amount' =>'', 'pecentage' => '', 'links' => '/unit'],
            'invoices' => ['title' => 'Total Invoiced', 'value' => '', 'amount' => $payments, 'pecentage' => '', 'links' => '/invoice'],
            'balance' => ['title' => 'Balance Not Paid', 'value' => '', 'amount' => $balance, 'pecentage' => '', 'links' => '/payment'],
        ];
        return $cards;
    }

    public function tenantTopCard($properties,$units)
    {
        $propertyCount = $properties->count();
        $unitCount = $units->count();
        $invoiceCount =  $units->flatMap(function ($units) {
            return $units->invoices;
        })->count();
        $invoices =  $units->flatMap(function ($units) {
            return $units->invoices;
        })->sum('totalamount');
        $payments = $units->flatMap(function ($units) {
            return $units->payments;
        })->sum('totalamount');
        $balance = $invoices - $payments;
        //   $invoicepaid =  $invoices->filter(function ($invoice) {
        //        return $invoice->payments !== null;
        //   })->sum('payments.totalamount');
        //  $invoicePayments = $invoices->withCount('payments')->get();
        //  $paymentCount = $invoicePayments->sum('payments_count');
        // Define the columns for the unit report
        $cards =  [
            'invoiceCount' => ['title' => 'Total Invoices', 'value' => $invoiceCount, 'amount' => '', 'pecentage' => '', 'links' => '/invoice'],
            'unpaidInvoices' => ['title' => 'Unpaid Invoices', 'value' => $unitCount, 'amount' =>'', 'pecentage' => '', 'links' => '/unit'],
            'requests' => ['title' => 'Maintenance Requests', 'value' => '', 'amount' => $payments, 'pecentage' => '', 'links' => '/invoice'],
            'payment' => ['title' => 'Payments Total', 'value' => '', 'amount' => $balance, 'pecentage' => '', 'links' => '/payment'],
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
            'invoicecount' => ['title' => 'Total Invoices', 'value' => $invoiceCount, 'amount' => '', 'pecentage' => '', 'links' => ''],
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
