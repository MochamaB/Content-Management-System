<?php

// app/Services/TableViewDataService.php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Unitcharge;
use App\Models\Unit;
use App\Models\Lease;
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

        /// TABLE DATA ///////////////////////////
        $tableData = [
            'headers' => ['REFERENCE NO', 'INVOICE DATE', 'TYPE', 'AMOUNT DUE', 'AMOUNT PAID', 'ACTIONS'],
            'rows' => [],
        ];

        foreach ($invoicedata as $item) {
            $statusClasses = [
                'paid' => 'badge-active',
                'unpaid' => 'badge-warning',
                'Over Due' => 'badge-danger',
                'partially_paid' => 'badge-secondary',
            ];
            //// GET INVOICE STATUS. IF STATUS UNPAID AND DUEDATE
            $today = Carbon::now();
            if ($item->duedate > $today) {
                $status = $item->status;
            } else {
                $status = 'Over Due';
            }
            $statusClass = $statusClasses[$status] ?? 'badge-secondary';
            $invoiceStatus = '<span class="badge ' . $statusClass . '">' . $status . '</span>';
            $tableData['rows'][] = [
                'id' => $item->id,
                $invoiceStatus . '</br></br> INV#: ' .$item->id.'-'. $item->referenceno,
                '<span class="text-muted" style="font-weight:500;font-style: italic"> Invoice Date  -  Due Date</span></br>' .
                    Carbon::parse($item->created_at)->format('Y-m-d') . ' - ' . Carbon::parse($item->duedate)->format('Y-m-d'),
                $item->invoice_type,
                $item->totalamount,
                $item->payment,


            ];
        }

        return $tableData;
    }
}
