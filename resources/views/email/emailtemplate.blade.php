@include('layouts.client.header')
<div class="register-area" style="background-color:#D3D3D3;">
  <div class="container">
    <div class="row">
      <div class="col-md-5">
      </div>
      <div class="col-md-2">

      </div>
      <div class="col-md-5">
      </div>
    </div>


    <div class="row">
      <div class="col-md-2">
      </div>
      <div class="col-md-8">
        <div class="box-for overflow">
          <div class="col-md-12 col-xs-12 login-blocks" style="padding:30px;">
            <div class="row">
              <div class="col-md-4">
                <img src="{{ url('resources/uploads/images/'. ($sitesettings->company_logo ?? 'noimage.jpg')) }}" style="height: 120px; width: 200px; padding-bottom:20px ">
              </div>
              <div class="col-md-8">
                <h5 style="text-transform: uppercase;font-size:20px;"> {{$sitesettings->company_name ?? ""}}</h5>
                <address>
                  <b>EMAIL:</b> <a href="mailto:webmaster@example.com">{{$sitesettings->company_email ?? ""}}</a>.<br>
                  <b>PHONE NUMBER:</b> {{$sitesettings->company_telephone ?? ""}}  <b>| LOCATION:</b> {{$sitesettings->company_location ?? ""}}
              </div>
            </div>
            <hr>
            <h2> {{$subject ?? 'SUBJECT OF THE EMAIL'}} </h2>
            <h5>Dear {{$user->firstname ?? 'Firstname'}} {{$user->lastname ?? 'Lastname'}}</h5>
            <p>{{$greetings ?? 'Welcome message'}} {{$sitesettings->site_name ?? "The site name"}}</p>
            <p>{{ $message ?? 'Body of the email'}}</p>
            <p>{{ $linkmessage ?? 'Text explaining what the link does'}}</p>
            <div class="text-center">
              <a class="btn btn-default" href="{{ $url ?? ''}}" target="_blank" rel="noopener">{{$action ?? "Go to site"}}</a></td>
            </div>
            <p>{{ $footermessage ?? 'All the best'}}</p>
            <p>{{ $sitesettings->company_name ?? 'Company Name'}}</p>
            <hr>
            <p style="font-size:11px">Developed By Bridge Technologies</p>
          </div>
        </div>
      </div>
      <div class="col-md-2">
      </div>
    </div>
  </div>