<div class=" contwrapper table-responsive table-responsive-sm" id="printMe">

    <table class="table ">
        <tbody>
            <!--- FIRST SECTION  HEADER------->
            <tr>
                <td>
                    @if ($sitesettings)
                    <img src="{{ $sitesettings->getFirstMediaUrl('logo')}}" alt="Logo" style="height: 140px; width: 180px;border-radius:0px">
                    @else
                    <img src="url('resources/uploads/images/noimage.jpg')" alt="No Image">
                    @endif
                </td>
                <td class="text-right">
                    <ul class="ml-0 px-3 list-unstyled">
                        <li><b>COMPANY: </b>{{$sitesettings->company_name }}</li>
                        <li><b>LOCATION: </b>{{ $sitesettings->company_location}}</li>
                        <li><b>EMAIL: </b>{{ $sitesettings->company_email }}</li>
                        <li><b>TEL: </b>{{ $sitesettings->company_telephone }}</li>
                    </ul>
                </td>
                <td class="text-left">
                    <ul class="ml-4 px-3 list-unstyled">
                        <li>
                            <h3 style="text-transform: uppercase;"> {{$invoice->name}} INVOICE</h3>
                        </li>
                        <li><b>{{$invoice->referenceno}}</b></li>
                        <li style="color:red; font-weight:700;font-size:14px">TOTAL DUE</li>
                        <li style="color:red; font-weight:700;font-size:20px"> {{ $sitesettings->site_currency }} @currency($invoice->totalamount)</li>
                    </ul>
                </td>
            </tr>
            <!------ SECOND SECTION DETAILS -->
            <tr>
                <td></br>
                    <h4="text-muted"><b>BILL TO</b></h4>
                        <ul class="ml-2 px-3 list-unstyled">
                            <li><b>PROPERTY:</b> {{$invoice->property->property_name}}</li>
                            <li><b>UNIT NUMBER:</b> {{$invoice->unit->unit_number}}</li>
                            <li><b>NAME:</b> {{$invoice->model->firstname}} {{$invoice->model->lastname}}</li>
                            <li><b>EMAIL:</b> {{$invoice->model->email}}</li>
                            <li><b>PHONE NO:</b> {{$invoice->model->phonenumber}}</li>
                        </ul>
                </td>
                <td></td>
                <td class="text-right">
                    <ul class="ml-2 px-3 list-unstyled">
                        <li><b>INVOICE DATE:</b> {{\Carbon\Carbon::parse($invoice->created_at)->format('d M Y')}}</li>
                        <li><b>DUE DATE:</b> {{\Carbon\Carbon::parse($invoice->duedate)->format('d M Y')}}</li>
                        <li></br></li>
                        @if( $invoice->status == 'paid' )
                        <div class="badge badge-active"> PAID</div> <!------Status -->
                        @elseif( $invoice->status == 'overpaid' )
                        <div class="badge badge-information"> OVER PAID</div>
                        @elseif ( $invoice->status == 'partially_paid' )
                        <div class="badge badge-warning"> PARTIALLY PAID</div>
                        @elseif ( $invoice->status == 'unpaid' )
                        <div class="badge badge-error">UNPAID </div>
                        @endif
                    </ul>
                </td>
            </tr>
        </tbody>
    </table>
    <!------- THIRD LEVEL INVOICE ITEMS -->
    <table class="table table-hover table-bordered" style="font-size:12px;border:1px solid black;">
        <thead>
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
                        @endphp
                        <ul class="list-unstyled text-left">
                            <li><i>Current Reading: {{$meterReadings->currentreading ?? ' 0'}} Units</i> </li>
                            <li><i>Last Reading: {{$meterReadings->lastreading ?? ' 0'}} Units</i> </li>
                            <ul>
                                @endif
                </td>
                <td class="text-center">{{ $sitesettings->site_currency }} @currency($item->amount) </td>
                <td class="text-center">{{ $sitesettings->site_currency }} @currency($item->amount) </td>

            </tr>
            @endforeach
        </tbody>
    </table></br>
    <!------- FOURTH LEVEL PAYMENT DETAILS AND TOTALS-->

    <div class="row">
    <div class="col-md-6 mt-3">
    <h4><b>Payment Methods </b></h4>
        <div class="d-flex justify-content-start">
            <p class="text-muted me-3" style="font-size:15px;font-weight:600"> </p>
            <span> </span>
        </div>
        <div class="d-flex justify-content-start">
            <p class="text-muted me-3" style="font-size:15px;font-weight:600"> </p>
            <span> </span>
        </div>
        <div class="d-flex justify-content-start">
            <p class="text-muted me-3" style="font-size:16px;font-weight:600"> </p>
            <span> </span>
        </div>
        <div class="d-flex justify-content-start mt-3">
            <h4 class="me-3" style="font-weight:700"> </h4>
            <h4 class="text-success" style="font-weight:700"> </h4>
        </div>
    </div>
    <div class="col-md-6 mt-3">
        <div class="d-flex justify-content-end">
            <p class="text-muted me-3" style="font-size:15px;font-weight:600">Sub total Amount :</p>
            <span>{{ $sitesettings->site_currency }} @currency($invoice->totalamount)</span>
        </div>
        <div class="d-flex justify-content-end">
            <p class="text-muted me-3" style="font-size:15px;font-weight:600">Tax & Discounts:</p>
            <span>{{ $sitesettings->site_currency }} 0 </span>
        </div>
        <div class="d-flex justify-content-end">
            <p class="text-muted me-3" style="font-size:16px;font-weight:600">Other Charges:</p>
            <span>{{ $sitesettings->site_currency }} 0 </span>
        </div>
        <div class="d-flex justify-content-end mt-3">
            <h4 class="me-3" style="font-weight:700">Total:</h4>
            <h4 class="text-error" style="font-weight:700">{{ $sitesettings->site_currency }} @currency($invoice->totalamount)</h4>
        </div>
    </div>
    </div>
    <!------- FOOTER-->
    

</div>