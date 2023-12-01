<!DOCTYPE html>
<html lang="en">
<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
 
  <!--- Plugins for bootstrap table--------->
  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <link rel="stylesheet" href="{{ asset('resources/styles/admin/css/vertical-layout-light/style.css') }}">
  <link rel="stylesheet" href="{{ asset('resources/styles/admin/css/vertical-layout-light/mystyle.css') }}">

  <!-- endinject -->
</head>
<body>
<table class="table table-bordered">
                <tbody>
                    <!--- FIRST SECTION  HEADER------->
                    <tr style="height:45px;">
                        <td>
                            @if ($sitesettings)
                            <img src="{{ $sitesettings->getFirstMediaUrl('logo')}}" alt="Logo" style="height: 140px; width: 180px;">
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
                                    <h3 style="text-transform: uppercase;"> {{$invoice->invoice_type}} INVOICE</h3>
                                </li>
                                <li><b>INV#: {{$invoice->id}}-{{$invoice->referenceno}}</b></li>
                                <li style="color:red; font-weight:700;font-size:14px">TOTAL DUE</li>
                                <li style="color:red; font-weight:700;font-size:20px"> {{ $sitesettings->site_currency }} {{$invoice->totalamount}}</li>
                            </ul>
                        </td>
                    </tr>
                    <!------ SECOND SECTION DETAILS -->
                    <tr>
                        <td>
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
                                    <div style="background-color:green;font-size:17px" class="badge badge-opacity-warning"> PAID</div> <!------Status -->
                                    @elseif( $invoice->status == 'overpaid' )
                                    <div style="background-color:darkorange;font-size:17px" class="badge badge-opacity-warning"> OVER PAID</div>
                                    @elseif ( $invoice->status == 'partiallypaid' )
                                    <div style="background-color:blue;font-size:17px;font-weight:800" class="badge badge-opacity-sucess"> PARTIALLY PAID</div>
                                    @elseif ( $invoice->status == 'unpaid' )
                                    <div style="background-color:red;font-size:17px;font-weight:800" class="badge badge-opacity-warning;font-size:17px">UN-PAID </div>
                                    @endif
                            </ul>
                        </td>
                    </tr>
                </tbody>
            </table>
</body>