<?php

// app/Services/TableViewDataService.php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Unitcharge;
use App\Models\Unit;
use App\Models\Lease;
use App\Models\WebsiteSetting;
use App\Models\MeterReading;
use App\Models\InvoiceItems;
use Illuminate\Http\Request;
use Carbon\Carbon;



class TableViewDataService
{


    public function __construct()
    {
    }

    public function getUnitData($unitdata)
    {
        /// TABLE DATA ///////////////////////////
        $tableData = [
            'headers' => ['UNIT', 'TYPE', 'BEDS', 'BATHS', 'LEASE', 'ACTIONS'],
            'rows' => [],
        ];

        foreach ($unitdata as $item) {
            $leaseStatus = $item->lease ? '<span class="badge badge-active">Active</span>' : '<span class="badge badge-danger">No Lease</span>';
            $tableData['rows'][] = [
                'id' => $item->id,
                $item->unit_number . ' - ' . $item->property->property_name . '(' . $item->property->property_location . ')',
                $item->unit_type,
                $item->bedrooms,
                $item->bathrooms,
                $leaseStatus,
            ];
        }

        return $tableData;
    }

    /////METHOD TO POPULATE INVOICE TABLE

    public function getInvoiceData($invoicedata)
    {
        // Eager load the 'unit' relationship
        //   $invoicedata->load('user');
        $sitesettings = WebsiteSetting::first();

        /// TABLE DATA ///////////////////////////
        $tableData = [
            'headers' => ['REFERENCE NO', 'INVOICE DATE', 'TYPE', 'AMOUNT DUE', 'PAID','BALANCE', 'ACTIONS'],
            'rows' => [],
        ];

        foreach ($invoicedata as $item) {
            //// Status Classes for the Invoices////////
            $statusClasses = [
                'paid' => 'active',
                'unpaid' => 'warning',
                'Over Due' => 'danger',
                'partially_paid' => 'secondary',
            ];
            //// GET INVOICE STATUS. IF STATUS UNPAID AND DUEDATE
            $today = Carbon::now();
            $totalPaid = $item->payments->sum('totalamount');
            $balance = $item->totalamount - $totalPaid;
            $payLink = ''; // Initialize $payLink

            if ($item->payments->isEmpty()) {
                $status = 'unpaid';
                $payLink = '<a href="' . route('payment.create', ['id' => $item->id]) . '" class="badge badge-information"  style="float: right; margin-right:10px">Add Payment</a>';
            } elseif ($totalPaid < $item->totalamount) {
                $status = 'partially_paid';
                $payLink = '<a href="' . route('payment.create', ['id' => $item->id]) . '" class="badge badge-information" style="float: right; margin-right:10px">Add Payment</a>';
            } elseif ($totalPaid == $item->totalamount) {
                $status = 'paid';
            }

            if ($item->duedate < $today && $status == 'unpaid') {
                $status = 'Over Due';
            }

            $statusClass = $statusClasses[$status] ?? 'secondary';
            $invoiceStatus = '<span class="badge badge-' .$statusClass . '">' . $status . '</span>';
            $balanceStatus = '<span style ="font-weight:700" class="text-' .$statusClass . '">' . $sitesettings->site_currency.'. '.$balance . '</span>';

                $tableData['rows'][] = [
                    'id' => $item->id,
                    $invoiceStatus . '</br></br> INV#: ' . $item->id . '-' . $item->referenceno,
                    '<span class="text-muted" style="font-weight:500;font-style: italic"> Invoice Date  -  Due Date</span></br>' .
                        Carbon::parse($item->created_at)->format('Y-m-d') . ' - ' . Carbon::parse($item->duedate)->format('Y-m-d'),
                    $item->invoice_type,
                    $sitesettings->site_currency.'. '.$item->totalamount,
                    $sitesettings->site_currency.'. '.$totalPaid,
                    $balanceStatus.'  ' .$payLink,
                   

                ];
        }

        return $tableData;
    }

    ///////////////////
    public function getPaymentData($paymentdata)
    {
        // Eager load the 'unit' relationship
        //   $invoicedata->load('user');

        /// TABLE DATA ///////////////////////////
        $tableData = [
            'headers' => ['REFERENCE NO', 'PAYMENT DATE', 'TYPE', 'PAYMENT TYPE', 'AMOUNT PAID', 'ACTIONS'],
            'rows' => [],
        ];

        foreach ($paymentdata as $item) {
            $paymenttype = $item->paymentType;
            $type =$item->model;
            $tableData['rows'][] = [
                'id' => $item->id,
                'RCPT#: ' . $item->id . '-' . $item->referenceno,
                '<span class="text-muted" style="font-weight:500;font-style: italic"> Invoice Date  -  Due Date</span></br>' .
                    Carbon::parse($item->created_at)->format('Y-m-d'),
                $type->invoice_type,
                $paymenttype->name.
                ' </br>
                <span class="text-muted" style="font-weight:500;font-style: italic"> Payment Code: </span> '.$item->payment_code,
                $item->totalamount,
            ];
        }

        return $tableData;
    }




}
