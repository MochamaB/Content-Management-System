@extends('layouts.client.client')

@section('content')
<div class="register-area" style="background-color: rgb(249, 249, 249);">
    <div class="container">

        <div class="col-md-6">

        </div>
        <div class="col-md-6">
            <div class="box-for overflow">
                <div class="col-md-12 col-xs-12 login-blocks">
                    <p class="text-sm text-gray-600">
                    <h2>Reset Password : </h2> <br />
                    'Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.
                    </p>
                </div><br/><br/><br/><br/><br/><br/>
                @if (session('status') == 'verification-link-sent')
                <div class="mb-4 font-medium text-sm text-green-600">
                    {{ __('A new verification link has been sent to the email address you provided during registration.') }}
                </div>
                @endif
                <form method="POST" action="{{ route('verification.send') }}">
                        @csrf

                        <div class="" style="padding: 30px;">
                            <button type="submit" class="btn btn-primary">
                                {{ __('Resend Verification Email') }}
                            </button>
                        
                    </form>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <button type="submit" class="btn btn-link text-center">
                            {{ __('Log Out') }}
                        </button>
                    </form>
                    </div>
            </div>


        </div>


    </div>

</div>
@endsection