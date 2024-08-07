<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deposit</title>
    <style>
        /* Inline styles */
        body {
            font-family: sans-serif;
            font-size: 14px;
            line-height: 1.5;
            background-color: #f6f6f6;
            margin: 0;
            padding: 30px;
            text-transform: capitalize;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px 20px;
        }

        /* Table styles */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #f0f0f0;
        }

        /* Logo */
        .logo {
            width: 180px;
            height: auto;
        }

        /* Footer */
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
        }

        .row {
            display: flex;
        }

        .col-md-6 {
            width: 50%;
        }

        .mt-3 {
            margin-top: 1rem;
        }

        .d-flex {
            display: flex;
        }

        .justify-content-start {
            justify-content: flex-start;
        }

        .justify-content-end {
            justify-content: flex-end;
        }

        .me-3 {
            margin-right: 1rem;
        }

        .text-muted {
            color: #6c757d;
        }

        .text-success {
            color: #28a745;
        }

        .text-error {
            color: #dc3545;
        }

        /*--------------- badges */
        .badge {
            border-radius: 20px;
            font-size: 12.5px;
            line-height: 19px;
            padding: 0 10px 0;
            font-weight: 900;
            text-transform: uppercase;
        }

        .badge:hover {
            transform: scale(1.2);
            /* Increase the size by 10% on hover */
        }

        a .badge:hover {
            transform: scale(1.2);
        }

        .badge-active {
            color: #038d21 !important;
            border: 1px solid #038d21 !important;
            background-color: #c0e4c3;
        }

        .badge-error {
            color: #c50000c4 !important;
            background-color: #f9e2df;
            border: 2px solid #c50000c4 !important;
        }

        .badge-warning {
            color: #ffaf00 !important;
            background-color: #f7f5e0;
            border: 2px solid #fdac25 !important;
        }

        .badge-danger {
            color: #f83d3dc4 !important;
            background-color: #f9e2df;
            border: 2px solid #f83d3dc4 !important;
        }

        .badge-information {
            color: #3302a5c4 !important;
            background-color: #dae3fa;
            border: 2px solid #3302a5c4 !important;
        }

        .badge-dark {
            color: #1E283D !important;
            background-color: #dae3fa;
            border: 2px solid #1E283D !important;
        }

        .badge-light {
            color: #6a008a !important;
            background-color: #edd5f5;
            border: 2px solid #6a008a !important;
            margin-top: 5px;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <table style="border-collapse: collapse;">
            <tbody>
                <tr style="font-family: Lato, sans-serif;">
                    <td style="border: none;">
                        <img class="logo" src="{{ $sitesettings->getFirstMediaUrl('logo') ?: 'resources/uploads/images/noimage.jpg' }}" alt="Logo">
                    </td>
                    <td style="border: none;">
                        <ul style="list-style-type: none; padding: 0; text-align: left;">
                            <li><b>COMPANY:</b> {{$sitesettings->company_name }}</li>
                            <li><b>LOCATION:</b> {{ $sitesettings->company_location}}</li>
                            <li><b>EMAIL:</b> {{ $sitesettings->company_email }}</li>
                            <li><b>TEL:</b> {{ $sitesettings->company_telephone }}</li>
                        </ul>
                    </td>
                    <td style="border: none;">
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
                <!-- Second Section -->
                <tr style="border-top: 1px solid grey;">
                    <td style="border:none">
                        <h4 style="text-align:left;"><b>BILL TO</b></h4>
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
                    <td style="border: none;"></td>
                    <td style="border: none;">
                    <ul class="ml-2 px-3 list-unstyled">
                                <li><b>PAYMENT DATE:</b> {{\Carbon\Carbon::parse($deposit->created_at)->format('d M Y')}}</li>
                                <li><b>PAID DATE:</b> {{\Carbon\Carbon::parse($deposit->duedate)->format('d M Y')}}</li>
                                <li></br></li>
                                @if( $deposit->status == 'paid' )
                                <div style="background-color:green;font-size:17px" class="badge badge-opacity-warning"> PAID</div> <!------Status -->
                                @elseif( $deposit->status == 'unpaid' )
                                <div class="badge badge-error">UNPAID</div>
                                @endif

                            </ul>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Third Level deposit Items -->
        <table>
            <thead>
                <tr>
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
        </table>
        <br>
        <!----  THIRD LEVEL --->
        <table style="border-collapse: collapse;  width: 100%;">
            <tbody>
                <tr>
                    <td style="border:none; width: 50%;">
                        <h4><b>Payment Methods </b></h4>
                        <ul style="list-style-type: none; padding: 0; text-align: left;">
                            <li></li>

                        </ul>
                    </td>

                    <td class="text-center" style="border: none; width: 50%; ">
                        <ul style="list-style-type: none; padding: 0; text-align: right;">
                            <li>
                                <span class="text-muted me-3" style="font-size:15px;font-weight:600; display: inline;">Sub total Amount :</span>
                                <span style="display: inline;">{{ $sitesettings->site_currency }} @currency($deposit->totalamount)</span>
                            </li>
                            <br>
                            <li>
                                <span class="text-muted me-3" style="font-size:15px;font-weight:600; display: inline;">Tax & Discounts :</span>
                                <span style="display: inline;">{{ $sitesettings->site_currency }} 0</span>
                            </li><br>
                            <li>
                                <span class="text-muted me-3" style="font-size:15px;font-weight:600; display: inline;">Other Charges:</span>
                                <span style="display: inline;">{{ $sitesettings->site_currency }} 0</span>
                            </li><br>
                            <li>
                                <span class="me-3" style="font-size:17px;font-weight:700; display: inline;">Total:</span>
                                <span class="text-error" style="font-size:17px;font-weight:700; display: inline;">{{ $sitesettings->site_currency }} @currency($deposit->totalamount)</span>
                            </li>

                        </ul>
                    </td>
                </tr>
            </tbody>
        </table>


        <!-- Footer -->
    </div>
    <div class="footer">

    <i style="color:#1F3BB3">Powered By<a href="http://www.bridgetech.co.ke" target="_blank"> <b>Bridgtech Properties</b></a> Admin.<i>
    </div>
</body>

</html>