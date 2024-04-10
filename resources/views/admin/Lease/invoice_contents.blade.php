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
                            <h3 style="text-transform: uppercase;"> {{$invoice->type}} INVOICE</h3>
                        </li>
                        <li><b>INV#: {{$invoice->id}}-{{$invoice->referenceno}}</b></li>
                        <li style="color:red; font-weight:700;font-size:14px">TOTAL DUE</li>
                        <li style="color:red; font-weight:700;font-size:20px"> {{ $sitesettings->site_currency }} {{$invoice->totalamount}}</li>
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
                    {{$item->charge_name}} Charge
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
                <td class="text-center">{{$item->amount}} </td>
                <td class="text-center"> {{$item->amount}}</td>

            </tr>
            @endforeach
        </tbody>
    </table></br>
    <!------- FOURTH LEVEL PAYMENT DETAILS AND TOTALS-->
    <table class="table">
        <tbody>
            <tr>
                <td>
                    <h4><b>Payment Methods </b></h4>
                    <p>The following methods are available.</p>
                    @foreach($PaymentMethod as $item)
                    @if($item->name !== 'Cash')
                    <h6>{{ $item->name }}</h6>
                    <ul>  @if(stripos($item->name, 'paybill') === false)
                        <li><strong>Account Number:</strong> {{ $item->account_number }}</li>
                        <li><strong>Account Name:</strong> {{ $item->account_name }}</li>
                        @else
                        <li><strong>Paybill Number:</strong> {{ $item->account_number }}</li>
                        <li><strong>Paybill Number:</strong> <span style="color:blue; font-weight:700;">{{$invoice->id}}-{{$invoice->referenceno}}</span></li>
                        @endif
                    </ul>
                  
                    @endif
                    @endforeach

                </td>
                <td class="align-top">
                    <h4><b>Totals </b></h4>
                    <table class="table table-bordered">
                        <tbody>
                            <tr style="height:45px;">
                                <td>Sub Total Amount</td>
                                <td class="text-center">{{ $sitesettings->site_currency }} {{$invoice->totalamount}}</td>
                            </tr>
                            <tr style="height:45px;">
                                <td>Tax & Discounts</td>
                                <td class="text-center">{{ $sitesettings->site_currency }} 0 </td>
                            </tr>
                            <tr style="height:45px;">
                                <td>Other Charges</td>
                                <td class="text-center">{{ $sitesettings->site_currency }} 0 </td>
                            </tr>
                            <tr style="height:45px;">
                                <td class="text-bold-800" style="font-size:18px;font-weight:700">Total Due</td>

                                <td class="text-bold-800 text-center" style="font-size:18px;font-weight:700">{{ $sitesettings->site_currency }} {{$invoice->totalamount}} </td>

                            </tr>
                        </tbody>
                    </table>
                
                </td>


            </tr>
        </tbody>
    </table>
    <!------- FOOTER-->
    <hr>

    <div class="col-md-12" style="text-align:center;">
        <h6>Terms & Condition</h6>
        <p>Refer to the terms and conditions on Lease agreement.</p>
        <p><a href="www.bridgetech.co.ke">POWERED BY BRIDGE PROPERTIES</a></p>
    </div>

</div>