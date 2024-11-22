@extends('layouts.client.client')

@section('content')



<!-- Forgot Password -->
<div class="register-area" style="background-color: rgb(249, 249, 249);">
    <div class="container">

        <div class="col-md-6">

        </div>

        <div class="col-md-6">
            <div class="box-for overflow">
                <div class="col-md-12 col-xs-12 login-blocks" style="padding:25px">
                   <!-- Validation Messages -->
                   @include('auth.authmessages')
                   
                    <h2>Reset Password : </h2>
                    <p class="text-sm text-gray-600">
                        Enter the email address associated with your account to recover your password.
                    </p>

                    <!-- Session Status -->
                   
                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input id="email" class="form-control" type="email" name="email" :value="old('email')" required autofocus>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-default"> Reset My Password</button>
                        </div>
                    </form>
                    <br>
                </div>

            </div>
        </div>

    </div>
</div>




@endsection