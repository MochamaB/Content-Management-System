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
          </td>
          <td style="border: none;">
            <ul style="list-style-type: none; padding: 0; text-align: left;">
              <li>
                <h3 style="text-transform: uppercase;">{{$payment->model->name}} RECEIPT</h3>
              </li>
              <li><b>{{$payment->referenceno}} - #{{$payment->id}}</b></li>
              <li style="color:green; font-weight:700;font-size:14px">TOTAL PAID</li>
              <li style="color:green; font-weight:700;font-size:20px"> {{ $sitesettings->site_currency }} @currency($payment->totalamount)</li>
            </ul>
          </td>
        </tr>
        <!-- Second Section -->
        <tr style="border-top: 1px solid grey;">
          <td style="border:none">
            <h4 style="text-align:left;"><b>PAYMENT TO / FROM</b></h4>
            <ul style="list-style-type: none; padding: 0; text-align: left;">
              <li><b>PROPERTY:</b> {{$payment->property->property_name}}</li>
              <li><b>UNIT NUMBER:</b> {{$payment->unit->unit_number ?? 'NONE'}}</li>
              <li><b>NAME:</b> {{$payment->model->model->name}}
                {{$payment->model->model->firstname}} {{$payment->model->model->lastname}}
              </li>
              <li><b>EMAIL:</b> {{$payment->model->model->email}}</li>
              <li><b>PHONE NO:</b> {{$payment->model->model->phonenumber}}</li>
            </ul>
          </td>
          <td style="border: none;"></td>
          <td style="border: none;">
                        <ul style="list-style-type: none; padding: 0; text-align: left;">
              <li><b>PAYMENT DATE:</b> {{\Carbon\Carbon::parse($payment->created_at)->format('d M Y')}}</li>

              <li></br></li>

            </ul>
          </td>
        </tr>
      </tbody>
    </table>
    <!------- THIRD LEVEL PAYMENT ITEMS -->
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

        @foreach($payment->paymentItems as $key=> $item)
        <tr style="height:35px;">
          <td class="text-center">{{$key+1}}</td>
          <td class="text-center" style="text-transform: capitalize;">
            {{$item->charge_name ?? $item->description}}

          </td>
          <td class="text-center">{{ $sitesettings->site_currency }} @currency($item->amount) </td>
          <td class="text-center"> {{ $sitesettings->site_currency }} @currency($item->amount)</td>

        </tr>
        @endforeach

      </tbody>
    </table>

     <!----  FOURTH LEVEL --->
     <table style="border-collapse: collapse;  width: 100%;">
            <tbody>
                <tr>
                    <td style="border:none; width: 50%;">
                        <h4><b> </b></h4>
                        <ul style="list-style-type: none; padding: 0; text-align: left;">
                            <li></li>

                        </ul>
                    </td>

                    <td class="text-center" style="border: none; width: 50%; ">
                        <ul style="list-style-type: none; padding: 0; text-align: right;">
                            <li>
                                <span class="text-muted me-3" style="font-size:15px;font-weight:600; display: inline;">{{$payment->model->name }} Amount Due:</span>
                                <span style="display: inline;">{{ $sitesettings->site_currency }}  @currency($payment->model->totalamount)</span>
                            </li>
                            <br>
                            <li>
                                <span class="text-muted me-3" style="font-size:15px;font-weight:600; display: inline;"> Amount Paid:</span>
                                <span style="display: inline;">{{ $sitesettings->site_currency }} @currency($payment->totalamount)</span>
                            </li>
                            <br>
                            <li>
                                <span class="text-muted me-3" style="font-size:15px;font-weight:600; display: inline;">Tax & Discounts :</span>
                                <span style="display: inline;">{{ $sitesettings->site_currency }} 0</span>
                            </li><br>
                           
                            <li>
                                <span class="me-3" style="font-size:17px;font-weight:700; display: inline;">Total Paid:</span>
                                <span class="text-error" style="font-size:17px;font-weight:700;color:green; display: inline;">{{ $sitesettings->site_currency }} @currency($payment->totalamount)</span>
                            </li>

                        </ul>
                    </td>
                </tr>
            </tbody>
        </table>

    <!------- FOOTER-->



    <!-- Footer -->
  </div>

</body>

</html>