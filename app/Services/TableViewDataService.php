<?php

// app/Services/TableViewDataService.php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Unitcharge;
use App\Models\Unit;
use App\Models\Lease;
use App\Models\Website;
use App\Models\MeterReading;
use App\Models\InvoiceItems;
use App\Models\Property;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\View;
use Illuminate\Database\Eloquent\Model;



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
        $this->sitesettings = Website::first();
        $this->user = Auth::user();
    }


    public function getBadgeClass($status)
    {
        return $this->badgeClasses[$status] ?? 'active';
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
            $isDeleted = $item->deleted_at !== null;
            $tableData['rows'][] = [
                'id' => $item->id,
                $item->unit_number . ' - ' . $item->property->property_name . '(' . $item->property->property_location . ')',
                $item->unit_type,
                $item->bedrooms,
                $item->bathrooms,
                $leaseStatus,
                'isDeleted' => $isDeleted,
            ];
        }

        return $tableData;
    }

    /////METHOD TO POPULATE INVOICE TABLE

    public function getInvoiceData($invoicedata, $extraColumns = false)
    {
        // Eager load the 'unit' relationship
        //   $invoicedata->load('user');

        /// TABLE DATA ///////////////////////////

        $headers = ['REFNO','AMOUNT','STATUS', ' DATE', 'TYPE', 'PAID', 'BALANCE', 'ACTIONS'];
        // If $Extra columns is true, insert 'Unit Details' at position 5
        if ($extraColumns) {
            array_splice($headers, 4, 0, ['BILLING DETAILS']);
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
                $payLink = '<a href="' . route('payment.create', ['id' => $item->id]) . '" class="badge badge-information mt-2">Record Payment</a>';
            } elseif ($totalPaid < $item->totalamount) {
                $status = 'partially_paid';
                $payLink = '<a href="' . route('payment.create', ['id' => $item->id]) . '" class="badge badge-information mt-2">Record Payment</a>';
            } elseif ($totalPaid > $item->totalamount) {
                $status = 'over_paid';
            } elseif ($totalPaid == $item->totalamount) {
                $status = 'paid';
            }

            if ($item->duedate < $today && $status == 'unpaid') {
                $status = 'Over Due';
            }

            $statusClass = $statusClasses[$status] ?? 'secondary';
            $invoiceStatus = '  <span class="badge badge-' . $statusClass . '">' . $status . '</span>';
            $balanceStatus = '  <span style ="font-weight:700" class="text-' . $statusClass . '">' . $this->sitesettings->site_currency . '. ' . number_format($balance, 0, '.', ',') . '</span>';
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
            $isDeleted = $item->deleted_at !== null;

            $row = [
                'id' => $item->id,
                $item->referenceno,
                $this->sitesettings->site_currency . '. ' . number_format($item->totalamount, 0, '.', ','),
                $invoiceStatus,
                Carbon::parse($item->created_at)->format('F Y'),
                $item->name,
                $this->sitesettings->site_currency . '. ' . number_format($totalPaid, 0, '.', ',') . $receipttext . '' . $paymentLinks,
                $balanceStatus . ' </br>' . $payLink,
                'isDeleted' => $isDeleted,


            ];


            // If $Extra Columns is true, insert unit details at position 5
            if ($extraColumns) {
                array_splice(
                    $row,
                    5,
                    0,
                    '<div class="d-flex "> <img src="' . $profpic . '" alt="">
                <div>
                  <h6> ' . $item->property->property_name . ' - ' . $item->unit->unit_number . '</h6>
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
            $isDeleted = $item->deleted_at !== null;


            $row = [
                'id' => $item->id,
                $item->referenceno,
                $voucherStatus . ' </br></br>' . $dueDate,
                $item->name,
                $this->sitesettings->site_currency . ' ' . number_format($item->totalamount, 0, '.', ','),
                $this->sitesettings->site_currency . ' ' . number_format($totalPaid, 0, '.', ','),
                $balanceStatus . ' </br></br> ' . $payLink,
                'isDeleted' => $isDeleted,
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
            $PaymentMethod = $item->PaymentMethod;
            $isDeleted = $item->deleted_at !== null;

            $row = [
                'id' => $item->id,
                $item->referenceno . ' - #' . $item->id,
                '<span class="text-muted" style="font-weight:500;font-style: italic"> Paid on Date</span></br>' .
                    Carbon::parse($item->created_at)->format('Y-m-d'),
                class_basename($item->model),
                $this->sitesettings->site_currency . ' ' . number_format($item->totalamount, 0, '.', ','),
                $PaymentMethod->name .
                    ' </br>
                <span class="text-muted" style="font-weight:500;font-style: italic"> Payment Code: </span> ' . $item->payment_code,
                'isDeleted' => $isDeleted,

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
            $MeterReadingLink = ''; // Initialize $MeterReadingLink here
            $nextdateFormatted = Carbon::parse($item->nextdate)->format('Y-m-d');
            $updatedFormatted = Carbon::parse($item->updated_at ?? Carbon::now())->format('Y-m-d');
            if ($item->charge_type == 'units') {
                // Use the relationship to check for meter readings
                $checkMeterReadings = $item->meterReading()
                    ->where('startdate', '<=', $nextdateFormatted)
                    ->where('enddate', '>=', $updatedFormatted)
                    ->exists();
                if (!$checkMeterReadings) {
                    $MeterReadingLink = '<a href="' . route('meter-reading.create', ['id' => $item->unit_id, 'model' => 'units']) . '" class="badge badge-warning mt-2">Add Reading</a>';
                } else {
                    $MeterReadingLink = '<a href="" class="badge badge-information mt-2">Reading Added</a>';
                }
            }

            $nextDateFormatted = empty($item->nextdate) ? 'Charged Once' : Carbon::parse($item->nextdate)->format('Y-m-d');
            $charge_name = $item->charge_name;
            $charge_cycle = $item->charge_cycle;
            $charge_type = $item->charge_type;
            if ($charge_type === 'fixed') {
                $rate = $this->sitesettings->site_currency . ' ' . number_format($item->rate, 0, '.', ',');
            } else {
                $rate = $this->sitesettings->site_currency . ' ' . number_format($item->rate, 0, '.', ',') . ' <i>Per Unit</i>';
            }
            $recurring_charge = $item->recurring_charge . '<br/>' . $MeterReadingLink;
            // If the current charge has child charges, add them to the charge name
            if ($item->childrencharge->isNotEmpty()) {
                foreach ($item->childrencharge as $child) {

                    if ($child->charge_type == 'units') {
                        $childCheckMeterReadings = $child->meterReading()
                            ->where('startdate', '<=', $nextdateFormatted)
                            ->where('enddate', '>=', $updatedFormatted)
                            ->exists();
                        if (!$childCheckMeterReadings) {
                            $childMeterReadingLink = '<a href="' . route('meter-reading.create', ['id' => $child->unit_id, 'model' => 'units']) . '" class="badge badge-warning mt-2">Add Reading</a>';
                        } else {
                            $childMeterReadingLink = '<a href="" class="badge badge-information mt-2">Reading Added</a>';
                        }
                    } else {
                        // Handle cases where $child->charge_type is not 'units'
                        $childMeterReadingLink = ''; // or any other appropriate handling
                    }
                    $charge_name .= ' <div>
                                        <i class="mdi mdi-menu-right mdi-24px text-muted" style="vertical-align: middle;"></i>
                                        <span class="text-muted me-3">' . $child->charge_name . '</span>
                                    </div>';
                    $charge_cycle .= ' <div>
                                            <i class="mdi mdi-menu-right mdi-24px text-muted" style="vertical-align: middle;"></i>
                                            <span class="text-muted me-3">' . $child->charge_cycle . '</span>
                                        </div>';
                    $charge_type .= ' <div>
                                        <i class="mdi mdi-menu-right mdi-24px text-muted" style="vertical-align: middle;"></i>
                                        <span class="text-muted me-3">' . $child->charge_type . '</span>
                                    </div>';
                    $rate .= ' <div>
                                    <i class="mdi mdi-menu-right mdi-24px text-muted" style="vertical-align: middle;"></i>
                                    <span class="text-muted me-3">' . $this->sitesettings->site_currency . ' ' . number_format($child->rate, 0, '.', ',') . '</span>
                                </div>';
                    $recurring_charge .= ' <div>
                                <i class="mdi mdi-menu-right mdi-24px text-muted" style="vertical-align: middle;"></i>'
                        . $childMeterReadingLink . '
                            </div>';
                }
            }
            $isDeleted = $item->deleted_at !== null;
            $row = [
                'id' => $item->id,
                $charge_name,
                $charge_cycle,
                $charge_type,
                $rate,
                $recurring_charge,
                \Carbon\Carbon::parse($item->updated_at)->format('Y-m-d'),
                $nextDateFormatted,
                'isDeleted' => $isDeleted,

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
            $isDeleted = $item->deleted_at !== null;
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
                'isDeleted' => $isDeleted,
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
            $isDeleted = $item->deleted_at !== null;
            $row = [
                'id' => $item->id,
                $key + 1,
                '<img src="' . $thumbnail . '" alt="' . $item->name . '" style="width:130px;height:80px;border-radius:0">',
                $item->file_name,
                $category,
                $item->collection_name,
                $item->size,
                'isDeleted' => $isDeleted,

            ];
            // If $Extra Columns is true, insert unit details at position 3

            $tableData['rows'][] = $row;
        }

        return $tableData;
    }

    public function getAmenitiesData($amenities,$extraColumns = false)
    {
        // Eager load the 'unit' relationship
        //   $invoicedata->load('user');

        /// TABLE DATA ///////////////////////////
        $headers = ['AMENITIES', 'ATTACHED PROPERTY', 'ACTIONS'];

        $tableData = [
            'headers' => $headers,
            'rows' => [],
        ];
        foreach ($amenities as $key=> $item) 
        {
            $names = '';
            foreach ($item->properties as $key => $property) {
                $names .= '<li class="">' . ($key + 1) . '. ' . $property->property_name . '</li>';
            }
            // Add a 'deleted' flag if the item is soft deleted
            $isDeleted = $item->deleted_at !== null;
            $row = [
                'id' => $item->id,
                $item->amenity_name,
                $names,
                'isDeleted' => $isDeleted,
                
            ];
            $tableData['rows'][] = $row;
        }

        return $tableData;
    }
    public function getUserData($userdata, $extraColumns = false)
    {

        /// TABLE DATA ///////////////////////////

        $headers = ['NAME', 'ROLE', 'EMAIL', 'STATUS', 'ACTIONS'];

        // If $Extra columns is true, insert 'Unit Details' at position 3
        if ($extraColumns) {
            array_splice($headers, 2, 0, ['ASSIGNED UNITS']);
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
             <p>' . $item->firstname . ' ' . $item->lastname .
                '</p>
            </div>
          </div>';
          
          $userStatus = $item->status === 'Active' ? '<span class="badge badge-active">Active</span>' : '<span class="badge badge-warning">Inactive</span>';
            $isDeleted = $item->deleted_at !== null;

            $row = [
                'id' => $item->id,
                $name,
                $roleNames->name ?? 'Super Admin',
                $item->email,
                $userStatus,
                'isDeleted' => $isDeleted,
                

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
          $isDeleted = $item->deleted_at !== null;
            $row = [
                'id' => $item->id,
                $name,
                $item->email,
                $item->phonenumber,
                $item->vendorSubscription->subscription_status,
                'isDeleted' => $isDeleted,

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
            $isDeleted = $item->deleted_at !== null;
            $row = [
                'id' => $item->id,
                $requestStatus . ' ' . $status,
                $item->category,
                $item->subject
                    . '</br><span class="text-muted" style="font-weight:500;font-style: italic">' . ($item->property->property_name ?? '') . ' ' .($unit ?? ''). '</span>',
                Carbon::parse($item->created_at)->format('Y-m-d')
                    . '</br><span class="text-muted" style="font-weight:500;font-style: italic">' . $ageInDays . ' Days </span>',
                $raisedby
                    . '</br><span class="text-muted" style="font-weight:500;font-style: italic">' . ($roleNames ? $roleNames->name : '') . '</span>',
                $priorityStatus,
                $assigned ?? $assignLink,
                $this->sitesettings->site_currency . ' ' . number_format($item->totalamount, 0, '.', ','),
                'isDeleted' => $isDeleted,

            ];
            // If $Extra Columns is true, insert unit details at position 3
            if ($extraColumns) {
                array_splice($row, 3, 0, $item->property->property_name ?? ''); // Replace with how you get unit details
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
            $isDeleted = $item->deleted_at !== null;
            $row = [
                'id' => $item->id,
                Carbon::parse($item->created_at)->format('Y-m-d'),
                $item->charge_name,
                class_basename($item->transactionable_type),
                $item->transactionable->referenceno,
                $item->debitAccount->account_name,
                $item->creditAccount->account_name,
                $item->amount,
                'isDeleted' => $isDeleted,

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
            $isDeleted = $item->deleted_at !== null;
            $row = [
                'id' => $item->id,
                $key + 1,
                Carbon::parse($item->created_at)->format('Y-m-d'),
                $item->item,
                $item->quantity,
                $this->sitesettings->site_currency . ' ' . number_format($item->price, 0, '.', ','),
                $this->sitesettings->site_currency . ' ' . number_format($item->amount, 0, '.', ','),
                'isDeleted' => $isDeleted,

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

        $headers = ['NAME', 'KEY', 'VALUE','DESCRIPTION', 'ACTIONS'];

        // If $Extra columns is true, insert 'Unit Details' at position 3
        if ($extraColumns) {
            array_splice($headers, 2, 0, ['PROPERTY']);
        }

        $tableData = [
            'headers' => $headers,
            'rows' => [],
        ];

        foreach ($settingdata as $item) {
            $class= class_basename($item->model_type);
            switch ($class) {
                case 'Lease':
                    $value = $item->model->property->property_name . ' - ' . $item->model->unit->unit_number;
                         // Convert to an array
                    break;
                case 'Property':
                    $value = $item->model->property_name;
                    break;
                default:
                    break; // or handle this case differently
                }
            $isDeleted = $item->deleted_at !== null;
            $row = [
                'id' => $item->id,
                $item->name,
                $item->key,
                $item->value,
                $item->description,
                'isDeleted' => $isDeleted,
                

            ];
            // If $Extra Columns is true, insert unit details at position 3
            if ($extraColumns) {
                array_splice($row, 3, 0, $value); // Replace with how you get unit details
            }
            $tableData['rows'][] = $row;
        }

        return $tableData;
    }

    public function generateSettingTabContents($modelType, $setting, $individualsetting = null)
    {

        $tabTitles = collect([
            'Global Settings',
            'Overrides',
        ]);
        $controller = 'setting';
        $globalSettings = Setting::where('model_type', $modelType)
            ->whereNull('model_id')
        ->get();

        // Check if $individualsetting is passed and is null, then perform the query
        if (is_null($individualsetting)) {
            // If no individual setting is passed, perform the query to fetch them
            $individualSetting = Setting::where('model_type', $modelType)
                ->whereNotNull('model_id')
                ->showSoftDeleted()
                ->get();
        } else {
            // If $individualsetting is provided, use it directly
            $individualSetting = $individualsetting;
            }

        $settingsTableData = $this->getSettingData($individualSetting, true);
        $id = class_basename($setting->model_type);

        $tabContents = [];

        foreach ($tabTitles as $title) {
            if ($title === 'Global Settings') {
                $tabContents[] = View('admin.Setting.global_settings', compact('setting', 'globalSettings'))->render();
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

        $headers = ['REFNO', 'STATUS | DUEDATE', 'NAME', 'AMOUNT DUE', 'PAID', 'BALANCE', 'ACTIONS'];

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
            $isDeleted = $item->deleted_at !== null;


            $row = [
                'id' => $item->id,
                $item->referenceno,
                $expenseStatus . ' </br></br>' . Carbon::parse($item->duedate)->format('Y-m-d'),
                $item->name,
                $this->sitesettings->site_currency . ' ' . number_format($item->totalamount, 0, '.', ','),
                $this->sitesettings->site_currency . ' ' . number_format($totalPaid, 0, '.', ','),
                $balanceStatus . ' </br></br> ' . $payLink,
                'isDeleted' => $isDeleted,

            ];
            // If $Extra Columns is true, insert unit details at position 3
            if ($extraColumns) {
                array_splice($row, 3, 0, $item->property->property_name); // Replace with how you get unit details
            }
            $tableData['rows'][] = $row;
        }

        return $tableData;
    }

    public function getPaymentMethodData($PaymentMethoddata, $extraColumns = false)
    {
         /// TABLE DATA ///////////////////////////

         $headers = ['NAME', 'TYPE', 'ACTIONS'];

         // If $Extra columns is true, insert 'Unit Details' at position 3
         if ($extraColumns) {
            array_splice($headers, 0, 0, ['PROPERTY']);
        }

        $tableData = [
            'headers' => $headers,
            'rows' => [],
        ];

        foreach ($PaymentMethoddata as $item) {
            $isDeleted = $item->deleted_at !== null;
            $row = [
                'id' => $item->id,
                $item->name,
                $item->type,
                'isDeleted' => $isDeleted,
            ];
            // If $Extra Columns is true, insert unit details at position 3
            if ($extraColumns) {
                array_splice($row, 1, 0, $item->property->property_name); // Replace with how you get unit details
            }
            $tableData['rows'][] = $row;
        }

        return $tableData;
    }

    public function getTaxData($taxData, $extraColumns = false)
    {
         /// TABLE DATA ///////////////////////////

         $headers = ['NAME', 'PROPERTY TYPE','APPLIES TO','RATE','STATUS', 'ACTIONS'];

         // If $Extra columns is true, insert 'Unit Details' at position 3
         if ($extraColumns) {
            array_splice($headers, 0, 0, ['PROPERTY']);
        }

        $tableData = [
            'headers' => $headers,
            'rows' => [],
        ];

        foreach ($taxData as $item) {
            $className = class_basename($item->model_type);
            $status = ($item->status === 'active')
            ? '<span class="badge badge-active">Active</span>'
            : '<span class="badge badge-danger">Not Active</span>';
            $isDeleted = $item->deleted_at !== null;
            $row = [
                'id' => $item->id,
                $item->name,
                $item->propertyType->property_category.' - '. $item->propertyType->property_type,
                $className,
                $item->rate,
                $status,
                'isDeleted' => $isDeleted,
            ];
            // If $Extra Columns is true, insert unit details at position 3
            if ($extraColumns) {
                array_splice($row, 1, 0, $item->property->property_name); // Replace with how you get unit details
            }
            $tableData['rows'][] = $row;
        }

        return $tableData;
    }

    /// Delete Method
    public function destroy($model, array $relationships, $modelName)
    {
        $blockingRelationships = [];

            // Check if there are any relationships to check
            if (!empty($relationships) && $relationships[0] !== '') {
                // Check each relationship for existing related records
                foreach ($relationships as $relationship) {
                    // Ensure the relationship method exists on the model before calling it
                    if (method_exists($model, $relationship) && $model->$relationship()->exists()) {
                        $blockingRelationships[] = $relationship;
                    }
                }

        // If there are blocking relationships, return with an error message
            if (!empty($blockingRelationships)) {
                $blockingRelationshipsString = implode(', ', $blockingRelationships);
                return back()->with('statuserror', 'Cannot delete ' . $modelName . ' because the following related records exist: ' . $blockingRelationshipsString . '.');
            }
        }

        // Perform deletion
        $model->delete();

        return back()->with('status', $modelName . ' deleted successfully.');
    }
}
