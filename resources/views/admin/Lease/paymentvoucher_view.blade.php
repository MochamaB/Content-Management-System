<div class="row">
    <div class="col-md-9">

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
                                    <h3 style="text-transform: uppercase;"> {{$paymentvoucher->voucher_type}}</h3>
                                </li>
                                <li><b>PV#: {{$paymentvoucher->id}}-{{$paymentvoucher->referenceno}}</b></li>
                                <li style="color:red; font-weight:700;font-size:14px">TOTAL RECEIVED</li>
                                <li style="color:red; font-weight:700;font-size:20px"> {{ $sitesettings->site_currency }} @currency($paymentvoucher->totalamount)</li>
                            </ul>
                        </td>
                    </tr>
                    <!------ SECOND SECTION DETAILS -->
                    <tr>
                        <td></br>
                            <h4="text-muted"><b>PAID BY</b></h4>
                                <ul class="ml-2 px-3 list-unstyled">
                                    <li><b>PROPERTY:</b> {{$paymentvoucher->property->property_name}}</li>
                                    <li><b>UNIT NUMBER:</b> {{$payment->unit->unit_number ?? 'NONE'}}</li>
                                    <li><b>NAME:</b> {{$paymentvoucher->model->name}}
                                        {{$paymentvoucher->model->firstname}} {{$paymentvoucher->model->lastname}}
                                    </li>
                                    <li><b>EMAIL:</b> {{$paymentvoucher->model->email}}</li>
                                    <li><b>PHONE NO:</b> {{$paymentvoucher->model->phonenumber}}</li>

                                </ul>
                        </td>
                        <td></td>
                        <td class="text-right">
                            <ul class="ml-2 px-3 list-unstyled">
                                <li><b>PAYMENT DATE:</b> {{\Carbon\Carbon::parse($paymentvoucher->created_at)->format('d M Y')}}</li>
                                <li><b>PAID DATE:</b> {{\Carbon\Carbon::parse($paymentvoucher->duedate)->format('d M Y')}}</li>
                                <li></br></li>
                                @if( $paymentvoucher->status == 'paid' )
                                <div style="background-color:green;font-size:17px" class="badge badge-opacity-warning"> PAID</div> <!------Status -->
                                @elseif( $paymentvoucher->status == 'Payable' )
                                <div class="badge badge-warning">PAYABLE</div>
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
                        <th class="text-center">Amount Paid </th>
                        <th class="text-center">Total Paid</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($paymentvoucher->paymentvoucherItems as $key=> $item)
                    <tr style="height:35px;">
                        <td class="text-center">{{$key+1}}</td>
                        <td class="text-center" style="text-transform: capitalize;">
                            {{$item->charge_name}} Charge

                        </td>
                        <td class="text-center">@currency($item->amount) </td>
                        <td class="text-center"> @currency($item->amount)</td>

                    </tr>
                    @endforeach
                </tbody>
            </table></br>
            <!------- FOURTH LEVEL PAYMENT DETAILS AND TOTALS-->
            <table class="table">
                <tbody>
                    <tr>
                        <td>
                            <h4><b>Payment Voucher </b></h4>
                            <p>This voucher serves as proof of payment made to the company.</p>

                            <h6>.</h6>
                            <ul>

                            </ul>

                        </td>
                        <td class="align-top">
                            <h4><b>Totals </b></h4>
                            <table class="table table-bordered">
                                <tbody>
                                    <tr style="height:45px;">
                                        <td>Sub Total Amount</td>
                                        <td class="text-center">{{ $sitesettings->site_currency }}. @currency($paymentvoucher->totalamount)</td>
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
                                        <td class="text-bold-800" style="font-size:18px;font-weight:700">Total PAID</td>

                                        <td class="text-bold-800 text-center" style="font-size:18px;font-weight:700">{{ $sitesettings->site_currency }} @currency($paymentvoucher->totalamount) </td>

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

    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-header">
                <h4>ACTIONS</h4>
            </div>
            <div class="card-body">
                <a href="" onclick="printDiv('printMe')" class="btn btn-warning btn-lg text-white"><i class="icon-printer" style="color:white"></i> Print to PDF</a>
            </div>
        </div>

    </div>

</div>