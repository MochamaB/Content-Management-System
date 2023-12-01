@extends('layouts.admin.admin')

@section('content')


<div class="row">
    <div class="col-md-9">
        <div class=" contwrapper" id="printMe">

            <table class="table">
                <tbody>
                    <!--- FIRST SECTION  HEADER------->
                    <tr>
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
                                <div style="background-color:green;font-size:17px" class="badge badge-opacity-warning"> PAID</div> <!------Status -->
                                @elseif( $invoice->status == 'overpaid' )
                                <div style="background-color:darkorange;font-size:17px" class="badge badge-opacity-warning"> OVER PAID</div>
                                @elseif ( $invoice->status == 'partiallypaid' )
                                <div style="background-color:blue;font-size:17px;font-weight:800" class="badge badge-opacity-sucess"> PARTIALLY PAID</div>
                                @elseif ( $invoice->status == 'unpaid' )
                                <div style="background-color:red;font-size:17px;font-weight:800" class="badge badge-opacity-warning;font-size:17px">PENDING </div>
                                @endif
                            </ul>
                        </td>
                    </tr>
                </tbody>
            </table>
            <!------- THIRD LEVEL INVOICE ITEMS -->
            <table class="table table-hover table-bordered" style="font-size:12px;border:1px solid black;">
                <thead>
                    <tr class="tableheading">

                        <th>No.</th>
                        <th class="text-center">Description </th>
                        <th class="text-center">Amount Due </th>
                        <th class="text-center">Amount Paid</th>
                        <th class="text-center">Total Due</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->invoiceItems as $key=> $item)
                    <tr style="height:35px;">
                        <td class="text-center">{{$key+1}}</td>
                        <td class="text-center">{{$item->charge_name}} </td>
                        <td class="text-center">{{$item->amount}} </td>
                        <td class="text-center"> </td>
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
                            <h4><b>Payment Method </b></h4>
                            <p>We Accept M-PESA, Cash</p>
                            <ul class="ml-0 px-3 list-unstyled">
                                <li>1. Go to the M-PESA Menu </li>
                                <li>2. Go to Lipa Na Mpesa </li>
                                <li>3. Select Paybill </li>
                                <li>4. Enter the business no. <span style="color:blue; font-weight:700;"></span></li>
                                <li>5. Enter the Account no. The Invoice Number <span style="color:blue; font-weight:700;">{{$invoice->id}}-{{$invoice->referenceno}}</span></li>
                                <li>6. Enter Total amount due. <span style="color:blue; font-weight:700;">{{ $sitesettings->site_currency }} {{$invoice->totalamount}} </span></li>
                                <li>7. Complete Transaction</li>
                            </ul>
                        </td>
                        <td class="">
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
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-header">
                <h4>ACTIONS</h4>
            </div>
            <div class="card-body">
                <a href="{{ url('invoice/'.$invoice->id.'/pdf') }}" class="btn btn-primary btn-lg text-white float-end" data-toggle="modal" data-target="#sendemail"><i class="ti-email"></i> Email</a>

                <a href="" onclick="printDiv('printMe')" class="btn btn-warning btn-lg text-white float-end"><i class="icon-printer" style="color:white"></i> Print</a>


            </div>
        </div>

    </div>

</div>
<script>
    function printDiv(divName) {
        var printContents = document.getElementById(divName).innerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;

        window.print();

        document.body.innerHTML = originalContents;

    }
</script>




@endsection