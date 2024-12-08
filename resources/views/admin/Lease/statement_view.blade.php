<div class="col-md-12">
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
                              <h5 style="text-transform: uppercase;"> ACCOUNT STATEMENT</h5>
                         </li>
                         <li><b>BILLING ACCOUNT:</b> {{$invoice->name}}</li>

                    </ul>
               </div>
               <!------ SECOND SECTION DETAILS -->
               <div class="col-md-6">
                    <h4="text-muted"><b>STATEMENT TO</b></h4>
                         <ul class="ml-2 px-3 list-unstyled">
                              <li><b>PROPERTY:</b> {{$invoice->property->property_name ?? 'DELETED'}}</li>
                              <li><b>UNIT NUMBER:</b> {{$invoice->unit->unit_number ?? ''}}</li>
                              <li><b>NAME:</b> {{$invoice->model->firstname ?? ''}} {{$invoice->model->lastname ?? ''}}</li>
                              <li><b>EMAIL:</b> {{$invoice->model->email ?? ''}}</li>
                              <li><b>PHONE NO:</b> {{$invoice->model->phonenumber ?? ''}}</li>
                         </ul>
               </div>
               <div class="col-md-6">
                    <ul class="ml-2 px-3 list-unstyled" style="text-align: right;">
                         <li><b>STATEMENT DURATION:</b></li>
                         <li><i>(Last 6 Months)</i> </li>
                         <li></br></li>
                    </ul>
               </div>

               <!------- THIRD LEVEL INVOICE ITEMS -->
               <table class="table table-hover table-bordered custom-table" style="font-size:12px;border:1px solid black;">
                    <thead>
                         <tr style="height:35px;" class="tableheading">

                              <th>No.</th>
                              <th class="text-center">Date </th>
                              <th class="text-center">Description</th>
                              <th class="text-center">Bills</th>
                              <th class="text-center">Payments</th>
                              <th class="text-center">Balance</th>
                         </tr>
                    </thead>
                    <tbody>
                         <tr style="height:35px;">
                              <td class="text-center"> - </td>
                              <td class="text-center"> - </td>
                              <td class="text-center"> Opening Balance</td>
                              <td class="text-center">{{ $sitesettings->site_currency }}.</td>
                              <td class="text-center">{{ $sitesettings->site_currency }}.</td>
                              <td class="text-center">{{ $sitesettings->site_currency }}.@currency($openingBalance)</td>

                         </tr>
                         @php
                         $balance = $openingBalance;
                         $totalInvoices = 0;
                         $totalPayments = 0;
                         @endphp
                         @foreach($transactions as $key => $item)
                         <tr style="height:35px;">
                              <td class="text-center">{{$key +1}}</td>
                              <td class="text-center">{{\Carbon\Carbon::parse($item->created_at)->format('d M Y')}}</td>
                              <td class="text-center" style="text-transform: capitalize;">{{$item->charge_name}}</td>

                              @if($item->transactionable_type ==='App\Models\Invoice')
                              @php
                              $balance += $item->amount;
                              $totalInvoices += $item->amount;
                              @endphp
                              <td class="text-center">{{ $sitesettings->site_currency }}.@currency($item->amount)</td>
                              <td class="text-center"> - </td>
                              @elseif($item->transactionable_type ==='App\Models\Payment')
                              @php
                              $balance -= $item->amount;
                              $totalPayments += $item->amount;
                              @endphp
                              <td class="text-center"> - </td>
                              <td class="text-center">{{ $sitesettings->site_currency }}.@currency($item->amount)</td>
                              @endif
                              <td class="text-center">{{ $sitesettings->site_currency }}. @currency($balance)</td>
                         </tr>
                         @endforeach
                    </tbody>
                    <tfooter>
                         <tr style="height:35px;">
                              <td class="text-center"> </td>
                              <td class="text-center"> - </td>
                              <td class="text-center"> <b>TOTALS</b></td>
                              <td class="text-center"><b>{{ $sitesettings->site_currency }}. @currency($totalInvoices)</b></td>
                              <td class="text-center"><b>{{ $sitesettings->site_currency }}. @currency($totalPayments)</b></td>
                              <td class="text-center"><b>{{ $sitesettings->site_currency }}. @currency($balance)</b></td>

                         </tr>
                    </tfooter>
               </table></br>


          </div>
          <!------- FOURTH LEVEL PAYMENT DETAILS AND TOTALS-->
          <!------- FOOTER-->

     </div>
</div>