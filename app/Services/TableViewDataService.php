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
use App\Models\Property;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\View;



class TableViewDataService
{

    public $sitesettings;
    public $user;
    protected $badgeClasses = [
        'Completed' => 'active',
        'New' => 'warning',
        'OverDue' => 'error',
        'In Progress' => 'information',
        'Assigned' => 'dark',

        'critical' => 'error',
        'high' => 'warning',
        'normal' => 'active',
        'low' => 'dark',

        'paid' => 'active',
        'unpaid' => 'warning',
        'Over Due' => 'danger',
        'partially_paid' => 'dark',
        'over_paid' => 'light',
    ];

    public function __construct()
    {
        $this->sitesettings = WebsiteSetting::first();
        $this->user = Auth::user();
    }


    public function getBadgeClass($status)
    {
        return $this->badgeClasses[$status] ?? 'active';
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

    public function applyPaymentDateRangeFilter($query, $month, $year)
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

        $headers = ['REFERENCE NO', ' DATES', 'TYPE', 'AMOUNT DUE', 'PAID', 'BALANCE', 'ACTIONS'];
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
            $mediaURL = $item->model->getFirstMediaUrl('avatar');
            if ($mediaURL) {
                $profpic = url($item->getFirstMediaUrl('avatar'));
            } else {
                $profpic = url('uploads/images/avatar.png');
            }
            //   $profpic = url('resources/uploads/images/' . Auth::user()->profilepicture ?? 'avatar.png');

            if ($item->payments->isEmpty()) {
                $status = 'unpaid';
                $payLink = '<a href="' . route('payment.create', ['id' => $item->id]) . '" class="badge badge-information"  style="float: right; margin-right:10px">Add Payment</a>';
            } elseif ($totalPaid < $item->totalamount) {
                $status = 'partially_paid';
                $payLink = '<a href="' . route('payment.create', ['id' => $item->id]) . '" class="badge badge-information" style="float: right; margin-right:10px">Add Payment</a>';
            } elseif ($totalPaid > $item->totalamount) {
                $status = 'over_paid';
            } elseif ($totalPaid == $item->totalamount) {
                $status = 'paid';
            }

            if ($item->duedate < $today && $status == 'unpaid') {
                $status = 'Over Due';
            }

            $statusClass = $statusClasses[$status] ?? 'secondary';
            $invoiceStatus = '<span class="badge badge-' . $statusClass . '">' . $status . '</span>';
            $balanceStatus = '<span style ="font-weight:700" class="text-' . $statusClass . '">' . $sitesettings->site_currency . '. ' . number_format($balance, 0, '.', ',') . '</span>';
            if (!empty($item->payments)) {
                $receipttext = '</br><span class="text-muted" style="margin-top:5px;font-weight:500;"> Receipts</span>';
            } else {
                $receipttext = '';
            }
            $paymentLinks = '';
            $paymentCounter = 1;

            foreach ($item->payments as $payment) {
                $id = $payment->id;
                $refn = $payment->referenceno;
                $paymentLinks .= '<br> <a href="' . url('payment/' . $id) . '" class="badge badge-light">' . $id . ' ' . $refn . '</a>';
                $paymentCounter++;
            }

            $row = [
                'id' => $item->id,
                $invoiceStatus . '</br></br>' . $item->referenceno . '-' . $item->id,
                '<span class="text-muted" style="font-weight:500;font-style: italic"> Issue Date  -  Due Date</span></br>' .
                    Carbon::parse($item->created_at)->format('Y-m-d') . ' - ' . Carbon::parse($item->duedate)->format('Y-m-d'),
                $item->name,
                $sitesettings->site_currency . '. ' . number_format($item->totalamount, 2, '.', ','),
                $sitesettings->site_currency . '. ' . number_format($totalPaid, 2, '.', ',') . $receipttext . '' . $paymentLinks,
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
    public function getDepositData($Depositdata, $extraColumns = false)
    {
        // Eager load the 'unit' relationship
        //   $invoicedata->load('user');

        /// TABLE DATA ///////////////////////////

        $headers = ['REFERENCE NO', 'STATUS | DUEDATE', 'NAME', 'AMOUNT RECEIVED', 'PAID', 'BALANCE', 'ACTIONS'];

        // If $Extra columns is true, insert 'Unit Details' at position 3
        if ($extraColumns) {
            array_splice($headers, 1, 0, ['UNIT DETAILS']);
        }

        $tableData = [
            'headers' => $headers,
            'rows' => [],
        ];

        foreach ($Depositdata as $item) {
            $statusClasses = [
                'paid' => 'active',
                'unpaid' => 'warning',
                'Over Due' => 'danger',
                'partially_paid' => 'dark',
                'over_paid' => 'light',
            ];
            $today = Carbon::now();
            $totalPaid = $item->payments->sum('totalamount');
            $balance = $item->totalamount - $totalPaid;
            $payLink = ''; // Initialize $payLink
            if ($item->payments->isEmpty()) {
                $status = 'unpaid';
                $payLink = '<a href="' . route('payment.create', ['id' => $item->id, 'model' => class_basename($item)]) . '" class="badge badge-information"  style="float: right; margin-right:10px">Add Payment</a>';
            } elseif ($totalPaid < $item->totalamount) {
                $status = 'partially_paid';
                $payLink = '<a href="' . route('payment.create', ['id' => $item->id, 'model' => class_basename($item)]) . '" class="badge badge-information" style="float: right; margin-right:10px">Add Payment</a>';
            } elseif ($totalPaid > $item->totalamount) {
                $status = 'over_paid';
            } elseif ($totalPaid == $item->totalamount) {
                $status = 'paid';
            }

            if ($item->duedate !== null && $item->duedate < $today && $status == 'unpaid') {
                $status = 'Over Due';
            }
            //        $status = $item->status;
            $statusClass = $statusClasses[$status] ?? 'unpaid';
            $voucherStatus = '<span class="badge badge-' . $statusClass . '">' . $status . '</span>';
            $balanceStatus = '<span style ="font-weight:700" class="text-' . $statusClass . '">' . $this->sitesettings->site_currency . '. ' . number_format($balance, 0, '.', ',') . '</span>';
            if ($item->duedate !== null) {
                $dueDate = Carbon::parse($item->duedate)->format('Y-m-d');
            } else {
                $dueDate = "";
            }



            $row = [
                'id' => $item->id,
                $item->referenceno,
                $voucherStatus . ' </br></br>' . $dueDate,
                $item->name,
                $this->sitesettings->site_currency . ' ' . number_format($item->totalamount, 0, '.', ','),
                $this->sitesettings->site_currency . ' ' . number_format($totalPaid, 0, '.', ','),
                $balanceStatus . ' </br></br> ' . $payLink,
            ];
            // If $Extra Columns is true, insert unit details at position 3
            if ($extraColumns) {
                if ($item->unit) {
                    array_splice($row, 2, 0, $item->unit->unit_number . ' - ' . $item->property->property_name);
                } else {
                    array_splice($row, 2, 0, $item->property->property_name);
                } // Replace with how you get unit details
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
        $headers = ['REFERENCE NO', 'PAYMENT DATE', 'TYPE', 'DESCRIPTION', 'AMOUNT', 'PAY METHOD', 'ACTIONS'];

        // If $Extra columns is true, insert 'Unit Details' at position 3
        if ($extraColumns) {
            array_splice($headers, 2, 0, ['UNIT DETAILS']);
        }

        $tableData = [
            'headers' => $headers,
            'rows' => [],
        ];

        foreach ($paymentdata as $item) {
            $PaymentMethod = $item->PaymentMethod;
            $type = $item->paymentItems->first();
            $profpic = url('resources/uploads/images/' . Auth::user()->profilepicture ?? 'avatar.png');
            $row = [
                'id' => $item->id,
                'RCPT#: ' . $item->id . '-' . $item->referenceno,
                '<span class="text-muted" style="font-weight:500;font-style: italic"> Paid on Date</span></br>' .
                    Carbon::parse($item->created_at)->format('Y-m-d'),
                class_basename($item->model),
                $type->charge_name ?? $item->model->name,
                $this->sitesettings->site_currency . ' ' . number_format($item->totalamount, 0, '.', ','),
                $PaymentMethod->name .
                    ' </br>
                <span class="text-muted" style="font-weight:500;font-style: italic"> Payment Code: </span> ' . $item->payment_code,

            ];

            // If $Extra Columns is true, insert unit details at position 3
            if ($extraColumns) {
                array_splice($row, 3, 0, $item->unit->unit_number ?? '' . ' - ' . $item->property->property_name); // Replace with how you get unit details
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
            if ($charge_type === 'fixed') {
                $rate = $this->sitesettings->site_currency . ' ' . number_format($item->rate, 0, '.', ',');
            } else {
                $rate = $this->sitesettings->site_currency . ' ' . number_format($item->rate, 0, '.', ',') . ' <i>Per Unit</i>';
            }
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
                \Carbon\Carbon::parse($item->updated_at)->format('Y-m-d'),
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
            array_splice($headers, 2, 0, ['UNIT DETAILS']);
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
                $usage . ' units ',
                $item->rate_at_reading,
                $this->sitesettings->site_currency . '. ' . number_format($amount, 0, '.', ','),
            ];
            // If $Extra Columns is true, insert unit details at position 3
            if ($extraColumns) {
                array_splice($row, 3, 0, $item->unit->unit_number . ' - ' . $item->property->property_name); // Replace with how you get unit details
            }

            $tableData['rows'][] = $row;
        }


        return $tableData;
    }

    ///////////////////
    public function getMediaData($mediadata)
    {
        // Eager load the 'unit' relationship
        //   $invoicedata->load('user');

        /// TABLE DATA ///////////////////////////

        $headers = ['NO', 'THUMBNAIL', 'NAME', 'CATEGORY', 'COLLECTION', 'SIZE', 'ACTIONS'];

        $tableData = [
            'headers' => $headers,
            'rows' => [],
        ];
        $allMedia = collect();

        foreach ($mediadata as $key => $item) {
            $model_type = $item->model_type;
            $parts = explode('\\', $model_type);
            $category = end($parts);
            if ($item->mime_type === 'application/pdf') {
                $thumbnail = url('uploads/pdf.png');
            } else {
                $thumbnail = $item->getUrl();
            }
            $row = [
                'id' => $item->id,
                $key + 1,
                '<img src="' . $thumbnail . '" alt="' . $item->name . '" style="width:130px;height:80px;border-radius:0">',
                $item->file_name,
                $category,
                $item->collection_name,
                $item->size,

            ];
            // If $Extra Columns is true, insert unit details at position 3

            $tableData['rows'][] = $row;
        }

        return $tableData;
    }


    public function getUserData($userdata, $extraColumns = false)
    {

        /// TABLE DATA ///////////////////////////

        $headers = ['NAME', 'ROLE', 'EMAIL', 'ACCOUNT STATUS', 'ACTIONS'];

        // If $Extra columns is true, insert 'Unit Details' at position 3
        if ($extraColumns) {
            array_splice($headers, 2, 0, ['DETAILS']);
        }

        $tableData = [
            'headers' => $headers,
            'rows' => [],
        ];

        foreach ($userdata as $item) {
            $unit = $item->units->first();
            //  $property = $item->properties->first();
            $roleNames = $item->roles->first();
            $addlease = '<a href="' . route('lease.create') . '" class="badge badge-warning"  style="float: left; margin-right:10px">Add Lease</a>';
            $mediaURL = $item->getFirstMediaUrl('avatar');
            if ($mediaURL) {
                $profpic = url($item->getFirstMediaUrl('avatar'));
            } else {
                $profpic = url('uploads/images/avatar.png');
            }
            //url('resources/uploads/images/' . Auth::user()->profilepicture ?? 'avatar.png');
            $name =     '<div class="d-flex "> <img src="' . $profpic . '" alt="">
            <div>
            <h6>' . $item->firstname . ' ' . $item->lastname .
                '</h6>
            </div>
          </div>';

            $row = [
                'id' => $item->id,
                $name,
                $roleNames->name ?? 'Super Admin',
                $item->email,
                $item->status,

            ];
            // If $Extra Columns is true, insert unit details at position 3
            if ($extraColumns) {
                if ($unit) {
                    array_splice($row, 3, 0, $unit->property->property_name . ' - ' . $unit->unit_number);
                } else {
                    // Add default value when the condition is not met
                    array_splice($row, 3, 0, $addlease); // Replace 'Default Value' with your desired default
                }
            }
            $tableData['rows'][] = $row;
        }

        return $tableData;
    }


    public function getvendorData($vendordata, $extraColumns = false)
    {

        /// TABLE DATA ///////////////////////////

        $headers = ['NAME', 'EMAIL', 'PHONE NUMBER', 'ACCOUNT STATUS', 'ACTIONS'];

        // If $Extra columns is true, insert 'Unit Details' at position 3
        if ($extraColumns) {
            array_splice($headers, 2, 0, ['PROPERTY']);
        }

        $tableData = [
            'headers' => $headers,
            'rows' => [],
        ];

        foreach ($vendordata as $item) {
            //  $property = $item->properties->first();
            //$subscriptionStatus = $item->vendorSubscription->first();

            $profpic = url('uploads/images/' . Auth::user()->profilepicture ?? 'avatar.png');
            $name =     '<div class="d-flex "> <img src="' . $profpic . '" alt="">
            <div>
            <h6>' . $item->name .
                '</h6>
            </div>
          </div>';

            $row = [
                'id' => $item->id,
                $name,
                $item->email,
                $item->phonenumber,
                $item->vendorSubscription->subscription_status,

            ];
            // If $Extra Columns is true, insert unit details at position 3
            if ($extraColumns) {
                array_splice($row, 3, 0, $item->property->property_name); // Replace with how you get unit details
            }
            $tableData['rows'][] = $row;
        }

        return $tableData;
    }

    public function getTicketData($ticketdata, $extraColumns = false)
    {

        /// TABLE DATA ///////////////////////////

        $headers = ['STATUS', ' CATEGORY', 'SUBJECT', 'DURATION', 'RAISED BY', 'PRIORITY', 'ASSIGNED', 'AMOUNT', 'ACTIONS'];

        // If $Extra columns is true, insert 'Unit Details' at position 3
        if ($extraColumns) {
            array_splice($headers, 2, 0, ['PROPERTY']);
        }

        $tableData = [
            'headers' => $headers,
            'rows' => [],
        ];

        foreach ($ticketdata as $item) {
            $statusClasses = [
                'Completed' => 'active',
                'New' => 'warning',
                'OverDue' => 'error',
                'In Progress' => 'information',
                'Assigned' => 'dark',
            ];

            $status = $item->status;
            $statusClass = $statusClasses[$status] ?? 'Reported';
            $priority = $item->priority;
            $priorityClass = $this->getBadgeClass($priority);

            $priorityStatus = '<span class="badge badge-' . $priorityClass . '">' . $priority . '</span>';
            $requestStatus = '<span class="statusdot statusdot-' . $statusClass . '"></span>';
            $roleNames = $item->users->roles->first();
            $raisedby =  $item->users->firstname . ' ' . $item->users->lastname;
            $url = url('ticket/assign/' . $item->id);
            if (Auth::user()->can('work-order.create') || Auth::user()->id === 1) {
                $assignLink = '<a href="' .  $url . '" class="badge badge-information"><i class="mdi mdi-lead-pencil mdi-12px text-primary">ASSIGN</i>  </a>';
            } else {
                // Empty link or any other fallback content
                $assignLink = '<a href="" class="badge badge-warning"><i class="mdi mdi-lead-pencil mdi-12px text-warning"> NOT ASSIGNED</i>  </a>';
            }
            $assignedType = $item->assigned_type;
            $assignedModel = $item->assigned;
            ////CHECK IF ITS A VENDOR OR A USER STAFF
            if ($assignedType === 'App\\Models\\User') {
                $assigned = $assignedModel->firstname . ' ' . $assignedModel->lastname;
            } elseif ($assignedType === 'App\\Models\\Vendor') {
                $assigned = $assignedModel->name;
            } else {
                $assigned =   $assignLink;
            }

            if ($item->unit) {
                $unit = ' - ' . $item->unit->unit_number;
            }
            $createdAt = Carbon::parse($item->created_at);
            // Get the current date
            $currentDate = Carbon::now();
            // Calculate the difference in days
            $ageInDays = $createdAt->diffInDays($currentDate);
            // Now, you can check the type of the assigned model and access its attributes accordingly

            $row = [
                'id' => $item->id,
                $requestStatus . ' ' . $status,
                $item->category,
                $item->subject
                    . '</br><span class="text-muted" style="font-weight:500;font-style: italic">' . $item->property->property_name . ' ' . $unit . '</span>',
                Carbon::parse($item->created_at)->format('Y-m-d')
                    . '</br><span class="text-muted" style="font-weight:500;font-style: italic">' . $ageInDays . ' Days </span>',
                $raisedby
                    . '</br><span class="text-muted" style="font-weight:500;font-style: italic">' . ($roleNames ? $roleNames->name : '') . '</span>',
                $priorityStatus,
                $assigned ?? $assignLink,
                $this->sitesettings->site_currency . ' ' . number_format($item->totalamount, 0, '.', ','),

            ];
            // If $Extra Columns is true, insert unit details at position 3
            if ($extraColumns) {
                array_splice($row, 3, 0, $item->property->property_name); // Replace with how you get unit details
            }
            $tableData['rows'][] = $row;
        }

        return $tableData;
    }


    public function getGeneralLedgerData($ledgerdata, $extraColumns = false)
    {
        // Eager load the 'unit' relationship
        //   $invoicedata->load('user');

        /// TABLE DATA ///////////////////////////

        $headers = ['NO', 'DATE', 'DESCRIPTION', 'TYPE', 'REFERENCE', 'DEBIT', 'CREDIT', 'AMOUNT', 'TOTAL CREDIT'];

        // If $Extra columns is true, insert 'Unit Details' at position 3
        if ($extraColumns) {
            array_splice($headers, 3, 0, ['DETAILS']);
        }

        $tableData = [
            'headers' => $headers,
            'rows' => [],
        ];

        foreach ($ledgerdata as $item) {

            $row = [
                'id' => $item->id,
                Carbon::parse($item->created_at)->format('Y-m-d'),
                $item->charge_name,
                class_basename($item->transactionable_type),
                $item->transactionable->referenceno,
                $item->debitAccount->account_name,
                $item->creditAccount->account_name,
                $item->amount,

            ];
            if ($extraColumns) {
                array_splice($row, 3, 0, $item->property->property_name); // Replace with how you get unit details
            }
            $tableData['rows'][] = $row;
        }

        return $tableData;
    }

    public function getWorkOrderExpenseData($expensedata, $extraColumns = false)
    {
        // Eager load the 'unit' relationship
        //   $invoicedata->load('user');

        /// TABLE DATA ///////////////////////////

        $headers = ['NO', 'DATE', 'ITEM DESCRIPTION', 'QUANTITY', 'PRICE', 'AMOUNT', 'ACTION'];

        // If $Extra columns is true, insert 'Unit Details' at position 3
        if ($extraColumns) {
            array_splice($headers, 3, 0, ['DETAILS']);
        }

        $tableData = [
            'headers' => $headers,
            'rows' => [],
        ];

        foreach ($expensedata as $key => $item) {

            $row = [
                'id' => $item->id,
                $key + 1,
                Carbon::parse($item->created_at)->format('Y-m-d'),
                $item->item,
                $item->quantity,
                $this->sitesettings->site_currency . ' ' . number_format($item->price, 0, '.', ','),
                $this->sitesettings->site_currency . ' ' . number_format($item->amount, 0, '.', ','),

            ];
            if ($extraColumns) {
                array_splice($row, 3, 0, $item->property->property_name); // Replace with how you get unit details
            }
            $tableData['rows'][] = $row;
        }

        return $tableData;
    }


    public function getSettingData($settingdata, $extraColumns = false)
    {

        /// TABLE DATA ///////////////////////////

        $headers = ['NAME', 'KEY', 'VALUE', 'ACTIONS'];

        // If $Extra columns is true, insert 'Unit Details' at position 3
        if ($extraColumns) {
            array_splice($headers, 2, 0, ['PROPERTY']);
        }

        $tableData = [
            'headers' => $headers,
            'rows' => [],
        ];

        foreach ($settingdata as $item) {

            $row = [
                'id' => $item->id,
                $item->name,
                $item->key,
                $item->value,
                $item->description,

            ];
            // If $Extra Columns is true, insert unit details at position 3
            if ($extraColumns) {
                array_splice($row, 3, 0, $item->property->property_name); // Replace with how you get unit details
            }
            $tableData['rows'][] = $row;
        }

        return $tableData;
    }

    public function generateSettingTabContents($modelType, $setting)
    {

        $tabTitles = collect([
            'Global Settings',
            'Overrides',
        ]);
        $controller = 'setting';
        $globalSettings = Setting::where('model_type', $modelType)
            ->whereNull('model_id')
            ->get();

        $individualSetting = Setting::where('model_type', $modelType)
            ->whereNotNull('model_id')
            ->get();

        $settingsTableData = $this->getSettingData($individualSetting, true);
        $id = $setting->model_type;

        $tabContents = [];

        foreach ($tabTitles as $title) {
            if ($title === 'Global Settings') {
                $tabContents[] = View('admin.setting.global_settings', compact('setting', 'globalSettings'))->render();
            } elseif ($title === 'Overrides') {
                $tabContents[] = View('admin.CRUD.index_show', ['tableData' => $settingsTableData, 'controller' => ['setting']], compact('id'))->render();
            }
        }

        return [
            'tabTitles' => $tabTitles,
            'tabContents' => $tabContents,
        ];
    }

    public function getExpenseData($expensedata, $extraColumns = false)
    {

        /// TABLE DATA ///////////////////////////

        $headers = ['REFNO', 'STATUS | DUEDATE', 'NAME','AMOUNT DUE', 'PAID', 'BALANCE', 'ACTIONS'];

        // If $Extra columns is true, insert 'Unit Details' at position 3
        if ($extraColumns) {
            array_splice($headers, 2, 0, ['PROPERTY']);
        }

        $tableData = [
            'headers' => $headers,
            'rows' => [],
        ];

        foreach ($expensedata as $item) {
            $statusClasses = [
                'paid' => 'active',
                'unpaid' => 'warning',
                'Over Due' => 'danger',
                'partially_paid' => 'dark',
                'over_paid' => 'light',
            ];

            $today = Carbon::now();
            $totalPaid = $item->payments->sum('totalamount');
            $balance = $item->totalamount - $totalPaid;
            $payLink = ''; // Initialize $payLink

            if ($item->payments->isEmpty()) {
                $status = 'unpaid';
                $payLink = '<a href="' . route('payment.create', ['id' => $item->id, 'model' => class_basename($item)]) . '" class="badge badge-information"  style="float: right; margin-right:10px">Add Payment</a>';
            } elseif ($totalPaid < $item->totalamount) {
                $status = 'partially_paid';
                $payLink = '<a href="' . route('payment.create', ['id' => $item->id, 'model' => class_basename($item)]) . '" class="badge badge-information" style="float: right; margin-right:10px">Add Payment</a>';
            } elseif ($totalPaid > $item->totalamount) {
                $status = 'over_paid';
            } elseif ($totalPaid == $item->totalamount) {
                $status = 'paid';
            }

            if ($item->duedate < $today && $status == 'unpaid') {
                $status = 'Over Due';
            }
            //        $status = $item->status;
            $statusClass = $statusClasses[$status] ?? 'unpaid';
            $expenseStatus = '<span class="badge badge-' . $statusClass . '">' . $status . '</span>';
            $balanceStatus = '<span style ="font-weight:700" class="text-' . $statusClass . '">' . $this->sitesettings->site_currency . '. ' . number_format($balance, 0, '.', ',') . '</span>';



            $row = [
                'id' => $item->id,
                $item->referenceno,
                $expenseStatus . ' </br></br>' . Carbon::parse($item->duedate)->format('Y-m-d'),
                $item->name,
                $this->sitesettings->site_currency . ' ' . number_format($item->totalamount, 0, '.', ','),
                $this->sitesettings->site_currency . ' ' . number_format($totalPaid, 0, '.', ','),
                $balanceStatus . ' </br></br> ' . $payLink,

            ];
            // If $Extra Columns is true, insert unit details at position 3
            if ($extraColumns) {
                array_splice($row, 3, 0, $item->property->property_name); // Replace with how you get unit details
            }
            $tableData['rows'][] = $row;
        }

        return $tableData;
    }
}
