@include('layouts.client.header')
<div class="register-area" style="background-color:#D3D3D3;">
    <div class="container">
        <div class="row">
            <div class="col-md-5">
            </div>
            <div class="col-md-2">
                <!---1.  LOGO OF THE COMPANY --->
                @if ($sitesettings)
                              <img src="{{ $sitesettings->getFirstMediaUrl('logo')}}" alt="Logo" style="height: 140px; width: 180px;">
                              @else
                              <img src="url('resources/uploads/images/noimage.jpg')" alt="No Image">
                              @endif
            </div>
            <div class="col-md-5">
            </div>
        </div>


        <div class="row">
            <div class="col-md-2">
            </div>
            <div class="col-md-8">
                <div class="box-for overflow">
                    <div class="col-md-12 col-xs-12 login-blocks">
                    <!---2.  THE MAIN SUBJECT OF THE EMAIL --->
                        <h2> {{$heading ?? ''}} </h2>
                    
                    <!---3.  USER INFORMATION --->
                        <p>Dear {{$user->firstname ?? ''}} {{$user->lastname ?? ''}}</p>

                    <!---4.  EMAIL BODY --->
                        @foreach ($data as $line => $content)
                            @if (!empty($content))
                                @if ($line === 'action')
                                    <p>{{ $linkmessage ?? ''}}</p>
                                    <div class="text-center">
                                     <a href="{{url($content) }}" class="btn btn-default" >Go to Site</a>
                                    </div>
                                @else
                                    <p>{{ $content ?? '' }}</p>
                                @endif
                            @endif
                        @endforeach
                        <p>Kind Regards</p>
                        <p>{{$sitesettings->company_name ?? ""}}</p>

                    </div>
                </div>
            </div>
            <div class="col-md-2">
            </div>
        </div>
    </div>