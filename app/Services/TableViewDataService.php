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
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;



class TableViewDataService
{


    public function __construct()
    {
    }

    public function applyDateRangeFilter($query, $month, $year)
    {
        if ($month !== 'ALL') {
            // Get the first and last day of the specified month
            $firstDayOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
            $lastDayOfMonth = Carbon::create($year, $month, 1)->endOfMonth();
            $query->whereBetween('created_at', [$firstDayOfMonth, $lastDayOfMonth]);
        }
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

    public function getInvoiceData($invoicedata, $extraColumns = false)
    {
        // Eager load the 'unit' relationship
        //   $invoicedata->load('user');
        $sitesettings = WebsiteSetting::first();

        /// TABLE DATA ///////////////////////////

        $headers = ['REFERENCE NO', 'INVOICE DATE', 'TYPE', 'AMOUNT DUE', 'PAID', 'BALANCE', 'ACTIONS'];
        // If $Extra columns is true, insert 'Unit Details' at position 3
        if ($extraColumns) {
            array_splice($headers, 2, 0, ['UNIT DETAILS']);
        }

        $tableData = [
            'headers' => $headers,
            'rows' => [],
        ];


        foreach ($invoicedata as $item) {
            //// Status Classes for the Invoices////////
            $statusClasses = [
                'paid' => 'active',
                'unpaid' => 'warning',
                'Over Due' => 'danger',
                'partially_paid' => 'dark',
                'over_paid' => 'light',
            ];
            //// GET INVOICE STATUS. IF STATUS UNPAID AND DUEDATE
            $today = Carbon::now();
            $totalPaid = $item->payments->sum('totalamount');
            $balance = $item->totalamount - $totalPaid;
            $payLink = ''; // Initialize $payLink
            $profpic = url('resources/uploads/images/' . Auth::user()->profilepicture ?? 'avatar.png');

            if ($item->payments->isEmpty()) {
                $status = 'unpaid';
                $payLink = '<a href="' . route('payment.create', ['id' => $item->id]) . '" class="badge badge-information"  style="float: right; margin-right:10px">Add Payment</a>';
            } elseif ($totalPaid < $item->totalamount) {
                $status = 'partially_paid';
                $payLink = '<a href="' . route('payment.create', ['id' => $item->id]) . '" class="badge badge-information" style="float: right; margin-right:10px">Add Payment</a>';
            }  elseif ($totalPaid > $item->totalamount) {
                $status = 'over_paid';
            }   elseif ($totalPaid == $item->totalamount) {
                $status = 'paid';
            }

            if ($item->duedate < $today && $status == 'unpaid') {
                $status = 'Over Due';
            }

            $statusClass = $statusClasses[$status] ?? 'secondary';
            $invoiceStatus = '<span class="badge badge-' . $statusClass . '">' . $status . '</span>';
            $balanceStatus = '<span style ="font-weight:700" class="text-' . $statusClass . '">' . $sitesettings->site_currency . '. ' . $balance . '</span>';

            $row = [
                'id' => $item->id,
                $invoiceStatus . '</br></br> INV#: ' . $item->id . '-' . $item->referenceno,
                '<span class="text-muted" style="font-weight:500;font-style: italic"> Invoice Date  -  Due Date</span></br>' .
                    Carbon::parse($item->created_at)->format('Y-m-d') . ' - ' . Carbon::parse($item->duedate)->format('Y-m-d'),
                $item->invoice_type,
                $sitesettings->site_currency . '. ' . $item->totalamount,
                $sitesettings->site_currency . '. ' . $totalPaid,
                $balanceStatus . '  ' . $payLink,


            ];
            // If $Extra Columns is true, insert unit details at position 3
            if ($extraColumns) {
                array_splice(
                    $row,
                    3,
                    0,
                    '<div class="d-flex "> <img src="' . $profpic . '" alt="">
                <div>
                  <h6>' . $item->unit->unit_number . ' - ' . $item->property->property_name . '</h6>
                <p>' . $item->model->firstname . ' ' . $item->model->lastname .
                        '</p>
                </div>
              </div>'
                ); // Replace with how you get unit details
            }

            $tableData['rows'][] = $row;
        }

        return $tableData;
    }

    ///////////////////
    public function getPaymentVoucherData($paymentvoucherdata, $extraColumns = false)
    {
        // Eager load the 'unit' relationship
        //   $invoicedata->load('user');

        /// TABLE DATA ///////////////////////////

        $headers = ['REFERENCE NO', 'VOUCHER DATE', 'TYPE', 'AMOUNT RECEIVED', 'STATUS', 'ACTIONS'];

        // If $Extra columns is true, insert 'Unit Details' at position 3
        if ($extraColumns) {
            array_splice($headers, 2, 0, ['UNIT DETAILS']);
        }

        $tableData = [
            'headers' => $headers,
            'rows' => [],
        ];

        foreach ($paymentvoucherdata as $item) {
            $row = [
                'id' => $item->id,
                $item->referenceno,
                Carbon::parse($item->created_at)->format('Y-m-d'),
                $item->voucher_type,
                $item->totalamount,
                $item->status,
            ];
            // If $Extra Columns is true, insert unit details at position 3
            if ($extraColumns) {
                array_splice($row, 3, 0, $item->unit->unit_number . ' - ' . $item->property->property_name); // Replace with how you get unit details
            }

            $tableData['rows'][] = $row;
        }

        return $tableData;
    }



    ///////////////
    public function getPaymentData($paymentdata, $extraColumns = false)
    {
        // Eager load the 'unit' relationship
        // $invoicedata->load('user');

        // TABLE DATA
        $headers = ['REFERENCE NO', 'PAYMENT DATE', 'TYPE', 'AMOUNT', 'PAY METHOD', 'ACTIONS'];

        // If $Extra columns is true, insert 'Unit Details' at position 3
        if ($extraColumns) {
            array_splice($headers, 2, 0, ['UNIT DETAILS']);
        }

        $tableData = [
            'headers' => $headers,
            'rows' => [],
        ];

        foreach ($paymentdata as $item) {
            $paymenttype = $item->paymentType;
            $type = $item->model;
            $profpic = url('resources/uploads/images/' . Auth::user()->profilepicture ?? 'avatar.png');
            $row = [
                'id' => $item->id,
                'RCPT#: ' . $item->id . '-' . $item->referenceno,
                '<span class="text-muted" style="font-weight:500;font-style: italic"> Invoice Date  -  Due Date</span></br>' .
                    Carbon::parse($item->created_at)->format('Y-m-d'),
                $type->invoice_type,
                $item->totalamount,
                $paymenttype->name .
                    ' </br>
                <span class="text-muted" style="font-weight:500;font-style: italic"> Payment Code: </span> ' . $item->payment_code,

            ];

            // If $Extra Columns is true, insert unit details at position 3
            if ($extraColumns) {
                array_splice($row, 3, 0, $item->unit->unit_number . ' - ' . $item->property->property_name); // Replace with how you get unit details
            }

            $tableData['rows'][] = $row;
        }

        return $tableData;
    }




    ////////////////

    public function getUnitChargeData($unitchargedata, $extraColumns = false)
    {

        $headers = ['CHARGE', 'CYCLE', 'TYPE', 'RATE', 'RECURRING', 'LAST BILLED', 'NEXT BILL DATE', 'ACTIONS'];

        // If $Extra columns is true, insert 'Unit Details' at position 3
        if ($extraColumns) {
            array_splice($headers, 0, 0, ['UNIT DETAILS']);
        }

        $tableData = [
            'headers' => $headers,
            'rows' => [],
        ];

        foreach ($unitchargedata as $item) {
            $nextDateFormatted = empty($item->nextdate) ? 'Charged Once' : Carbon::parse($item->nextdate)->format('Y-m-d');
            $charge_name = $item->charge_name;
            $charge_cycle = $item->charge_cycle;
            $charge_type = $item->charge_type;
            $rate = $item->rate;
            $recurring_charge = $item->recurring_charge;
            $updated_at = \Carbon\Carbon::parse($item->updated_at)->format('d M Y');
            $unit = $item->unit->unit_number;
            $property = $item->property->property_name;

            // If the current charge has child charges, add them to the charge name
            if ($item->childrencharge->isNotEmpty()) {
                foreach ($item->childrencharge as $child) {
                    $charge_name .= ' <br><i class=" mdi mdi-subdirectory-arrow-right mdi-24px text-primary">' . $child->charge_name . '</li>';
                    $charge_cycle .= ' <br></br><li class="text-primary">' . $child->charge_cycle . '</li>';
                    $charge_type .= ' <br></br><li class="text-primary">' . $child->charge_type . '</li>';
                    $rate .= ' <br></br><li class="text-primary">' . $child->rate . '</li>';
                }
            }
            $row = [
                'id' => $item->id,
                $charge_name,
                $charge_cycle,
                $charge_type,
                $rate,
                $item->recurring_charge,
                \Carbon\Carbon::parse($item->updated_at)->format('d M Y'),
                $nextDateFormatted,

            ];

            // If $Extra Columns is true, insert unit details at position 3
            if ($extraColumns) {
                array_splice($row, 1, 0, $item->unit->unit_number . ' - ' . $item->property->property_name); // Replace with how you get unit details
            }

            $tableData['rows'][] = $row;
        }

        return $tableData;
    }

    ////////////
    public function getMeterReadingsData($meterReadings, $extraColumns = false)
    {

        $headers = ['UNIT', 'CHARGE', 'PREVIOUS READING', 'CURRENT', 'USAGE', 'RATE', 'AMOUNT', 'ACTIONS'];

        // If $Extra columns is true, insert 'Unit Details' at position 3
        if ($extraColumns) {
            array_splice($headers, 0, 0, ['UNIT DETAILS']);
        }

        $tableData = [
            'headers' => $headers,
            'rows' => [],
        ];

        foreach ($meterReadings as $item) {
            $startFormatted = empty($item->startdate) ? 'Not set' : Carbon::parse($item->startdate)->format('Y-m-d');
            $enddateFormatted = empty($item->enddate) ? 'Not set' : Carbon::parse($item->enddate)->format('Y-m-d');
            $usage =  $item->currentreading - $item->lastreading;
            $amount = $usage *  $item->rate_at_reading;

            $row = [
                'id' => $item->id,
                $item->unit->unit_number,
                $item->unitcharge->charge_name,
                $item->lastreading . '</br></br><span class="text-muted" style="font-weight:500;font-style: italic">' .
                    $startFormatted . '</span>',
                $item->currentreading . '</br></br><span class="text-muted" style="font-weight:500;font-style: italic">' .
                    $enddateFormatted . '</span>',
                $usage,
                $item->rate_at_reading,
                $amount,
            ];
            // If $Extra Columns is true, insert unit details at position 3
            if ($extraColumns) {
                array_splice($row, 3, 0, $item->unit->unit_number . ' - ' . $item->property->property_name); // Replace with how you get unit details
            }

            $tableData['rows'][] = $row;
        }


        return $tableData;
    }
}
