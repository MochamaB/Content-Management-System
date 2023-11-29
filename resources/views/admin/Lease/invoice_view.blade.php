@extends('layouts.admin.admin')

@section('content')


<div class="row">
    <div class="col-md-9">
        <div class=" contwrapper">
            <div class="row">
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
                            <h2 style="text-transform: uppercase;"> INVOICE TYPE</h2>
                        </li>
                        <li>REFERENCE#: INV-NO,</li>
                        <li style="color:red; font-weight:700;font-size:14px">TOTAL DUE</li>
                        <li style="color:red; font-weight:700;font-size:20px"> {{ $sitesettings->site_currency }} AMOUNT DUE</li>
                    </ul>

                </div></br></br>
                <!----------  SECOND LEVEL ---------------->
                <div class="col-md-6">
                    <h4="text-muted"><b>BILL TO</b></h4>
                        <ul class="ml-2 px-3 list-unstyled">
                            <li><b>NAME:</b></li>
                            <li><b>PROPERTY:</b></li>
                            <li><b>UNIT NUMBER:</b></li>
                            <li><b>EMAIL:</b></li>
                            <li><b>PHONE NO:</b></li>
                        </ul>
                </div>
                <div class="col-md-6">
                    <ul class="ml-2 px-3 list-unstyled">
                        <li><b>INVOICE DATE:</b></li>
                        <li><b>DUE DATE:</b></li>
                        <li><b>STATUS</b></li>
                        <li>Status</li>
                    </ul>
                </div>

            </div>
            <!------- THIRD LEVEL -->
            <div class="table-responsive">
                <table class="table" id="table" data-toggle="table" data-side-pagination="server" data-click-to-select="true" class="table table-hover table-striped" style="font-size:12px">
                    <thead style="" class="sticky-header">
                        <tr class="tableheading">

                            <th>No.</th>
                            <th class="text-right">Description </th>
                            <th class="text-right">Amount Due </th>
                            <th class="text-right">Previous Balance</th>
                            <th class="text-right">Total Due</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-right align-top"></td>
                            <td class="text-right align-top"> </td>
                            <td class="text-right align-top"> </td>
                            <td class="text-right align-top"> </td>
                            <td class="text-right align-top"> </td>

                        </tr>
                    </tbody>
                </table>
            </div></br>
            <div class="row">
                <div class="col-md-5">
                </div>
                <div class="col-md-5">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td>Sub Total Amount</td>
                                <td class="text-right">{{ $sitesettings->site_currency }}:</td>
                            </tr>
                            <tr>
                                <td>Tax & Discounts</td>
                                <td class="text-right">{{ $sitesettings->site_currency }}: </td>
                            </tr>
                            <tr>
                                <td>Other Charges</td>
                                <td class="text-right">{{ $sitesettings->site_currency }}: </td>
                            </tr>
                            <tr>
                                <td class="text-bold-800" style="font-size:18px;font-weight:700">Total Due</td>

                                <td class="text-bold-800 text-right" style="font-size:18px;font-weight:700">{{ $sitesettings->site_currency }}: </td>

                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>


        </div>
    </div>

</div>





@endsection