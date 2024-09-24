<div class=" contwrapper table-responsive table-responsive-sm" id="printMe">
    <div class="row">
        <!-- FIRST LEVEL -------->
        <div class="col-md-4">
            @if ($sitesettings)
            <img src="{{ $sitesettings->getFirstMediaUrl('logo')}}" alt="Logo" style="height: 140px; width: 180px;">
            @else
            <img src="url('resources/uploads/images/noimage.jpg')" alt="No Image">
            @endif
        </div>
        <div class="col-md-4">
            <ul class="ml-0 px-3 list-unstyled">
                <li><b>COMPANY: </b>{{$sitesettings->company_name }}</li>
                <li><b>LOCATION: </b>{{ $sitesettings->company_location}}</li>
                <li><b>EMAIL: </b>{{ $sitesettings->company_email }}</li>
                <li><b>TEL: </b>{{ $sitesettings->company_telephone }}</li>
            </ul>
        </div>
        <div class="col-md-4">
            <ul class="ml-4 px-3 list-unstyled">
                <li>
                    <h5 style="text-transform: uppercase;"> {{$payment->model->name}} RECEIPT</h5>
                </li>
                <li><b>{{$payment->referenceno}} - #{{$payment->id}}</b></li>
                <li style="color:green; font-weight:700;font-size:14px">TOTAL PAID</li>
                <li style="color:green; font-weight:700;font-size:20px"> {{ $sitesettings->site_currency }} @currency($payment->totalamount)</li>
            </ul>
        </div>
         <!------ SECOND SECTION DETAILS -->
        <div class="col-md-6">
        <h4="text-muted"><b>PAYMENT TO / FROM</b></h4>
                        <ul class="ml-2 px-3 list-unstyled">
                            <li><b>PROPERTY:</b> {{$payment->property->property_name}}</li>
                            <li><b>UNIT NUMBER:</b> {{$payment->unit->unit_number ?? 'NONE'}}</li>
                            <li><b>NAME:</b> {{$payment->model->model->name}}
                                {{$payment->model->model->firstname}} {{$payment->model->model->lastname}}
                            </li>
                            <li><b>EMAIL:</b> {{$payment->model->model->email}}</li>
                            <li><b>PHONE NO:</b> {{$payment->model->model->phonenumber}}</li>
                        </ul>
        </div>
        <div class="col-md-6">
        <ul class="ml-2 px-3 list-unstyled" style="text-align: right;">
                        <li><b>PAYMENT DATE:</b> {{\Carbon\Carbon::parse($payment->created_at)->format('d M Y')}}</li>

                        <li></br></li>

                    </ul>
        </div>
        <!------- THIRD LEVEL PAYMENT ITEMS -->
        <table class="table table-hover table-bordered custom-table" style="font-size:12px;border:1px solid black;">
        <thead>
            <tr class="tableheading" style="height:35px;">

                <th>No.</th>
                <th class="text-center">Description </th>
                <th class="text-center">Amount Paid </th>
                <th class="text-center">Total Paid</th>
            </tr>
        </thead>
        <tbody>

          
            <tr style="height:35px;">
                <td class="text-center">1.</td>
                <td class="text-center" style="text-transform: capitalize;">
                    {{$payment->model->name  ?? $payment->model->description}}

                </td>
                <td class="text-center">{{ $sitesettings->site_currency }} @currency($payment->totalamount) </td>
                <td class="text-center"> {{ $sitesettings->site_currency }} @currency($payment->totalamount)</td>

            </tr>
            

        </tbody>
    </table>

     <!------- FOURTH LEVEL PAYMENT DETAILS AND TOTALS-->
    

     <div class="mt-3">
            <div class="d-flex justify-content-end">
                <p class="text-muted me-3" style="font-size:15px;font-weight:600">{{$payment->model->name }} Amount Due :</p>
                <span>{{ $sitesettings->site_currency }} @currency($payment->model->totalamount)</span>
            </div>
            <div class="d-flex justify-content-end">
                <p class="text-muted me-3" style="font-size:16px;font-weight:600">Amount Paid:</p>
                <span>{{ $sitesettings->site_currency }} @currency($payment->totalamount) </span>
            </div>
            <div class="d-flex justify-content-end">
                <p class="text-muted me-3" style="font-size:15px;font-weight:600">Tax & Discounts:</p>
                <span>{{ $sitesettings->site_currency }} 0 </span>
            </div>
           
            <div class="d-flex justify-content-end mt-3">
                <h4 class="me-3" style="font-weight:700">Total Paid:</h4>
                <h4 class="text-success" style="font-weight:700">{{ $sitesettings->site_currency }} @currency($payment->totalamount)</h4>
            </div>
        </div>


    </div>


</div>