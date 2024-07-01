<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Statement</title>
    <style>
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
            padding: 20px;
        }

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

        .logo {
            width: 180px;
            height: auto;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
        }

        .header-table,
        .info-table,
        .items-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td,
        .info-table td {
            border: none;
        }

        @media screen and (max-width: 600px) {
            .container {
                padding: 10px;
            }

            .header-table,
            .info-table {
                width: 100%;
            }

            .header-table td,
            .info-table td {
                display: block;
                width: 100%;
                text-align: left;
            }

            .header-table img {
                display: block;
                margin: 0 auto 10px auto;
            }

            .header-table ul,
            .info-table ul {
                padding: 0;
                margin: 10px 0;
            }

            .header-table ul li,
            .info-table ul li {
                margin-bottom: 5px;
            }

            a.table:link {
                color: #1F3BB3;
                font-size: 15px;
                font-weight: 600;
                text-decoration: none;
                text-transform: capitalize;
            }

            a.table:visited {
                color: #0000ff;
            }

            a.table:hover {
                color: #ffaf00;
                font-size: 17px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <table class="header-table">
            <tbody>
                <tr>
                    <td>
                        <img class="logo" src="{{ $sitesettings->getFirstMediaUrl('logo') ?: 'resources/uploads/images/noimage.jpg' }}" alt="Logo">
                    </td>
                    <td>
                        <ul style="list-style-type: none; padding: 0; text-align: left;">
                            <li><b>COMPANY:</b> {{$sitesettings->company_name }}</li>
                            <li><b>LOCATION:</b> {{ $sitesettings->company_location}}</li>
                            <li><b>EMAIL:</b> {{ $sitesettings->company_email }}</li>
                            <li><b>TEL:</b> {{ $sitesettings->company_telephone }}</li>
                        </ul>
                    </td>
                    <td>
                        <ul style="list-style-type: none; padding: 0; text-align: left;">
                            <li>
                                <h3>ACCOUNT STATEMENT</h3>
                            </li>
                            <li><b>BILLING ACCOUNT:</b> {{$invoice->type}}</li>
                        </ul>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Info Section -->
        <table class="info-table">
            <tbody>
                <tr>
                    <td class="text-left">
                        <h4 style="text-align: left;"><b>STATEMENT TO</b></h4>
                        <ul style="list-style-type: none; padding-left: 10px; text-align: left;">
                            <li><b>PROPERTY:</b> {{$invoice->property->property_name}}</li>
                            <li><b>UNIT NUMBER:</b> {{$invoice->unit->unit_number}}</li>
                            <li><b>NAME:</b> {{$invoice->model->firstname}} {{$invoice->model->lastname}}</li>
                            <li><b>EMAIL:</b> {{$invoice->model->email}}</li>
                            <li><b>PHONE NO:</b> {{$invoice->model->phonenumber}}</li>
                        </ul>
                    </td>
                    <td></td>
                    <td>
                        <ul style="list-style-type: none; padding: 0; text-align: left;">
                            <li><b>STATEMENT DURATION:</b></li>
                            <li><i>(Last 6 Months)</i></li>
                        </ul>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Invoice Items -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Bills</th>
                    <th>Payments</th>
                    <th>Balance</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>-</td>
                    <td>-</td>
                    <td>Opening Balance</td>
                    <td>{{ $sitesettings->site_currency }}.</td>
                    <td>{{ $sitesettings->site_currency }}.</td>
                    <td>{{ $sitesettings->site_currency }}.@currency($openingBalance)</td>
                </tr>
                @php
                $balance = $openingBalance;
                $totalInvoices = 0;
                $totalPayments = 0;
                @endphp
                @foreach($transactions as $key => $item)
                <tr>
                    <td>{{$key +1}}</td>
                    <td>{{\Carbon\Carbon::parse($item->created_at)->format('d M Y')}}</td>
                    <td>{{$item->charge_name}}</td>
                    @if($item->transactionable_type ==='App\Models\Invoice')
                    @php
                    $balance += $item->amount;
                    $totalInvoices += $item->amount;
                    @endphp
                    <td>{{ $sitesettings->site_currency }}. @currency($item->amount)</td>
                    <td>-</td>
                    @elseif($item->transactionable_type ==='App\Models\Payment')
                    @php
                    $balance -= $item->amount;
                    $totalPayments += $item->amount;
                    @endphp
                    <td>-</td>
                    <td>{{ $sitesettings->site_currency }}.@currency($item->amount)</td>
                    @endif
                    <td>{{ $sitesettings->site_currency }}. @currency($balance)</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td></td>
                    <td></td>
                    <td><b>TOTALS</b></td>
                    <td><b>{{ $sitesettings->site_currency }}. @currency($totalInvoices)</b></td>
                    <td><b>{{ $sitesettings->site_currency }}. @currency($totalPayments)</b></td>
                    <td><b>{{ $sitesettings->site_currency }}. @currency($balance)</b></td>
                </tr>
            </tfoot>
        </table>

        <h4><b>PAYMENT OPTIONS AVAILABLE </b></h4>
        @foreach($PaymentMethod as $key=> $item)
        <ul style="list-style-type: none; padding: 0; text-align: left;">
            @if (stripos($item->name, 'M') !== false && stripos($item->name, 'PESA') !== false)
            <li style="font-size:14px;font-weight:400">{{$key+1}}. {{$item->name}} -
                <a href="{{route('mpesa.view', ['id' => $invoice->id])}}" class="table">
                    <i class="ti-money"></i>Click to Pay Now</a>
            </li>
            @else
            <li style="font-size:14px;font-weight:400">{{$key+1}}. {{$item->name}} </li>
            @endif
        </ul>
        @endforeach
    </div>
    <div class="footer">
        <i style="color:#1F3BB3">Powered By<a href="http://www.bridgetech.co.ke" target="_blank"> <b>Bridgtech Properties</b></a> Admin.</i>
    </div>
</body>

</html>