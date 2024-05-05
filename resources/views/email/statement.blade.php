<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Statement</title>
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
                        <ul style="list-style-type: none; padding: 0; text-align: left;">
                            <li>
                                <h3>ACCOUNT STATEMENT</h3>
                            </li>
                            <li><b>BILLING ACCOUNT:</b> {{$invoice->type}}</li>
                        </ul>
                    </td>
                </tr>
                <!-- Second Section -->
                <tr style="border-top: 1px solid grey;">
                    <td style="border:none">
                        <h4 style="text-align:left;"><b>STATEMENT TO</b></h4>
                        <ul style="list-style-type: none; padding: 0; text-align: left;">
                            <li><b>PROPERTY:</b> {{$invoice->property->property_name}}</li>
                            <li><b>UNIT NUMBER:</b> {{$invoice->unit->unit_number}}</li>
                            <li><b>NAME:</b> {{$invoice->model->firstname}} {{$invoice->model->lastname}}</li>
                            <li><b>EMAIL:</b> {{$invoice->model->email}}</li>
                            <li><b>PHONE NO:</b> {{$invoice->model->phonenumber}}</li>
                        </ul>
                    </td>
                    <td style="border: none;"></td>
                    <td style="border: none;">
                        <ul style="list-style-type: none; padding: 0; text-align: left;">
                            <li><b>STATEMENT DURATION:</b></li>
                            <li><i>(Last 6 Months)</i></li>
                            <li></li>
                        </ul>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Third Level Invoice Items -->
        <table>
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
                    <td style="text-transform: capitalize;">{{$item->charge_name}}</td>

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

        <!-- Footer -->
    </div>
    <div class="footer">

        <i style="color:#1F3BB3">Powered By<a href="http://www.bridgetech.co.ke" target="_blank"> <b>Bridgtech Properties</b></a> Admin.<i>
    </div>
</body>

</html>