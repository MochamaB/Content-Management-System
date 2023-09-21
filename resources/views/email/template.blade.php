@include('layouts.client.header')
<div class="register-area" style="background-color:#D3D3D3;">
    <div class="container">
        <div class="row">
            <div class="col-md-5">
            </div>
            <div class="col-md-2">
                <img src="{{ url('resources/uploads/images/'. ($sitesettings->company_logo ?? 'noimage.jpg')) }}" style="height: 120px; width: 200px; padding-bottom:20px ">
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
                        <h2> {{$data['subject'] ?? ''}} </h2>
                        <p>Dear {{$user->firstname ?? ''}} {{$user->lastname ?? ''}}</p>
                        <p>{{$data['message'] ?? ''}}</p>
                        @foreach ($data as $line => $content)
                        @if (!empty($content))
                        @if ($line === 'action')
                        <p>{{ $linkmessage ?? ''}}: <a href="{{url($content) }}">{{ __('Click here') }}</a></p>
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