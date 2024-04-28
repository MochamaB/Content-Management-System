
<table class="table ">
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
                        <h3 style="text-transform: uppercase;"> {{class_basename($model)}}</h3>
                    </li>
                    <li><b>{{$model->referenceno}} - {{$model-id}}</b></li>
                    <li style="color:green; font-weight:700;font-size:14px">TOTAL PAID</li>
                    <li style="color:green; font-weight:700;font-size:20px"> {{ $sitesettings->site_currency }} @currency($payment->totalamount)</li>
                </ul>
            </td>
        </tr>
        <!------ SECOND SECTION DETAILS -->
        <tr>
            <td></br>
                <h4="text-muted"><b>PAYMENT TO</b></h4>
                    <ul class="ml-2 px-3 list-unstyled">
                        <li><b>PROPERTY:</b> {{$payment->property->property_name}}</li>
                        <li><b>UNIT NUMBER:</b> {{$payment->unit->unit_number ?? 'NONE'}}</li>
                        <li><b>NAME:</b> {{$payment->model->model->name}}
                         {{$payment->model->model->firstname}} {{$payment->model->model->lastname}}</li>
                        <li><b>EMAIL:</b> {{$payment->model->model->email}}</li>
                        <li><b>PHONE NO:</b> {{$payment->model->model->phonenumber}}</li>
                    </ul>
            </td>
            <td></td>
            <td class="text-right">
                <ul class="ml-2 px-3 list-unstyled">
                    <li><b>PAYMENT DATE:</b> {{\Carbon\Carbon::parse($payment->created_at)->format('d M Y')}}</li>

                    <li></br></li>

                </ul>
            </td>
        </tr>
    </tbody>
</table>