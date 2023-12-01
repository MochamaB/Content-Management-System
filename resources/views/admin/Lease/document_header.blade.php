



<div class="row">
    <div class="col-md-4" style="text-align:left;">
        @if ($sitesettings)
        <img src="{{ $sitesettings->getFirstMediaUrl('logo')}}" alt="Logo" style="height: 140px; width: 180px;">
        @else
        <img src="url('resources/uploads/images/noimage.jpg')" alt="No Image">
        @endif
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
                <h3 style="text-transform: uppercase;"> {{$invoice->invoice_type}} INVOICE</h3>
            </li>
            <li><b>INV#: {{$invoice->id}}-{{$invoice->referenceno}}</b></li>
            <li style="color:red; font-weight:700;font-size:14px">TOTAL DUE</li>
            <li style="color:red; font-weight:700;font-size:20px"> {{ $sitesettings->site_currency }} {{$invoice->totalamount}}</li>
        </ul>

    </div>
</div>
<hr></br>