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
                                    <h3 style="text-transform: uppercase;"> {{$deposit->name}}</h3>
                                </li>
                                <li><b>{{$deposit->referenceno}} - {{$deposit->id}}</b></li>
                                <li style="color:red; font-weight:700;font-size:14px">TOTAL RECEIVED</li>
                                <li style="color:red; font-weight:700;font-size:20px"> {{ $sitesettings->site_currency }} @currency($deposit->totalamount)</li>
                            </ul>
                        </td>
                    </tr>
                    <!------ SECOND SECTION DETAILS -->
                    <tr>
                        <td></br>
                            <h4="text-muted"><b>PAID BY</b></h4>
                                <ul class="ml-2 px-3 list-unstyled">
                                    <li><b>PROPERTY:</b> {{$deposit->property->property_name}}</li>
                                    <li><b>UNIT NUMBER:</b> {{$payment->unit->unit_number ?? 'NONE'}}</li>
                                    <li><b>NAME:</b> {{$deposit->model->name}}
                                        {{$deposit->model->firstname}} {{$deposit->model->lastname}}
                                    </li>
                                    <li><b>EMAIL:</b> {{$deposit->model->email}}</li>
                                    <li><b>PHONE NO:</b> {{$deposit->model->phonenumber}}</li>

                                </ul>
                        </td>
                        <td></td>
                        <td class="text-right">
                            <ul class="ml-2 px-3 list-unstyled">
                                <li><b>PAYMENT DATE:</b> {{\Carbon\Carbon::parse($deposit->created_at)->format('d M Y')}}</li>
                                <li><b>PAID DATE:</b> {{\Carbon\Carbon::parse($deposit->duedate)->format('d M Y')}}</li>
                                <li></br></li>
                                @if( $deposit->getStatusLabel() === 'Paid' )
                                <div style="background-color:green;font-size:17px" class="badge badge-opacity-warning"> PAID</div> <!------Status -->
                                @elseif( $deposit->getStatusLabel() === 'Unpaid' )
                                <div class="badge badge-error">UNPAID</div>
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

                @foreach($deposit->getItems as $key=> $item)
                <tr style="height:35px;">
                    <td class="text-center">{{$key+1}}</td>
                    <td class="text-center" style="text-transform: capitalize;">
                        {{$item->description}} Charge
                    </td>
                    <td class="text-center">@currency($item->amount) </td>
                    <td class="text-center"> @currency($item->amount) </td>

                </tr>
                @endforeach

                </tbody>
            </table></br>
          
            <!------- FOURTH LEVEL PAYMENT DETAILS AND TOTALS-->

            <div class="row">
                <div class="col-md-6 mt-3">
                    <h4><b>Deposit Receipt </b></h4>
                    <p>This receipt serves as proof of payment made to the company.</p>

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
                        <span>{{ $sitesettings->site_currency }} @currency($deposit->totalamount)</span>
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
                        <h4 class="text-error" style="font-weight:700">{{ $sitesettings->site_currency }} @currency($deposit->totalamount)</h4>
                    </div>
                </div>
            </div>
            <!------- FOOTER-->


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