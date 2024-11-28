<style>
    .mpesaButton {
  background: #6AAE2D;
  color: white;
  text-align: center;
  font-weight: bold;
  padding: 15px;
  border-radius: 10px;
  font-size: 20px;
  font-family: "Lato", "Helvetica Neue", Arial, Helvetica, sans-serif
}
</style>
@php
$status = $invoice->getStatusLabel();
$statusClasses = [
'Paid' => 'active',
'Unpaid' => 'warning',
'Over Due' => 'danger',
'Partially Paid' => 'dark',
'Over Paid' => 'light',
];
$statusClass = $statusClasses[$status] ?? 'warning'; // Default to 'warning' if status is not found
@endphp
<div class=" contwrapper table-responsive table-responsive-sm" id="printMe">
    <!-- FIRST LEVEL -------->
    <div class="row">
        <div class="col-md-4">
            @if ($sitesettings)
            <img src="{{ $sitesettings->getFirstMediaUrl('logo')}}" alt="Logo" style="height: 140px; width: 180px;border-radius:0px">
            @else
            <img src="url('resources/uploads/images/noimage.jpg')" alt="No Image">
            @endif

        </div>
        <div class="col-md-4">

            <ul style="list-style-type:none; text-align:left">
                <li><b>COMPANY:</b> {{$sitesettings->company_name }}</li>
                <li><b>LOCATION:</b> {{ $sitesettings->company_location}}</li>
                <li><b>EMAIL:</b> {{ $sitesettings->company_email }}</li>
                <li><b>TEL:</b> {{ $sitesettings->company_telephone }}</li>
            </ul>
        </div>
        <div class="col-md-4">
            <ul class="ml-4 px-3 list-unstyled">
                <li>
                    <h5 style="text-transform: uppercase;"> {{$invoice->name}} INVOICE</h5>
                </li>
                <li><b>{{$invoice->referenceno}}</b></li>
                <li style="color:red; font-weight:700;font-size:14px">TOTAL DUE</li>
                <li style="color:red; font-weight:700;font-size:20px"> {{ $sitesettings->site_currency }} @currency($invoice->totalamount)</li>
            </ul>

        </div>
        <!------ SECOND SECTION DETAILS -->
        <div class="col-md-6">
            <h4><b>BILL TO</b></h4>
            <ul class="ml-2 px-3 list-unstyled">
                <li><b>PROPERTY:</b> {{$invoice->property->property_name}}</li>
                <li><b>UNIT NUMBER:</b> {{$invoice->unit->unit_number}}</li>
                <li><b>NAME:</b> {{$invoice->model->firstname}} {{$invoice->model->lastname}}</li>
                <li><b>EMAIL:</b> {{$invoice->model->email}}</li>
                <li><b>PHONE NO:</b> {{$invoice->model->phonenumber}}</li>
            </ul>
        </div>
        <div class="col-md-6">
            <ul class="ml-2 px-3 list-unstyled" style="text-align: right;">
                <li><b>INVOICE DATE:</b> {{\Carbon\Carbon::parse($invoice->created_at)->format('d M Y')}}</li>
                <li><b>DUE DATE:</b> {{\Carbon\Carbon::parse($invoice->duedate)->format('d M Y')}}</li>
                <li></br></li>
                <!-- Render the badge -->
                <div class="badge badge-{{ $statusClass }}">{{ $status }}</div>
            </ul>
        </div>

        <!----- THIRD LEVEL INVOICE ITEMS -->
        <table class="table table-bordered custom-table" style="font-size:12px;border:1px solid black;">
            <thead style="border:1px solid black;">
                <tr class="tableheading" style="height:35px;">

                    <th>No.</th>
                    <th class="text-center">Description </th>
                    <th class="text-center">Amount Due </th>
                    <th class="text-center">Total Due</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->invoiceItems as $key=> $item)
                <tr style="height:35px;">
                    <td class="text-center">{{$key+1}}</td>
                    <td class="text-center" style="text-transform: capitalize;">
                        {{$item->description}}
                        <!--- METER READINGS -->
                        @if($item->unitcharge->charge_type == 'units')
                        @php
                        $meterReadings = $item->meterReadings()
                        ->whereDate('created_at', '>=', $invoice->created_at)
                        ->whereDate('created_at', '<=', $item->unitcharge->nextdate)
                            ->first();
                            // Default values in case meterReadings is not present
                            $currentReading = $meterReadings->currentreading ?? 0;
                            $lastReading = $meterReadings->lastreading ?? 0;
                            $rateAtReading = $meterReadings->rate_at_reading ?? 0;
                            $used = $currentReading - $lastReading;
                            @endphp
                            @if($meterReadings)
                            <ul class="list-unstyled text-left mt-1">
                                <li><i>Current Reading: {{ $currentReading }} Units</i> </li>
                                <li><i>Last Reading: {{ $lastReading }} Units</i> </li>
                                <li><i>Used: {{ $used }} Units * Rate: {{ $rateAtReading }}</i> </li>
                            </ul>
                            @else
                            <ul class="list-unstyled text-left">
                                <li><i>No meter reading available for this period.</i></li>
                            </ul>
                            @endif
                            @endif
                    </td>
                    <td class="text-center">{{ $sitesettings->site_currency }} @currency($item->amount) </td>
                    <td class="text-center">{{ $sitesettings->site_currency }} @currency($item->amount) </td>

                </tr>
                @endforeach
            </tbody>
        </table></br>
        <!------- FOURTH LEVEL PAYMENT DETAILS AND TOTALS-->
        <div class="col-md-6 mt-3">
            <h6><b>PAYMENT OPTIONS </b></h6>
            @if(isset($PaymentMethod))
            @foreach($PaymentMethod as $key=> $item)
            <div class="d-flex justify-content-start" style="text-transform: capitalize;">
                <p class="text-muted me-3" style="font-size:14px;font-weight:600"> </p>
                @if($item->name == 'm-pesa')
                <span class="defaulttext">{{$key+1}}. {{$item->name}} &nbsp;
                    <a href="{{route('mpesa.view', ['id' => $invoice->id])}}" class="btn btn-warning mpesaButton text-white mb-0 me-0">
                        <img src="{{ url('uploads/mpesa.png') }}" alt="Mpesa Logo" class="me-2" style="width: 20px; height: 20px;">
                        Pay with Mpesa
                    </a>
                </span>
                @else
                <span class="defaulttext">{{$key+1}}. {{$item->name}} </span>
                @endif
            </div>
            @endforeach
            @endif

        </div>
        <div class="col-md-6 mt-3">
            <div class="d-flex justify-content-end">
                <p class="text-muted me-3" style="font-size:14px;font-weight:600">Sub total Amount :</p>
                <span>{{ $sitesettings->site_currency }} @currency($invoice->totalamount)</span>
            </div>
            <div class="d-flex justify-content-end">
                <p class="text-muted me-3" style="font-size:14px;font-weight:600">Tax & Discounts:</p>
                <span>{{ $sitesettings->site_currency }} 0 </span>
            </div>
            <div class="d-flex justify-content-end">
                <p class="text-muted me-3" style="font-size:14px;font-weight:600">Other Charges:</p>
                <span>{{ $sitesettings->site_currency }} 0 </span>
            </div>
            <div class="d-flex justify-content-end mt-3">
                <h5 class="me-3" style="font-weight:700">Total:</h5>
                <h5 class="text-error" style="font-weight:700">{{ $sitesettings->site_currency }} @currency($invoice->totalamount)</h5>
            </div>
        </div>
    </div>
</div>