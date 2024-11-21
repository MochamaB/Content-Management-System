@extends('layouts.client.client')

@section('content')
<div class="register-area" style="background-color: rgb(249, 249, 249);">
    <div class="container">
        <div class="col-md-6"></div>
        <div class="col-md-6">
            <div class="box-for overflow">
                <div class="col-md-12 col-xs-12 login-blocks" style="padding:25px">
                @if (session('status'))
                    <div class="alert alert-success text-center">
                        {{ session('status') }}
                    </div>
                    @endif
                    <h2>Verify your email</h2>
                     <!-- Session Status -->
                   
                    <p class="text-sm text-gray-600">
                        Account activation link has been sent to your email address
                        Please follow the link inside to continue. If you cant find the link then click resend below
                    </p>
                

                <!-- Resend Verification Email Form -->
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <div style="padding: 30px; display: flex; justify-content: center;">
                        <button type="submit" class="btn btn-primary">
                            Resend Verification Email
                        </button>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection